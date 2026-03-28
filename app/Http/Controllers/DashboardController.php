<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Inventory;
use App\Models\Expense; // កុំភ្លេច Model នេះ
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        // 1. SALES & REVENUE
        $todaySales = Order::whereDate('OrderDate', $today)->sum('TotalAmount');
        $todayOrders = Order::whereDate('OrderDate', $today)->count();
        $totalRevenue = Order::sum('TotalAmount');

        // 2. TOTAL DEBT
        $unpaidOrders = Order::with('receipts')->whereIn('Status', ['Partial', 'Unpaid'])->get();
        $totalDebt = 0;
        foreach ($unpaidOrders as $order) {
            $paidAlready = $order->receipts->sum('PaidAmount') - $order->receipts->sum('ChangeAmount');
            $totalDebt += max(0, $order->TotalAmount - $paidAlready);
        }

        $totalExpenses = Expense::sum('amount');
        $netProfit = $totalRevenue - $totalExpenses;

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

        // 6. WEEKLY SALES CHART (Last 7 Days)
        $chartDates = [];
        $chartSales = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $chartDates[] = $date->format('D'); // Mon, Tue, etc.
            $chartSales[] = Order::whereDate('OrderDate', $date)->sum('TotalAmount');
        }

        // 7. PAYMENT METHOD BREAKDOWN
        $paymentCounts = Order::select('PaymentType', DB::raw('count(*) as total'))
            ->groupBy('PaymentType')
            ->pluck('total', 'PaymentType')
            ->toArray();

        $cashCount = $paymentCounts['Cash'] ?? 0;
        $cardCount = $paymentCounts['Card'] ?? 0;
        $qrCount   = $paymentCounts['QR'] ?? 0;

        return view('dashboard.index', compact(
            'todaySales',
            'todayOrders',
            'totalRevenue',
            'totalDebt',
            'totalExpenses',
            'netProfit',
            'totalStock',
            'totalUsers',
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
