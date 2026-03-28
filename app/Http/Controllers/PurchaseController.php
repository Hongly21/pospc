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

class PurchaseController extends Controller
{
    public function index()
    {
        $purchases = Purchase::with('supplier')->orderBy('PurchaseID', 'desc')->get();
        return view('purchases.index', compact('purchases'));
    }


    public function create()
    {
        $suppliers = Supplier::where('status', 1)->get();

        $products = Product::where('status', 1)->with('inventory')->get();

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
            return redirect()->route('purchases.index')->with('success', 'បានបញ្ជាទិញដោយជោគជ័យ!');
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e->getMessage());
        }
    }

    public function show($id)
    {
        $purchase = Purchase::with(['supplier', 'details.product'])->findOrFail($id);

        return view('purchases.show', compact('purchase'));
    }
}
