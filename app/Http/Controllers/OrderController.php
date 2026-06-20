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
use App\Models\Tax;
use App\Services\KhqrService;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{


    public function index(Request $request)
    {
        $taxes = Tax::where('Status', 1)->orderBy('TaxID', 'desc')->get();

        $query = Product::where('status', 1)
            ->whereHas('inventory', function ($q) {
                $q->where('Quantity', '>', 0);
            })->with(['inventory', 'category', 'attributes']);

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

        if ($request->ajax()) {
            return response()->json([
                'html' => view('pos.partials.product-grid', compact('products'))->render(),
            ]);
        }

        $categories = Category::where('status', 1)->get();
        $customers  = Customer::where('status', 1)->orderBy('CustomerID', 'desc')->get();

        foreach ($customers as $customer) {
            $unpaidOrders = Order::where('CustomerID', $customer->CustomerID)
                ->whereIn('Status', ['Partial', 'Unpaid'])
                ->get();
            $totalDebt = 0;
            foreach ($unpaidOrders as $order) {
                $paidAlready = Receipt::where('OrderID', $order->OrderID)->sum('PaidAmount');
                $totalDebt  += max(0, $order->TotalAmount - $paidAlready);
            }
            $customer->has_debt = $totalDebt > 0;
        }

        return view('pos.index', compact('products', 'customers', 'categories', 'taxes'));
    }

    public function generateKhqr(Request $request)
    {
        $request->validate([
            'amount'      => 'required|numeric|min:0.01',
            'currency'    => 'nullable|in:USD,KHR',
            'cart'        => 'required|array|min:1',
            'customer_id' => 'nullable|exists:customers,CustomerID',
            'tax_id'      => ['nullable', Rule::exists('taxes', 'TaxID')->where('Status', 1)],
        ]);

        $khqr   = new KhqrService();
        $result = $khqr->generateQr(
            (float) $request->amount,
            $request->currency ?? 'USD',
            'POS-' . date('YmdHis')
        );

        if ($result['success']) {
            session([
                'pending_qr_checkout' => [
                    'md5'         => $result['md5'],
                    'amount'      => (float) $request->amount,
                    'cart'        => $request->cart,
                    'customer_id' => $request->filled('customer_id') ? (int) $request->customer_id : null,
                    'tax_id'      => $request->filled('tax_id') ? (int) $request->tax_id : null,
                    'created_at'  => now()->timestamp,
                ],
            ]);

            return response()->json([
                'status' => 'success',
                'qr'     => $result['qr'],
                'md5'    => $result['md5'],
            ]);
        }

        return response()->json([
            'status'  => 'error',
            'message' => $result['message'],
        ], 500);
    }


    public function checkKhqrPayment(Request $request)
    {
        $request->validate(['md5' => 'required|string']);

        $khqr   = new KhqrService();
        $result = $khqr->checkPayment($request->md5);

        return response()->json($result);
    }


    public function store(Request $request)
    {
        if ($request->payment_type === 'QR') {
            $qrError = $this->prepareQrCheckout($request);
            if ($qrError !== null) {
                return $qrError;
            }
        }

        $request->validate([
            'payment_type'       => 'required|in:Cash,QR,Card',
            'cart'               => 'required|array|min:1',
            'customer_id'        => 'nullable|exists:customers,CustomerID',
            'tax_id'             => ['nullable', Rule::exists('taxes', 'TaxID')->where('Status', 1)],
            'paid_amount'        => 'nullable|numeric|min:0',
            'payment_confirmed'  => 'nullable|boolean',
        ]);

        $customerId = $request->filled('customer_id') ? (int) $request->customer_id : null;

        try {
            DB::beginTransaction();
            $calculatedTotal = 0;
            $totalTax = 0;
            $selectedTax = $request->filled('tax_id')
                ? Tax::where('Status', 1)->find($request->tax_id)
                : null;
            $taxRate = $selectedTax?->Rate ?? 0;
            foreach ($request->cart as $item) {
                $inventory = Inventory::where('ProductID', $item['id'])->lockForUpdate()->first();
                if (!$inventory || $inventory->Quantity < $item['qty']) {
                    DB::rollBack();
                    return response()->json([
                        'status'  => 'error',
                        'message' => __('pos.insufficient_stock', ['name' => $item['name'], 'qty' => $inventory->Quantity ?? 0]),
                    ]);
                }

                $product = Product::findOrFail($item['id']);
                $lineAmount = $product->SellPrice * $item['qty'];
                $taxAmount = round($lineAmount * ($taxRate / 100), 2);
                $calculatedTotal += round($lineAmount + $taxAmount, 2);
                $totalTax += $taxAmount;
            }

            $paidAmount = $request->payment_type === 'QR'
                ? $calculatedTotal
                : ($request->filled('paid_amount') ? $request->paid_amount : $calculatedTotal);

            $status = 'Unpaid';
            if ($paidAmount >= $calculatedTotal) {
                $status = 'Paid';
            } elseif ($paidAmount > 0 && $paidAmount < $calculatedTotal) {
                $status = 'Partial';
            }

            $now = Carbon::now()->setTimezone(config('app.timezone'));

            $userId = Auth::id();
            if (! $userId) {
                DB::rollBack();

                return response()->json([
                    'status'  => 'error',
                    'message' => __('pos.session_expired_relogin'),
                ], 401);
            }

            $order = Order::create([
                'UserID'      => $userId,
                'CustomerID'  => $customerId,
                'TotalAmount' => $calculatedTotal,
                'TotalTax'    => round($totalTax, 2),
                'Status'      => $status,
                'OrderDate'   => $now,
            ]);

            if ($paidAmount > 0) {
                $receiptTaxSnapshot = $this->resolveReceiptTaxSnapshot($selectedTax);

                Receipt::create([
                    'OrderID'       => $order->OrderID,
                    'TaxID'         => $receiptTaxSnapshot['TaxID'],
                    'TaxRate'       => $receiptTaxSnapshot['TaxRate'],
                    'TaxAmount'     => round($totalTax, 2),
                    'ReceiptNo'     => 'REC-' . strtoupper(uniqid()) . '-' . random_int(100, 999),
                    'PaymentMethod' => $request->payment_type,
                    'PaidAmount'    => $paidAmount,
                    'ChangeAmount'  => ($paidAmount > $calculatedTotal) ? ($paidAmount - $calculatedTotal) : 0,
                    'CreatedAt'     => $now,
                ]);
            }

            foreach ($request->cart as $item) {
                $product = Product::findOrFail($item['id']);
                $lineAmount = $product->SellPrice * $item['qty'];
                $taxAmount = round($lineAmount * ($taxRate / 100), 2);

                OrderDetail::create([
                    'OrderID'   => $order->OrderID,
                    'ProductID' => $item['id'],
                    'Quantity'  => $item['qty'],
                    'TaxRate'   => $taxRate,
                    'TaxAmount' => $taxAmount,
                    'Subtotal'  => round($lineAmount + $taxAmount, 2),
                ]);
                Inventory::where('ProductID', $item['id'])->decrement('Quantity', $item['qty']);
            }

            if ($customerId) {
                $points   = floor($calculatedTotal / 10);
                $customer = Customer::find($customerId);
                if ($customer) {
                    $customer->increment('Points', $points);
                }
            }

            DB::commit();

            if ($request->payment_type === 'QR') {
                session([
                    'pending_qr_checkout' => array_merge(
                        session('pending_qr_checkout', []),
                        ['order_id' => $order->OrderID]
                    ),
                ]);
            }

            return response()->json([
                'status'         => 'success',
                'message'        => __('pos.sale_complete'),
                'order_id'       => $order->OrderID,
                'payment_status' => $status,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('POS checkout failed', [
                'payment_type' => $request->payment_type,
                'user_id'      => Auth::id(),
                'error'        => $e->getMessage(),
            ]);

            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function showReceipt($id)
    {
        $order   = Order::with(['details.product.attributes', 'user', 'customer', 'receipts'])->findOrFail($id);

        if (!Auth::user()->hasAnyRole(['Admin', 'Manager']) && $order->UserID != Auth::id()) {
            abort(403);
        }

        $setting = Setting::latest()->first();
        return view('pos.receipt', compact('order', 'setting'));
    }

    public function history(Request $request)
    {
        $query = Order::with(['customer', 'user', 'receipts'])->orderBy('OrderID', 'desc');

        if (!Auth::user()->hasAnyRole(['Admin', 'Manager'])) {
            $query->where('UserID', Auth::id());
        }

        if ($request->filled('search')) {
            $search  = $request->search;
            $cleanId = ltrim(str_replace('#', '', $search), '0');
            $query->where(function ($q) use ($search, $cleanId) {
                $q->where('OrderID', 'LIKE', "%{$cleanId}%")
                    ->orWhereHas('customer', fn($cq) => $cq->where('Name', 'LIKE', "%{$search}%"));
            });
        }

        if ($request->has('status') && $request->status != '') {
            if ($request->status === 'Debt') {
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
                $totalDebt  += max(0, $order->TotalAmount - $paidAlready);
            }
            if ($totalDebt > 0) {
                return response()->json([
                    'has_debt'   => true,
                    'total_debt' => $totalDebt,
                    'message'    => __('pos.customer_debt_msg', ['amount' => number_format($totalDebt, 2)]),
                ]);
            }
        }

        return response()->json(['has_debt' => false, 'total_debt' => 0]);
    }

    public function payDebt(Request $request, $id)
    {
        $request->validate([
            'paid_amount'    => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:Cash,QR,Card',
        ]);

        try {
            DB::beginTransaction();

            $order       = Order::findOrFail($id);
            $alreadyPaid = Receipt::where('OrderID', $id)->sum('PaidAmount');
            $remaining   = max(0, $order->TotalAmount - $alreadyPaid);

            if ($remaining <= 0) {
                return response()->json(['status' => 'error', 'message' => __('pos.bill_already_paid')]);
            }

            $payAmount     = $request->paid_amount;
            $appliedAmount = min($payAmount, $remaining);
            $changeAmount  = max(0, $payAmount - $remaining);
            $latestReceipt = Receipt::where('OrderID', $order->OrderID)->orderBy('ReceiptID', 'desc')->first();
            $receiptTaxSnapshot = [
                'TaxID' => $latestReceipt?->TaxID,
                'TaxRate' => $latestReceipt?->TaxRate ?? 0,
                'TaxAmount' => $latestReceipt?->TaxAmount ?? 0,
            ];

            Receipt::create([
                'OrderID'       => $order->OrderID,
                'TaxID'         => $receiptTaxSnapshot['TaxID'],
                'TaxRate'       => $receiptTaxSnapshot['TaxRate'],
                'TaxAmount'     => $receiptTaxSnapshot['TaxAmount'],
                'ReceiptNo'     => 'REC-' . strtoupper(uniqid()) . '-' . random_int(100, 999),
                'PaymentMethod' => $request->payment_method,
                'PaidAmount'    => $payAmount,
                'ChangeAmount'  => $changeAmount,
                'CreatedAt'     => Carbon::now(),
            ]);

            $newTotalPaid   = $alreadyPaid + $appliedAmount;
            $order->Status  = $newTotalPaid >= $order->TotalAmount ? 'Paid' : 'Partial';
            $order->save();

            DB::commit();

            return response()->json([
                'status'     => 'success',
                'message'    => __('pos.debt_payment_success'),
                'new_status' => $order->Status,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    private function resolveReceiptTaxSnapshot(?Tax $tax): array
    {
        return [
            'TaxID' => $tax?->TaxID,
            'TaxRate' => $tax?->Rate ?? 0,
        ];
    }

    /**
     * Verify Bakong payment server-side and restore checkout payload from session.
     * Production proxies can strip nested form fields from the POST body, so the
     * session copy created during QR generation is the reliable source of truth.
     */
    private function prepareQrCheckout(Request $request): ?\Illuminate\Http\JsonResponse
    {
        if (! $request->boolean('payment_confirmed')) {
            return response()->json([
                'status'  => 'error',
                'message' => __('pos.qr_payment_not_confirmed'),
            ], 422);
        }

        $pending = session('pending_qr_checkout');
        if (! is_array($pending) || empty($pending['md5'])) {
            return response()->json([
                'status'  => 'error',
                'message' => __('pos.qr_session_expired'),
            ], 422);
        }

        if (! empty($pending['order_id'])) {
            return response()->json([
                'status'   => 'success',
                'message'  => __('pos.sale_complete'),
                'order_id' => $pending['order_id'],
            ]);
        }

        $khqr   = new KhqrService();
        $result = $khqr->checkPayment($pending['md5']);
        if (empty($result['paid'])) {
            return response()->json([
                'status'  => 'error',
                'message' => __('pos.qr_payment_not_confirmed'),
            ], 422);
        }

        $cart = $request->input('cart');
        if (! is_array($cart) || count($cart) === 0) {
            $request->merge(['cart' => $pending['cart'] ?? []]);
        }

        if (! $request->filled('customer_id') && ! empty($pending['customer_id'])) {
            $request->merge(['customer_id' => $pending['customer_id']]);
        }

        if (! $request->filled('tax_id') && ! empty($pending['tax_id'])) {
            $request->merge(['tax_id' => $pending['tax_id']]);
        }

        return null;
    }
}
