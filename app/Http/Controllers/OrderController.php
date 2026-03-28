<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Inventory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Setting;
use App\Models\Customer;
use App\Models\Receipt;
use Carbon\Carbon;
use App\Models\Category;

class OrderController extends Controller
{

    public function index(Request $request)
    {
        $query = Product::where('status', 1)
            ->whereHas('inventory', function ($q) {
                $q->where('Quantity', '>', 0);
            })->with(['inventory', 'category']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('Name', 'LIKE', "%{$search}%")
                    ->orWhere('Barcode', 'LIKE', "%{$search}%")
                    ->orWhereHas('category', function ($catQ) use ($search) {
                        $catQ->where('Name', 'LIKE', "%{$search}%");
                    });
            });
        }

        if ($request->filled('CategoryID')) {
            $query->where('CategoryID', $request->CategoryID);
        }

        $products = $query->get();
        $categories = Category::where('status', 1)->get();
        $customers = Customer::where('status', 1)->orderBy('CustomerID', 'desc')->get();

        foreach ($customers as $customer) {
            $unpaidOrders = Order::where('CustomerID', $customer->CustomerID)
                ->whereIn('Status', ['Partial', 'Unpaid'])
                ->get();
            $totalDebt = 0;
            foreach ($unpaidOrders as $order) {
                $paidAlready = Receipt::where('OrderID', $order->OrderID)->sum('PaidAmount');
                $totalDebt += max(0, $order->TotalAmount - $paidAlready);
            }
            $customer->has_debt = $totalDebt > 0;
        }

        return view('pos.index', compact('products', 'customers', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'payment_type' => 'required|in:Cash,QR,Card',
            'cart'         => 'required|array',
            'customer_id'  => 'nullable|exists:customers,CustomerID',
            'paid_amount'  => 'nullable|numeric|min:0'
        ]);

        foreach ($request->cart as $item) {
            $inventory = Inventory::where('ProductID', $item['id'])->first();

            if (!$inventory || $inventory->Quantity < $item['qty']) {
                return response()->json([
                    'status' => 'error',
                    'message' => "សុំទោស! ទំនិញ '{$item['name']}' មិនមានស្តុកគ្រប់គ្រាន់ទេ (សល់ត្រឹម {$inventory->Quantity} ប៉ុណ្ណោះ)។"
                ]);
            }
        }

        try {
            DB::beginTransaction();
            $calculatedTotal = 0;
            foreach ($request->cart as $item) {
                $calculatedTotal += $item['price'] * $item['qty'];
            }

            $paidAmount = $request->filled('paid_amount') ? $request->paid_amount : $calculatedTotal;

            $status = 'Unpaid';
            if ($paidAmount >= $calculatedTotal) {
                $status = 'Paid';
            } elseif ($paidAmount > 0 && $paidAmount < $calculatedTotal) {
                $status = 'Partial';
            }

            $order = Order::create([
                'UserID'         => Auth::id() ?? 1,
                'CustomerID'     => $request->customer_id,
                'TotalAmount'    => $calculatedTotal,
                'PaymentType'    => $request->payment_type,
                'Status'         => $status,
                'OrderDate'      => Carbon::now(),
            ]);

            if ($paidAmount > 0) {
                Receipt::create([
                    'OrderID'       => $order->OrderID,
                    'ReceiptNo'     => 'REC-' . strtoupper(uniqid()),
                    'PaymentMethod' => $request->payment_type,
                    'PaidAmount'    => $paidAmount,
                    'ChangeAmount'  => ($paidAmount > $calculatedTotal) ? ($paidAmount - $calculatedTotal) : 0,
                    'CreatedAt'     => Carbon::now(),
                ]);
            }

            foreach ($request->cart as $item) {
                OrderDetail::create([
                    'OrderID'   => $order->OrderID,
                    'ProductID' => $item['id'],
                    'Quantity'  => $item['qty'],
                    'Subtotal'  => $item['price'] * $item['qty'],
                ]);

                Inventory::where('ProductID', $item['id'])->decrement('Quantity', $item['qty']);
            }

            if ($request->customer_id) {
                $points = floor($calculatedTotal / 10);
                $customer = Customer::find($request->customer_id);
                if ($customer) {
                    $customer->increment('Points', $points);
                }
            }

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'ការលក់ជោគជ័យ (Sale complete)!',
                'order_id' => $order->OrderID,
                'payment_status' => $status
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }


    public function showReceipt($id)
    {
        $order = Order::with(['details.product', 'user', 'customer', 'receipts'])->findOrFail($id);
        $setting = Setting::latest()->first();

        return view('pos.receipt', compact('order', 'setting'));
    }


    public function history(Request $request)
    {
        $query = Order::with(['customer', 'user', 'receipts'])->orderBy('OrderID', 'desc');


        if (Auth::user()->RoleID != 1) {
            $query->where('UserID', Auth::id());
        }


        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $cleanId = ltrim(str_replace('#', '', $search), '0');

                $q->where('OrderID', 'LIKE', "%{$cleanId}%")
                    ->orWhereHas('customer', function ($cQuery) use ($search) {
                        $cQuery->where('Name', 'LIKE', "%{$search}%");
                    });
            });
        }

        if ($request->has('status') && $request->status != '') {
            if ($request->status == 'Debt') {
                $query->whereIn('Status', ['Partial', 'Unpaid']);
            } else {
                $query->where('Status', $request->status);
            }
        }

        $orders = $query->paginate(15)->appends($request->query());

        return view('pos.history', compact('orders'));
    }

    public function checkCustomerDebt($customerId)
    {
        $unpaidOrders = Order::where('CustomerID', $customerId)
            ->whereIn('Status', ['Partial', 'Unpaid'])
            ->get();

        if ($unpaidOrders->count() > 0) {
            $totalDebt = 0;

            foreach ($unpaidOrders as $order) {
                $paidAlready = Receipt::where('OrderID', $order->OrderID)->sum('PaidAmount');

                $debtForThisOrder = max(0, $order->TotalAmount - $paidAlready);
                $totalDebt += $debtForThisOrder;
            }

            if ($totalDebt > 0) {
                return response()->json([
                    'has_debt' => true,
                    'total_debt' => $totalDebt,
                    'message' => 'អតិថិជននេះមានជំពាក់ប្រាក់សរុប $' . number_format($totalDebt, 2)
                ]);
            }
        }

        return response()->json([
            'has_debt' => false,
            'total_debt' => 0
        ]);
    }


    public function payDebt(Request $request, $id)
    {
        $request->validate([
            'paid_amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:Cash,QR,Card'
        ]);

        try {
            DB::beginTransaction();

            $order = Order::findOrFail($id);

            $alreadyPaid = Receipt::where('OrderID', $id)->sum('PaidAmount');
            $remainingDebt = max(0, $order->TotalAmount - $alreadyPaid);

            if ($remainingDebt <= 0) {
                return response()->json(['status' => 'error', 'message' => 'វិក្កយបត្រនេះត្រូវបានទូទាត់រួចរាល់ហើយ!']);
            }

            $payAmount = $request->paid_amount;

            $appliedAmount = ($payAmount > $remainingDebt) ? $remainingDebt : $payAmount;
            $changeAmount = ($payAmount > $remainingDebt) ? ($payAmount - $remainingDebt) : 0;

            Receipt::create([
                'OrderID'       => $order->OrderID,
                'ReceiptNo'     => 'REC-' . strtoupper(uniqid()),
                'PaymentMethod' => $request->payment_method,
                'PaidAmount'    => $payAmount,
                'ChangeAmount'  => $changeAmount,
                'CreatedAt'     => Carbon::now(),
            ]);

            $newTotalPaid = $alreadyPaid + $appliedAmount;
            if ($newTotalPaid >= $order->TotalAmount) {
                $order->Status = 'Paid';
            } else {
                $order->Status = 'Partial';
            }
            $order->save();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'ការទូទាត់ប្រាក់ជំពាក់ទទួលបានជោគជ័យ!',
                'new_status' => $order->Status
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
