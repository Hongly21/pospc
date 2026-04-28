<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\InventoryAdjustment;
use Illuminate\Support\Facades\Auth;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::where('status', 1)
            ->whereHas('inventory')
            ->with(['inventory', 'category']);

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

        $categories = \App\Models\Category::where('status', 1)->get();

        return view('inventory.index', compact('products', 'categories'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,ProductID',
            'action' => 'required|in:add,subtract',
            'quantity' => 'required|integer|min:1',
            'reason' => 'nullable|string'
        ]);

        $inventory = Inventory::firstOrCreate(
            ['ProductID' => $request->product_id],
        );


        if ($request->action === 'add') {
            $inventory->Quantity += $request->quantity;
        } else {
            if ($inventory->Quantity < $request->quantity) {
                return redirect()->back()->with('error', __('inventory.msg_cannot_subtract'));
            }
            $inventory->Quantity -= $request->quantity;
        }

        $inventory->save();


        InventoryAdjustment::create([
            'ProductID' => $request->product_id,
            'UserID' => Auth::id(),
            'Action' => $request->action,
            'Quantity' => $request->quantity,
            'Reason' => $request->reason,
        ]);

        return redirect()->back()->with('success', __('inventory.msg_stock_updated'));
    }

    //update only reorder
    public function updatereorder(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,ProductID',
            'reorder_level' => 'required|integer|min:0',
        ]);

        $inventory = Inventory::firstOrCreate(
            ['ProductID' => $request->product_id],
            ['ReorderLevel' => $request->reorder_level]
        );

        $inventory->ReorderLevel = $request->reorder_level;

        $inventory->save();

        return redirect()->back()->with('success', __('inventory.msg_stock_updated'));
    }

    public function history(Request $request)
    {
        $query = InventoryAdjustment::with(['product', 'user']);

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = \Carbon\Carbon::parse($request->start_date)->startOfDay();
            $endDate = \Carbon\Carbon::parse($request->end_date)->endOfDay();

            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('product', function ($productQuery) use ($search) {
                    $productQuery->where('Name', 'LIKE', "%{$search}%");
                })
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('Username', 'LIKE', "%{$search}%");
                    });
            });
        }

        $histories = $query->orderBy('created_at', 'desc')
            ->paginate(20)
            ->appends($request->query());

        return view('inventory.history', compact('histories'));
    }
}
