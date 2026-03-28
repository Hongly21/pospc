<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Receipt; 
use Carbon\Carbon;

class ReportController extends Controller
{

    public function sales(Request $request)
    {
        $startDate  = $request->input('start_date', Carbon::today()->toDateString());
        $endDate    = $request->input('end_date', Carbon::today()->toDateString());
        $customerId = $request->input('customer_id');
        $statusFilter = $request->input('status');

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

        $totalOrders  = $query->clone()->count();
        $totalRevenue = $query->clone()->sum('TotalAmount');

        $orderIds = $query->clone()->pluck('OrderID');

        $totalPaidRaw = Receipt::whereIn('OrderID', $orderIds)->sum('PaidAmount');
        $totalChangeRaw = Receipt::whereIn('OrderID', $orderIds)->sum('ChangeAmount');

        $totalReceived = $totalPaidRaw - $totalChangeRaw;

        $totalDebt = max(0, $totalRevenue - $totalReceived);

        $orders = $query->paginate(15)->appends([
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
            'totalDebt'
        ));
    }



}
