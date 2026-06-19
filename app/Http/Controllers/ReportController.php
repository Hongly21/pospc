<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Receipt;
use App\Models\Expense; // Added to calculate expenses for the date range
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function sales(Request $request)
    {
        $startDate    = $request->input('start_date', Carbon::today()->toDateString());
        $endDate      = $request->input('end_date', Carbon::today()->toDateString());
        $customerId   = $request->input('customer_id');
        $statusFilter = $request->input('status');

        // 1. Build the Main Order Query
        $query = Order::with(['user', 'customer', 'receipts'])
            ->whereDate('OrderDate', '>=', $startDate)
            ->whereDate('OrderDate', '<=', $endDate)
            ->orderBy('OrderDate', 'desc');

        if ($customerId) {
            $query->where('CustomerID', $customerId);
        }

        if ($statusFilter) {
            if ($statusFilter == 'Debt') {
                $query->whereIn('Status', ['Partial', 'Unpaid']);
            } else {
                $query->where('Status', $statusFilter);
            }
        }

        // 2. Calculate Order Totals
        $totalOrders  = $query->clone()->count();
        $totalRevenue = $query->clone()->sum('TotalAmount');

        $orderIds = $query->clone()->pluck('OrderID');

        // 3. Calculate Received & Debt
        $totalPaidRaw   = Receipt::whereIn('OrderID', $orderIds)->sum('PaidAmount');
        $totalChangeRaw = Receipt::whereIn('OrderID', $orderIds)->sum('ChangeAmount');

        $totalReceived = $totalPaidRaw - $totalChangeRaw;
        $totalDebt     = max(0, $totalRevenue - $totalReceived);

        // --- NEW: PROFITABILITY CALCULATION FOR THIS REPORT ---

        // A. General Expenses within this date range
        $totalExpenses = Expense::whereDate('expense_date', '>=', $startDate)
                                ->whereDate('expense_date', '<=', $endDate)
                                ->sum('amount');

        // B. Cost of Goods Sold (COGS) strictly for the filtered orders
        $cogs = DB::table('orderdetails')
            ->join('products', 'orderdetails.ProductID', '=', 'products.ProductID')
            ->whereIn('orderdetails.OrderID', $orderIds)
            ->sum(DB::raw('orderdetails.Quantity * products.CostPrice'));

        // C. True Net Profit for the report
        $netProfit = $totalRevenue - $totalExpenses - $cogs;
        // -----------------------------------------------------

        $orders = $query->paginate(10)->appends([
            'start_date'  => $startDate,
            'end_date'    => $endDate,
            'customer_id' => $customerId,
            'status'      => $statusFilter
        ]);

        $customers = Customer::all();

        return view('reports.sales', compact(
            'orders',
            'customers',
            'startDate',
            'endDate',
            'customerId',
            'statusFilter',
            'totalRevenue',
            'totalOrders',
            'totalReceived',
            'totalDebt',
            'cogs',        // Passed to view
            'netProfit'    // Passed to view
        ));
    }

    public function deadStock(Request $request)
    {
        $months = max(1, (int) $request->input('months', 5));
        $cutoffDate = Carbon::now()->subMonths($months);

        $products = Product::with('inventory')
            ->whereHas('inventory', fn ($query) => $query->where('Quantity', '>', 0))
            ->where('created_at', '<', $cutoffDate)
            ->whereDoesntHave('orderDetails', function ($query) use ($cutoffDate) {
                $query->whereHas('order', function ($orderQuery) use ($cutoffDate) {
                    $orderQuery->whereDate('OrderDate', '>=', $cutoffDate);
                });
            })
            ->orderBy('Name')
            ->paginate(25)
            ->appends(['months' => $months]);

        $totalCapitalTiedUp = DB::table('products')
            ->join('inventory', 'products.ProductID', '=', 'inventory.ProductID')
            ->where('inventory.Quantity', '>', 0)
            ->where('products.created_at', '<', $cutoffDate)
            ->whereNotExists(function ($query) use ($cutoffDate) {
                $query->select(DB::raw(1))
                    ->from('orderdetails')
                    ->join('orders', 'orderdetails.OrderID', '=', 'orders.OrderID')
                    ->whereColumn('orderdetails.ProductID', 'products.ProductID')
                    ->whereDate('orders.OrderDate', '>=', $cutoffDate);
            })
            ->sum(DB::raw('products.CostPrice * inventory.Quantity'));

        $deadStockCount = DB::table('products')
            ->join('inventory', 'inventory.ProductID', '=', 'products.ProductID')
            ->where('inventory.Quantity', '>', 0)
            ->where('products.created_at', '<', $cutoffDate)
            ->whereNotExists(function ($query) use ($cutoffDate) {
                $query->select(DB::raw(1))
                    ->from('orderdetails')
                    ->join('orders', 'orderdetails.OrderID', '=', 'orders.OrderID')
                    ->whereColumn('orderdetails.ProductID', 'products.ProductID')
                    ->whereDate('orders.OrderDate', '>=', $cutoffDate);
            })
            ->count();

        return view('reports.dead_stock', compact(
            'products',
            'months',
            'cutoffDate',
            'totalCapitalTiedUp',
            'deadStockCount'
        ));
    }
}
