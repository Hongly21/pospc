<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::withCount('products');

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('Name', 'LIKE', '%' . $request->search . '%');
        }

        $categories = $query->orderBy('CategoryID', 'desc')->paginate(15);

        return view('categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'Name' => 'required|string|max:255|unique:categories,Name',
        ]);

        Category::create([
            'Name' => $request->Name,
        ]);

        return redirect()->back()->with('success', __('categories.msg_created'));
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'Name' => 'required|string|max:255|unique:categories,Name,' . $id . ',CategoryID',
            'status' => 'required|boolean',
        ]);

        $category->update([
            'Name' => $request->Name,
            'status' => $request->status,
        ]);

        return redirect()->back()->with('success', __('categories.msg_updated'));
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);

        if ($category->products()->exists()) {
            return redirect()->back()->with('error', __('categories.msg_cannot_delete'));
        }

        $category->delete();

        return redirect()->back()->with('success', __('categories.msg_deleted'));
    }
}
