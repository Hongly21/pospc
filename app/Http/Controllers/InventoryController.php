<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\InventoryAdjustment;
use App\Models\OrderDetail;
use App\Models\PurchaseDetail;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
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

        if ($request->filled('stock_status')) {
            $stockStatus = $request->stock_status;
            if (in_array($stockStatus, ['normal', 'low', 'out'], true)) {
                $query->whereHas('inventory', function ($inventoryQuery) use ($stockStatus) {
                    if ($stockStatus === 'low') {
                        $inventoryQuery->where('Quantity', '>', 0)
                            ->whereColumn('Quantity', '<=', 'ReorderLevel');
                    } elseif ($stockStatus === 'out') {
                        $inventoryQuery->where('Quantity', 0);
                    } elseif ($stockStatus === 'normal') {
                        $inventoryQuery->where(function ($normalQuery) {
                            $normalQuery->whereColumn('Quantity', '>', 'ReorderLevel')
                                ->orWhere(function ($fallbackQuery) {
                                    $fallbackQuery->whereNull('ReorderLevel')
                                        ->where('Quantity', '>', 0);
                                });
                        });
                    }
                });
            }
        }

        $products = $query->orderBy('Name')
            ->paginate(10)
            ->appends($request->query());

        $categories = \App\Models\Category::where('status', 1)->get();

        // Get all products for the search dropdown
        $allProducts = Product::where('status', 1)
            ->whereHas('inventory')
            ->with(['inventory', 'category'])
            ->orderBy('Name')
            ->get();

        return view('inventory.index', compact('products', 'categories', 'allProducts'));
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
        $startDate = $request->filled('start_date') ? \Carbon\Carbon::parse($request->start_date)->startOfDay() : null;
        $endDate = $request->filled('end_date') ? \Carbon\Carbon::parse($request->end_date)->endOfDay() : null;
        $search = $request->filled('search') ? trim($request->search) : null;

        $sales = OrderDetail::with(['product', 'order.user'])
            ->when($startDate, function ($query) use ($startDate) {
                $query->whereHas('order', function ($orderQuery) use ($startDate) {
                    $orderQuery->where('OrderDate', '>=', $startDate);
                });
            })
            ->when($endDate, function ($query) use ($endDate) {
                $query->whereHas('order', function ($orderQuery) use ($endDate) {
                    $orderQuery->where('OrderDate', '<=', $endDate);
                });
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($sub) use ($search) {
                    $sub->whereHas('product', function ($productQuery) use ($search) {
                        $productQuery->where('Name', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('order.user', function ($userQuery) use ($search) {
                        $userQuery->where('Username', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('order', function ($orderQuery) use ($search) {
                        $orderQuery->where('OrderID', 'LIKE', "%{$search}%");
                    });
                });
            })
            ->get()
            ->map(function ($detail) {
                return (object) [
                    'date' => $detail->order->OrderDate ?? now(),
                    'source' => 'Sale',
                    'actor' => $detail->order->user->Username ?? 'POS',
                    'product' => $detail->product->Name ?? 'Deleted Product',
                    'qty' => -abs($detail->Quantity),
                    'action' => 'Sale',
                    'note' => 'Order #' . $detail->OrderID,
                ];
            });

        $purchases = PurchaseDetail::with(['product', 'purchase.supplier'])
            ->when($startDate, function ($query) use ($startDate) {
                $query->whereHas('purchase', function ($purchaseQuery) use ($startDate) {
                    $purchaseQuery->where('Date', '>=', $startDate);
                });
            })
            ->when($endDate, function ($query) use ($endDate) {
                $query->whereHas('purchase', function ($purchaseQuery) use ($endDate) {
                    $purchaseQuery->where('Date', '<=', $endDate);
                });
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($sub) use ($search) {
                    $sub->whereHas('product', function ($productQuery) use ($search) {
                        $productQuery->where('Name', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('purchase.supplier', function ($supplierQuery) use ($search) {
                        $supplierQuery->where('Name', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('purchase', function ($purchaseQuery) use ($search) {
                        $purchaseQuery->where('PurchaseID', 'LIKE', "%{$search}%");
                    });
                });
            })
            ->get()
            ->map(function ($detail) {
                return (object) [
                    'date' => $detail->purchase->Date ?? now(),
                    'source' => 'Purchase',
                    'actor' => $detail->purchase->supplier->Name ?? 'Supplier',
                    'product' => $detail->product->Name ?? 'Deleted Product',
                    'qty' => abs($detail->Qty),
                    'action' => 'Purchase',
                    'note' => 'Purchase #' . $detail->PurchaseID,
                ];
            });

        $adjustments = InventoryAdjustment::with(['product', 'user'])
            ->when($startDate, function ($query) use ($startDate) {
                $query->where('created_at', '>=', $startDate);
            })
            ->when($endDate, function ($query) use ($endDate) {
                $query->where('created_at', '<=', $endDate);
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($sub) use ($search) {
                    $sub->whereHas('product', function ($productQuery) use ($search) {
                        $productQuery->where('Name', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('Username', 'LIKE', "%{$search}%");
                    })
                    ->orWhere('Reason', 'LIKE', "%{$search}%");
                });
            })
            ->get()
            ->map(function ($history) {
                return (object) [
                    'date' => $history->created_at,
                    'source' => 'Adjustment',
                    'actor' => $history->user->Username ?? 'Unknown',
                    'product' => $history->product->Name ?? 'Deleted Product',
                    'qty' => $history->Action === 'add' ? abs($history->Quantity) : -abs($history->Quantity),
                    'action' => $history->Action === 'add' ? 'Add' : 'Subtract',
                    'note' => $history->Reason ?: 'Manual adjustment',
                ];
            });


        $sales = collect($sales);
        $purchases = collect($purchases);
        $adjustments = collect($adjustments);

        $historyItems = $sales->merge($purchases)->merge($adjustments)
            ->sortByDesc('date')
            ->values();

        $page = Paginator::resolveCurrentPage('page');
        $perPage = 20;
        $histories = new LengthAwarePaginator(
            $historyItems->slice(($page - 1) * $perPage, $perPage)->values(),
            $historyItems->count(),
            $perPage,
            $page,
            [
                'path' => Paginator::resolveCurrentPath(),
                'query' => $request->query(),
            ]
        );

        return view('inventory.history', compact('histories'));
    }
}
