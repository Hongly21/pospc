<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;

class SupplierController extends Controller
{
    public function index(Request $request)
    {

        $query = Supplier::withCount('purchases')->orderBy('SupplierID', 'desc');
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('Name', 'LIKE', "%{$search}%")
                    ->orWhere('Contact', 'LIKE', "%{$search}%")
                    ->orWhere('Address', 'LIKE', "%{$search}%");
            });
        }
        $suppliers = $query->paginate(10);
        return view('suppliers.index', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'Name' => 'required|string|max:150',
            'Contact' => 'required|string|max:100',
            'Address' => 'nullable|string|max:255',
        ]);

        Supplier::create([
            'Name' => $request->Name,
            'Contact' => $request->Contact,
            'Address' => $request->Address,
        ]);

        return redirect()->route('suppliers.index')->with('success', 'បន្ថែមអ្នកផ្គត់ផ្គងជោគជ័យ!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'Name' => 'required|string|max:150',
            'Contact' => 'required|string|max:100',
            'Address' => 'nullable|string|max:255',
            'status' => 'required|boolean',
        ]);

        $supplier = Supplier::findOrFail($id);

        $updated = $supplier->update([
            'Name' => $request->Name,
            'Contact' => $request->Contact,
            'Address' => $request->Address,
            'status' => $request->status
        ]);

        if (!$updated) {
            return redirect()->back()->with('error', 'មានបញ្ហាក្នុងការកែប្រែទិន្នន័យ!');
        }

        return redirect()->route('suppliers.index')->with('success', 'កែប្រែអ្នកផ្គត់ផ្គងជោគជ័យ!');
    }

    public function destroy($id)
    {
        $supplier = Supplier::findOrFail($id);

        if ($supplier->purchases()->exists()) {
            return redirect()->back()->with('error', __('suppliers.msg_cannot_delete'));
        }

        $supplier->delete();

        return redirect()->back()->with('success', __('suppliers.msg_deleted'));
    }
}
