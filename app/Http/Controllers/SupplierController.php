<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;
use Illuminate\Validation\Rule;

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
        $request->merge([
            'Name' => trim((string) $request->input('Name')),
            'Contact' => trim((string) $request->input('Contact')),
        ]);

        $request->validate([
            'Name' => ['required', 'string', 'max:150', Rule::unique('suppliers', 'Name')],
            'Contact' => ['required', 'string', 'max:100', Rule::unique('suppliers', 'Contact')],
            'Address' => 'nullable|string|max:255',
        ], [
            'Name.unique' => 'Supplier name already exists.',
            'Contact.unique' => 'Supplier contact already exists.',
        ]);

        Supplier::create([
            'Name' => $request->Name,
            'Contact' => $request->Contact,
            'Address' => $request->Address,
        ]);

        return redirect()->route('suppliers.index')->with('success', __('suppliers.msg_created'));
    }

    public function update(Request $request, $id)
    {
        $request->merge([
            'Name' => trim((string) $request->input('Name')),
            'Contact' => trim((string) $request->input('Contact')),
        ]);

        $request->validate([
            'Name' => ['required', 'string', 'max:150', Rule::unique('suppliers', 'Name')->ignore($id, 'SupplierID')],
            'Contact' => ['required', 'string', 'max:100', Rule::unique('suppliers', 'Contact')->ignore($id, 'SupplierID')],
            'Address' => 'nullable|string|max:255',
            'status' => 'required|boolean',
        ], [
            'Name.unique' => 'Supplier name already exists.',
            'Contact.unique' => 'Supplier contact already exists.',
        ]);

        $supplier = Supplier::findOrFail($id);

        $updated = $supplier->update([
            'Name' => $request->Name,
            'Contact' => $request->Contact,
            'Address' => $request->Address,
            'status' => $request->status
        ]);

        if (!$updated) {
            return redirect()->back()->with('error', __('suppliers.msg_update_error'));
        }

        return redirect()->route('suppliers.index')->with('success', __('suppliers.msg_updated'));
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
