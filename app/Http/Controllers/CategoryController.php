<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::query();

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('Name', 'LIKE', "%{$search}%");
        }

        $categories = $query->orderBy('CategoryID', 'desc')->paginate(15);

        return view('categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'Name' => 'required|string|max:255',
        ]);

        Category::create([
            'Name' => $request->Name,
        ]);

        return redirect()->back()->with('success', 'បានបង្កើត Category ជោគជ័យ!');
    }

    // UPDATE (Edit Existing)
    public function update(Request $request, $id)
    {
        $request->validate([
            'Name' => 'required|string|max:255',
            'status' => 'required',
        ]);

        $category = Category::findOrFail($id);
        $category->update([
            'Name' => $request->Name,
            'status' => $request->status
        ]);

        return redirect()->back()->with('success', 'បានកែប្រែ Category ជោគជ័យ!');
    }

    // DELETE
    // public function destroy($id)
    // {
    //     Category::findOrFail($id)->delete();
    //     return redirect()->back()->with('success', 'ត្រូវបានលុបដោយជោគជ័យ!');
    // }

    public function destroy($id)
    {
        try {
            $category = \App\Models\Category::findOrFail($id);
            $category->delete();

            return redirect()->back()->with('success', 'ប្រភេទទំនិញត្រូវបានលុបដោយជោគជ័យ!');
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == "23000") {
                return redirect()->back()->with('error', 'មិនអាចលុបបានទេ! ប្រភេទទំនិញនេះកំពុងមានទំនិញនៅខាងក្នុង។ សូមប្តូរស្ថានភាពទៅជា "ផ្អាក" ជំនួសវិញ។');
            }
            return redirect()->back()->with('error', 'មានបញ្ហាក្នុងការលុបទិន្នន័យ។');
        }
    }
}
