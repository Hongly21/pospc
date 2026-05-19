<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Expense;
use App\Services\GroqService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChatbotController extends Controller
{
    protected GroqService $groq;

    public function __construct(GroqService $groq)
    {
        $this->groq = $groq;
    }

    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:500',
        ]);

        $userMessage = $request->input('message');
        $systemContext = $this->buildSystemContext();

        $reply = $this->groq->chat($userMessage, $systemContext);

        return response()->json(['reply' => $reply]);
    }

    /**
     * Build a system prompt with real-time POS data so Groq can answer accurately.
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

        // --- Debt orders with customer details ---
        $debtOrders = Order::whereIn('Status', ['Debt', 'Partial'])
            ->with(['customer', 'receipts'])
            ->get()
            ->map(function($order) {
                $paidAmount = $order->receipts->sum('PaidAmount');
                $remainingDebt = $order->TotalAmount - $paidAmount;
                return [
                    'customer' => $order->customer->Name ?? 'Unknown Customer',
                    'order_id' => $order->OrderID,
                    'total_amount' => $order->TotalAmount,
                    'paid_amount' => $paidAmount,
                    'remaining_debt' => $remainingDebt,
                    'order_date' => \Carbon\Carbon::parse($order->OrderDate)->format('Y-m-d'),
                ];
            })
            ->filter(fn($debt) => $debt['remaining_debt'] > 0)
            ->sortByDesc('remaining_debt')
            ->take(10); // Top 10 debts

        $debtSummary = $debtOrders->map(fn($d) => "{$d['customer']}: \${$d['remaining_debt']} (Order #{$d['order_id']})")->toArray();

        // --- Total debt ---
        $totalDebt = $debtOrders->sum('remaining_debt');

        // --- Customer count ---
        $customerCount = Customer::where('status', 1)->count();

        // --- Expense summaries ---
        $thisMonthExpenses = Expense::whereMonth('expense_date', now()->month)
            ->whereYear('expense_date', now()->year)
            ->sum('amount');

        $expenseCategories = Expense::whereMonth('expense_date', now()->month)
            ->whereYear('expense_date', now()->year)
            ->select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->orderByDesc('total')
            ->get()
            ->map(fn($e) => "{$e->category}: \${$e->total}")
            ->toArray();

        // --- Recent expenses (last 10) ---
        $recentExpenses = Expense::with('user')
            ->orderBy('expense_date', 'desc')
            ->limit(10)
            ->get()
            ->map(fn($e) => "{$e->title}: \${$e->amount} ({\Carbon\Carbon::parse($e->expense_date)->format('M d')})")
            ->toArray();

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
        $debtText = empty($debtSummary) ? 'No outstanding debts.' : implode("\n", $debtSummary);
        $expenseCategoriesText = empty($expenseCategories) ? 'No expenses this month.' : implode("\n", $expenseCategories);
        $recentExpensesText = empty($recentExpenses) ? 'No recent expenses.' : implode("\n", $recentExpenses);

        return <<<PROMPT
You are a helpful POS (Point of Sale) AI assistant for a retail shop. {$langInstruction}
Your job is to answer questions about products, stock, sales, debts, and expenses.
Keep answers concise and helpful. Use bullet points or short lists when appropriate.

INVENTORY QUERIES:
- For "Do we have X left?" or stock checks: Search the product list and give exact stock numbers
- For "What items are low on stock?": List products below reorder level
- For "What's out of stock?": List products with 0 quantity

SALES QUERIES:
- For "How much money did we make today/month?": Use the sales statistics provided
- For revenue questions: Calculate from order totals

DEBT QUERIES:
- For "Who owes us money?": List customers with outstanding debts
- For "How much debt do we have?": Sum of all outstanding amounts
- Include order numbers and amounts owed

EXPENSE QUERIES:
- For "What were our expenses this month?": Use expense statistics and categories
- For expense breakdowns: Show by category
- For recent expenses: List the most recent ones

GENERAL RULES:
- If a user asks about a product, search the product list below and give accurate stock/price info
- If you don't know something or the data isn't available, say so honestly
- Do NOT make up product names or stock numbers — only use the data provided below
- For calculations, use the provided statistics rather than trying to recalculate

=== SHOP STATISTICS ===
- Total active products: {$totalProducts}
- Total active customers: {$customerCount}
- Today's sales: \${$todaySales} ({$todayOrders} orders)
- This month's sales: \${$monthSales} ({$monthOrders} orders)
- This month's expenses: \${$thisMonthExpenses}
- Outstanding debt: \${$totalDebt}

=== OUTSTANDING DEBTS ===
{$debtText}

=== EXPENSE CATEGORIES (THIS MONTH) ===
{$expenseCategoriesText}

=== RECENT EXPENSES ===
{$recentExpensesText}

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
