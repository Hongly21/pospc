<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use App\Models\Inventory;
use App\Models\Expense; // កុំភ្លេច Model នេះ
use Carbon\Carbon;
use App\Models\Purchase;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $periodOptions = [
            1  => __('1 Month'),
            3  => __('3 Months'),
            6  => __('6 Months'),
            12 => __('1 Year'),
        ];

        $selectedPeriod = (int) $request->query('period', 1);
        if (! array_key_exists($selectedPeriod, $periodOptions)) {
            $selectedPeriod = 1;
        }

        $startDate = Carbon::now()->subMonths($selectedPeriod)->startOfDay();
        $rangeLabel = __('Last :period', ['period' => $periodOptions[$selectedPeriod]]);

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
        // Product Purchases / Stock Cost (Red)
        $totalPurchases = Purchase::whereDate('Date', '>=', $startDate)->sum('Total');

        $totalExpenses = Expense::whereDate('expense_date', '>=', $startDate)->sum('amount');
        $netProfit = $totalRevenue - $totalExpenses - $totalPurchases;

        $totalStock = Inventory::sum('Quantity');

        $lowStockItems = Product::where('status', 1)->whereHas('inventory', function ($q) {
            $q->whereColumn('Quantity', '<=', 'ReorderLevel');
        })->with('inventory')->get();

        $outOfStockItems = Product::where('status', 1)
            ->whereHas('inventory', function ($q) {
                $q->where('Quantity', '<=', 0);
            })->with('inventory')->get();

        // 5. USERS
        $totalUsers = User::count();

        // 6. SALES CHART FOR SELECTED RANGE
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

        // 7. TOP SELLING PRODUCTS
        $topSellingProducts = OrderDetail::select('orderdetails.ProductID', DB::raw('SUM(orderdetails.Quantity) as quantity_sold'), DB::raw('SUM(orderdetails.Subtotal) as total_sales'))
            ->with('product')
            ->join('orders', 'orders.OrderID', '=', 'orderdetails.OrderID')
            ->whereDate('orders.OrderDate', '>=', $startDate)
            ->groupBy('orderdetails.ProductID')
            ->orderByDesc('quantity_sold')
            ->take(5)
            ->get();

        // 8. RECENT TRANSACTIONS
        $recentTransactions = Order::with('customer')
            ->whereDate('OrderDate', '>=', $startDate)
            ->orderByDesc('OrderDate')
            ->take(5)
            ->get();

        // 9. PAYMENT METHOD BREAKDOWN
        $paymentCounts = Order::select('PaymentType', DB::raw('count(*) as total'))
            ->groupBy('PaymentType')
            ->pluck('total', 'PaymentType')
            ->toArray();


        $cashCount = $paymentCounts['Cash'] ?? 0;
        $cardCount = $paymentCounts['Card'] ?? 0;
        $qrCount   = $paymentCounts['QR'] ?? 0;
        if($netProfit<0){
            $netProfit=0;
        }

        return view('dashboard.index', compact(
            'selectedPeriod',
            'periodOptions',
            'rangeLabel',
            'totalPurchases',
            'totalRevenue',
            'totalDebt',
            'totalExpenses',
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
