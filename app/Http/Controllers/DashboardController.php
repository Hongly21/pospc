<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use App\Models\Inventory;
use App\Models\Expense;
use Carbon\Carbon;
use App\Models\Purchase;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $periodOptions = [
            1  => __('1_Month'),
            3  => __('3_Months'),
            6  => __('6_Months'),
            12 => __('1_Year'),
        ];

        $selectedPeriod = (int) $request->query('period', 1);
        if (! array_key_exists($selectedPeriod, $periodOptions)) {
            $selectedPeriod = 1;
        }

        $startDate = Carbon::now()->subMonths($selectedPeriod)->startOfDay();
        $rangeLabel = __(' :period', ['period' => $periodOptions[$selectedPeriod]]);

        // 1. SALES & REVENUE
        $totalRevenue = Order::whereDate('OrderDate', '>=', $startDate)->sum('TotalAmount');
        $totalOrders = Order::whereDate('OrderDate', '>=', $startDate)->count();
        $totalProducts = Product::count();
        $totalCategories = Category::count();

        // 2. TOTAL DEBT
        $unpaidOrders = Order::with('receipts')
            ->whereIn('Status', ['Partial', 'Unpaid'])
            ->whereDate('OrderDate', '>=', $startDate)
            ->get();
        $totalDebt = 0;
        foreach ($unpaidOrders as $order) {
            $paidAlready = $order->receipts->sum('PaidAmount') - $order->receipts->sum('ChangeAmount');
            $totalDebt += max(0, $order->TotalAmount - $paidAlready);
        }

        // 3. EXPENSES & PURCHASES
        // Total money sent to suppliers (Cash Outflow)
        $totalPurchases = Purchase::whereDate('Date', '>=', $startDate)->sum('Total');

        // General Shop Expenses (Electric, Rent, etc.)
        $totalExpenses = Expense::whereDate('expense_date', '>=', $startDate)->sum('amount');


        // 4. COST OF ITEMS SOLD (COGS) & TRUE PROFIT
        // Calculates the original cost of ONLY the items that were successfully sold in this period
        $cogs = DB::table('orderdetails')
            ->join('orders', 'orders.OrderID', '=', 'orderdetails.OrderID')
            ->join('products', 'products.ProductID', '=', 'orderdetails.ProductID')
            ->whereDate('orders.OrderDate', '>=', $startDate)
            ->sum(DB::raw('orderdetails.Quantity * products.CostPrice'));

        // TRUE PROFIT FORMULA: Revenue - General Expenses - Cost of Items Sold
        $netProfit = $totalRevenue - $totalExpenses - $cogs;

        // Optional: Ensure profit doesn't show as negative if you prefer (though real businesses can have negative profit!)
        if($netProfit < 0){
            $netProfit = 0;
        }


        // 5. INVENTORY ALERTS
        $totalStock = Inventory::sum('Quantity');

        $lowStockItems = Product::where('status', 1)->whereHas('inventory', function ($q) {
            $q->whereColumn('Quantity', '<=', 'ReorderLevel');
        })->with('inventory')->get();

        $outOfStockItems = Product::where('status', 1)
            ->whereHas('inventory', function ($q) {
                $q->where('Quantity', '<=', 0);
            })->with('inventory')->get();

        // 6. USERS
        $totalUsers = User::count();

        // 7. SALES CHART FOR SELECTED RANGE
        $chartDates = [];
        $chartSales = [];

        if ($selectedPeriod === 1) {
            for ($i = 29; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);
                $chartDates[] = $date->format('d M');
                $chartSales[] = Order::whereDate('OrderDate', $date)
                    ->whereDate('OrderDate', '>=', $startDate)
                    ->sum('TotalAmount');
            }
        } else {
            for ($i = $selectedPeriod - 1; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);
                $monthStart = $date->copy()->startOfMonth();
                $monthEnd = $date->copy()->endOfMonth();
                $chartDates[] = $date->format('M Y');
                $chartSales[] = Order::whereBetween('OrderDate', [$monthStart, $monthEnd])
                    ->sum('TotalAmount');
            }
        }

        // 8. TOP SELLING PRODUCTS
        $topSellingProducts = OrderDetail::select('orderdetails.ProductID', DB::raw('SUM(orderdetails.Quantity) as quantity_sold'), DB::raw('SUM(orderdetails.Subtotal) as total_sales'))
            ->with('product')
            ->join('orders', 'orders.OrderID', '=', 'orderdetails.OrderID')
            ->whereDate('orders.OrderDate', '>=', $startDate)
            ->groupBy('orderdetails.ProductID')
            ->orderByDesc('quantity_sold')
            ->take(5)
            ->get();

        // 9. RECENT TRANSACTIONS
        $recentTransactions = Order::with('customer')
            ->whereDate('OrderDate', '>=', $startDate)
            ->orderByDesc('OrderDate')
            ->take(5)
            ->get();

        // 10. PAYMENT METHOD BREAKDOWN (from receipts, not orders)
        $paymentCounts = DB::table('receipts')
            ->join('orders', 'orders.OrderID', '=', 'receipts.OrderID')
            ->whereDate('orders.OrderDate', '>=', $startDate)
            ->select('receipts.PaymentMethod', DB::raw('count(*) as total'))
            ->groupBy('receipts.PaymentMethod')
            ->pluck('total', 'PaymentMethod')
            ->toArray();

        $cashCount = $paymentCounts['Cash'] ?? 0;
        $cardCount = $paymentCounts['Card'] ?? 0;
        $qrCount   = $paymentCounts['QR'] ?? 0;

        return view('dashboard.index', compact(
            'selectedPeriod',
            'periodOptions',
            'rangeLabel',
            'totalPurchases',
            'totalRevenue',
            'totalDebt',
            'totalExpenses',
            'cogs',                 // Make sure to pass $cogs to the view!
            'netProfit',
            'totalStock',
            'totalUsers',
            'totalOrders',
            'totalProducts',
            'totalCategories',
            'topSellingProducts',
            'recentTransactions',
            'lowStockItems',
            'outOfStockItems',
            'chartDates',
            'chartSales',
            'cashCount',
            'cardCount',
            'qrCount'
        ));
    }
}
