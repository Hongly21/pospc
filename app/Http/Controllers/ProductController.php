<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Inventory;
use Illuminate\Support\Facades\File;
use Illuminate\Database\QueryException;

class ProductController extends Controller
{

    public function index(Request $request)
    {
        $query = Product::with(['category', 'inventory']);

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        if ($request->filled('CategoryID')) {
            $query->where('CategoryID', $request->CategoryID);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('Name', 'LIKE', "%{$search}%")
                    ->orWhere('Barcode', 'LIKE', "%{$search}%");
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
            'CostPrice' => 'required|numeric',
            'SellPrice' => 'required|numeric',
            'StockQuantity' => 'required|integer|min:0',
            'WarrantyMonths' => 'nullable|integer',
            'Image' => 'nullable|image|max:2048',
            'Barcode' => 'required|unique:products,Barcode',
        ]);

        $imagePath = null;
        if ($request->hasFile('Image')) {
            $file = $request->file('Image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('Uploads/products'), $filename);
            $imagePath = 'Uploads/products/' . $filename;
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
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('products.index')->with('success', 'ទំនិញបានបង្កើតដោយជោគជ័យ!');
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'Name' => 'required|string|max:255',
            'CategoryID' => 'required|exists:categories,CategoryID',
            'CostPrice' => 'required|numeric',
            'SellPrice' => 'required|numeric',
            'WarrantyMonths' => 'nullable|integer',
            'Image' => 'nullable|image|max:2048',
            'status' => 'required|boolean',
            'Barcode' => 'required|unique:products,Barcode,' . $id . ',ProductID',
        ]);

        $data = $request->except(['Image', 'StockQuantity']);

        if ($request->hasFile('Image')) {
            // if has image delete old image
            if ($product->Image && File::exists(public_path($product->Image))) {
                File::delete(public_path($product->Image));
            }

            $file = $request->file('Image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('Uploads/products'), $filename);
            $data['Image'] = 'Uploads/products/' . $filename;
        }

        $product->update($data);

        return redirect()->back()->with('success', 'បានកែប្រែ Product ដោយជោគជ័យ!');
    }

    // public function destroy($id)
    // {
    //     $product = Product::findOrFail($id);

    //     if ($product->Image && File::exists(public_path($product->Image))) {
    //         File::delete(public_path($product->Image));
    //     }

    //     $product->delete();
    //     return redirect()->back()->with('success', 'ទំនិញត្រូវបានលុបដោយជោគជ័យ!');
    // }
    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);

            if ($product->Image && File::exists(public_path($product->Image))) {
                File::delete(public_path($product->Image));
            }

            $product->delete();
            return redirect()->back()->with('success', 'ទំនិញត្រូវបានលុបដោយជោគជ័យ!');
        } catch (QueryException $e) {
            if ($e->getCode() == "23000") {
                return redirect()->back()->with('error', 'មិនអាចលុបបានទេ! ទំនិញនេះមានប្រវត្តិលក់រួចហើយ។ សូមប្តូរស្ថានភាពទៅជា "ផ្អាក" ជំនួសវិញ។');
            }
            return redirect()->back()->with('error', 'មានបញ្ហាក្នុងការលុបទិន្នន័យ។');
        }
    }
}
