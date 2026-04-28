<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Expense;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChatbotController extends Controller
{
    protected GeminiService $gemini;

    public function __construct(GeminiService $gemini)
    {
        $this->gemini = $gemini;
    }

    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:500',
        ]);

        $userMessage = $request->input('message');
        $systemContext = $this->buildSystemContext();

        $reply = $this->gemini->chat($userMessage, $systemContext);

        return response()->json(['reply' => $reply]);
    }

    /**
     * Build a system prompt with real-time POS data so Gemini can answer accurately.
     */
    private function buildSystemContext(): string
    {
        // --- Product & Stock Summary ---
        $totalProducts = Product::where('status', 1)->count();

        $outOfStock = Product::where('status', 1)
            ->whereHas('inventory', fn($q) => $q->where('Quantity', 0))
            ->with('inventory')
            ->get(['ProductID', 'Name', 'SellPrice'])
            ->map(fn($p) => $p->Name)
            ->toArray();

        $lowStock = Product::where('status', 1)
            ->whereHas('inventory', function ($q) {
                $q->where('Quantity', '>', 0)
                  ->whereColumn('Quantity', '<=', 'ReorderLevel');
            })
            ->with('inventory')
            ->get(['ProductID', 'Name', 'SellPrice'])
            ->map(fn($p) => "{$p->Name} (qty: {$p->inventory->Quantity})")
            ->toArray();

        // --- Product list with stock (limit to 100 most recent) ---
        $products = Product::where('status', 1)
            ->with(['inventory', 'category'])
            ->orderBy('ProductID', 'desc')
            ->limit(100)
            ->get()
            ->map(fn($p) => [
                'name' => $p->Name,
                'category' => $p->category->Name ?? 'N/A',
                'price' => '$' . number_format($p->SellPrice, 2),
                'cost' => '$' . number_format($p->CostPrice, 2),
                'stock' => $p->inventory->Quantity ?? 0,
                'barcode' => $p->Barcode ?? 'N/A',
                'brand' => $p->Brand ?? 'N/A',
                'attributes' => $p->attributes->isNotEmpty() ? $p->attributes->map(fn($a) => $a->AttributeName . ': ' . $a->AttributeValue)->implode(', ') : 'None',
            ])
            ->toArray();

        // --- Categories ---
        $categories = Category::where('status', 1)
            ->withCount('products')
            ->get()
            ->map(fn($c) => "{$c->Name} ({$c->products_count} products)")
            ->toArray();

        // --- Sales summary (today) ---
        $todaySales = Order::whereDate('OrderDate', today())->sum('TotalAmount');
        $todayOrders = Order::whereDate('OrderDate', today())->count();

        // --- Sales this month ---
        $monthSales = Order::whereMonth('OrderDate', now()->month)
            ->whereYear('OrderDate', now()->year)
            ->sum('TotalAmount');
        $monthOrders = Order::whereMonth('OrderDate', now()->month)
            ->whereYear('OrderDate', now()->year)
            ->count();

        // --- Debt orders ---
        $totalDebt = Order::where('Status', 'Debt')->sum(DB::raw('TotalAmount - COALESCE((SELECT SUM(PaidAmount) FROM receipts WHERE receipts.OrderID = orders.OrderID), 0)'));

        // --- Customer count ---
        $customerCount = Customer::where('status', 1)->count();

        // --- Top 5 best sellers this month ---
        $topSellers = DB::table('orderdetails')
            ->join('orders', 'orders.OrderID', '=', 'orderdetails.OrderID')
            ->join('products', 'products.ProductID', '=', 'orderdetails.ProductID')
            ->whereMonth('orders.OrderDate', now()->month)
            ->whereYear('orders.OrderDate', now()->year)
            ->select('products.Name', DB::raw('SUM(orderdetails.Quantity) as total_sold'))
            ->groupBy('products.Name')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get()
            ->map(fn($r) => "{$r->Name}: {$r->total_sold} sold")
            ->toArray();

        // --- Build the system prompt ---
        $locale = app()->getLocale();
        $langInstruction = $locale === 'kh'
            ? 'Respond in Khmer (ភាសាខ្មែរ) language.'
            : 'Respond in English.';

        $productListText = '';
        foreach ($products as $p) {
            $productListText .= "- {$p['name']} | Category: {$p['category']} | Price: {$p['price']} | Stock: {$p['stock']} | Brand: {$p['brand']} | Attributes: {$p['attributes']}\n";
        }

        $categoriesText = implode("\n", $categories);
        $outOfStockText = empty($outOfStock) ? 'None - all products in stock!' : implode(', ', $outOfStock);
        $lowStockText = empty($lowStock) ? 'None - stock levels are healthy!' : implode("\n", $lowStock);
        $topSellersText = empty($topSellers) ? 'No sales data this month yet.' : implode("\n", $topSellers);

        return <<<PROMPT
You are a helpful POS (Point of Sale) AI assistant for a retail shop. {$langInstruction}
Your job is to answer questions about products, stock, sales, and general shop info.
Keep answers concise and helpful. Use bullet points or short lists when appropriate.
If a user asks about a product, search the product list below and give accurate stock/price info.
If you don't know something or the data isn't available, say so honestly.
Do NOT make up product names or stock numbers — only use the data provided below.

=== SHOP STATISTICS ===
- Total active products: {$totalProducts}
- Total active customers: {$customerCount}
- Today's sales: \${$todaySales} ({$todayOrders} orders)
- This month's sales: \${$monthSales} ({$monthOrders} orders)
- Outstanding debt: \${$totalDebt}

=== CATEGORIES ===
{$categoriesText}

=== OUT OF STOCK PRODUCTS ===
{$outOfStockText}

=== LOW STOCK PRODUCTS ===
{$lowStockText}

=== TOP 5 BEST SELLERS (THIS MONTH) ===
{$topSellersText}

=== FULL PRODUCT LIST (name | category | price | stock | brand | attributes) ===
{$productListText}
PROMPT;
    }
}
