<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Inventory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PurchaseController extends Controller
{
    // public function index()
    // {
    //     $purchases = Purchase::with('supplier')->orderBy('PurchaseID', 'desc')
    //         ->paginate(15)
    //         ->appends(request()->query());

    //     return view('purchases.index', compact('purchases'));
    // }

    // public function index(Request $request)
    // {
    //     $query = Purchase::with('supplier');

    //     // 1. Search by Supplier Name, Total Amount, or Purchase ID
    //     if ($request->filled('search')) {
    //         $search = $request->search;
    //         $query->where(function ($q) use ($search) {
    //             // Search Total
    //             $q->where('Total', 'like', "%{$search}%")
    //               // Search Supplier Name
    //               ->orWhereHas('supplier', function ($qSupplier) use ($search) {
    //                   $qSupplier->where('Name', 'like', "%{$search}%");
    //               })
    //               // Search Purchase ID (Optional but helpful)
    //               ->orWhere('PurchaseID', 'like', "%{$search}%");
    //         });
    //     }

    //     // 2. Filter by Date Range
    //     if ($request->filled('start_date')) {
    //         $query->whereDate('Date', '>=', $request->start_date);
    //     }

    //     if ($request->filled('end_date')) {
    //         $query->whereDate('Date', '<=', $request->end_date);
    //     }

    //     // Execute query with pagination and append query strings to pagination links
    //     $purchases = $query->orderBy('PurchaseID', 'desc')
    //         ->paginate(15)
    //         ->appends(request()->query());

    //     return view('purchases.index', compact('purchases'));
    // }
    public function index(Request $request)
    {
        $query = Purchase::with('supplier');

        // 1. Search by Supplier Name, Total Amount, or Purchase ID
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                // Search Total
                $q->where('Total', 'like', "%{$search}%")
                  // Search Supplier Name
                  ->orWhereHas('supplier', function ($qSupplier) use ($search) {
                      $qSupplier->where('Name', 'like', "%{$search}%");
                  })
                  // Search Purchase ID
                  ->orWhere('PurchaseID', 'like', "%{$search}%");
            });
        }

        // 2. Filter by Date Range
        if ($request->filled('start_date')) {
            $query->whereDate('Date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('Date', '<=', $request->end_date);
        }

        // 3. Calculate total spent for the CURRENT filter (before pagination)
        $totalSpent = $query->sum('Total');

        // Execute query with pagination
        $purchases = $query->orderBy('PurchaseID', 'desc')
            ->paginate(10)
            ->appends(request()->query());

        // Pass $totalSpent to the view as well
        return view('purchases.index', compact('purchases', 'totalSpent'));
    }


    public function create()
    {
        $suppliers = Supplier::where('status', 1)->get();

        // $products = Product::where('status', 1)->with('inventory')->get();
        $products= Product::where('Status', 1)->with('inventory')->get();

        return view('purchases.create', compact('suppliers', 'products'));
    }
    /**
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        $request->validate([
            'SupplierID' => 'required|exists:suppliers,SupplierID',
            'PurchaseDate' => 'required|date',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,ProductID',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.cost' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $totalAmount = 0;
            foreach ($request->items as $item) {
                $totalAmount += $item['quantity'] * $item['cost'];
            }

            $purchase = Purchase::create([
                'SupplierID' => $request->SupplierID,
                'Date' => $request->PurchaseDate,
                'Total' => $totalAmount,
            ]);

            foreach ($request->items as $item) {

                PurchaseDetail::create([
                    'PurchaseID' => $purchase->PurchaseID,
                    'ProductID' => $item['product_id'],

                    'Qty' => $item['quantity'],
                    'CostPrice' => $item['cost'],


                ]);

                $inventory = Inventory::firstOrCreate(
                    ['ProductID' => $item['product_id']],
                    ['Quantity' => 0, 'ReorderLevel' => 5]
                );

                $inventory->increment('Quantity', $item['quantity']);
            }

            DB::commit();
            return redirect()->route('purchases.index')->with('success', __('purchases.msg_created'));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Purchase creation failed', [
                'message' => $e->getMessage(),
                'user_id' => Auth::id(),
            ]);

            return back()->with('error', __('Something went wrong while saving the purchase.'))->withInput();
        }
    }

    public function show($id)
    {
        $purchase = Purchase::with(['supplier', 'details.product'])->findOrFail($id);

        return view('purchases.show', compact('purchase'));
    }
}
