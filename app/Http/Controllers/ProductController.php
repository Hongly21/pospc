<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Inventory;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'inventory'])->withCount('orderDetails');

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        if ($request->filled('CategoryID')) {
            $query->where('CategoryID', $request->CategoryID);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('Name', 'LIKE', '%' . $search . '%')
                    ->orWhere('Barcode', 'LIKE', '%' . $search . '%');
            });
        }

        $products = $query->orderBy('ProductID', 'desc')->paginate(15);
        $categories = Category::where('status', 1)->get();

        return view('products.index', compact('products', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'Name' => 'required|string|max:255',
            'CategoryID' => 'required|exists:categories,CategoryID',
            'CostPrice' => 'required|numeric|min:0',
            'SellPrice' => 'required|numeric|min:0',
            'StockQuantity' => 'required|integer|min:0',
            'WarrantyMonths' => 'nullable|integer|min:0',
            'Image' => 'nullable|image|max:2048',
            'Barcode' => 'required|string|max:100|unique:products,Barcode',
        ]);

        $imagePath = null;
        if ($request->hasFile('Image')) {
            $file = $request->file('Image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $imagePath = $file->storeAs('products', $filename, 'public');
        }

        $product = Product::create([
            'Name' => $request->Name,
            'CategoryID' => $request->CategoryID,
            'Brand' => $request->Brand,
            'Model' => $request->Model,
            'CostPrice' => $request->CostPrice,
            'SellPrice' => $request->SellPrice,
            'WarrantyMonths' => $request->WarrantyMonths ?? 0,
            'Barcode' => $request->Barcode,
            'Description' => $request->Description,
            'Image' => $imagePath,
        ]);

        Inventory::create([
            'ProductID' => $product->ProductID,
            'Quantity' => $request->StockQuantity,
            'ReorderLevel' => 5,
        ]);

        return redirect()->route('products.index')->with('success', __('products.msg_created'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'Name' => 'required|string|max:255',
            'CategoryID' => 'required|exists:categories,CategoryID',
            'CostPrice' => 'required|numeric|min:0',
            'SellPrice' => 'required|numeric|min:0',
            'WarrantyMonths' => 'nullable|integer|min:0',
            'Image' => 'nullable|image|max:2048',
            'status' => 'required|boolean',
            'Barcode' => 'required|string|max:100|unique:products,Barcode,' . $id . ',ProductID',
        ]);

        $data = $request->except(['Image', 'StockQuantity']);

        if ($request->hasFile('Image')) {
            if ($product->Image && Storage::disk('public')->exists($product->Image)) {
                Storage::disk('public')->delete($product->Image);
            }

            $file = $request->file('Image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $data['Image'] = $file->storeAs('products', $filename, 'public');
        }

        $product->update($data);

        return redirect()->back()->with('success', __('products.msg_updated'));
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        if ($product->orderDetails()->exists()) {
            return redirect()->back()->with('error', __('products.msg_cannot_delete'));
        }

        if ($product->Image && Storage::disk('public')->exists($product->Image)) {
            Storage::disk('public')->delete($product->Image);
        }

        $product->inventory()->delete();
        $product->delete();

        return redirect()->back()->with('success', __('products.msg_deleted'));
    }
}
