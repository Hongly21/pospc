<?php

namespace App\Http\Controllers;

use App\Models\Tax;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TaxController extends Controller
{
    public function index(Request $request)
    {
        $query = Tax::query();

        if ($request->has('status') && $request->status !== '') {
            $query->where('Status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('Name', 'LIKE', '%' . $request->search . '%');
        }

        $taxes = $query->orderBy('TaxID', 'desc')->paginate(15);

        return view('taxes.index', compact('taxes'));
    }


    public function store(Request $request)
    {
        $request->merge([
            'Name' => preg_replace('/\s+/', ' ', trim((string) $request->input('Name'))),
        ]);

        $request->validate([
            'Name' => ['required', 'string', 'max:255', Rule::unique('taxes', 'Name')],
            'Rate' => 'required|numeric|min:0',
            'Description' => 'nullable|string|max:1000',
            'Status' => 'required|boolean',
        ]);

        Tax::create([
            'Name' => $request->Name,
            'Rate' => $request->Rate,
            'Description' => $request->Description,
            'Status' => $request->Status,
        ]);

        return redirect()->route('taxes.index')->with('success', __('taxes.msg_created'));
    }

    public function update(Request $request, $id)
    {
        $tax = Tax::findOrFail($id);

        $request->merge([
            'Name' => preg_replace('/\s+/', ' ', trim((string) $request->input('Name'))),
        ]);

        $request->validate([
            'Name' => ['required', 'string', 'max:255', Rule::unique('taxes', 'Name')->ignore($tax->TaxID, 'TaxID')],
            'Rate' => 'required|numeric|min:0',
            'Description' => 'nullable|string|max:1000',
            'Status' => 'required|boolean',
        ]);

        $tax->update([
            'Name' => $request->Name,
            'Rate' => $request->Rate,
            'Description' => $request->Description,
            'Status' => $request->Status,
        ]);

        return redirect()->back()->with('success', __('taxes.msg_updated'));
    }

    public function destroy($id)
    {
        $tax = Tax::findOrFail($id);

        if ($tax->products()->exists() || $tax->categories()->exists()) {
            return redirect()->back()->with('error', __('taxes.msg_cannot_delete'));
        }

        $tax->delete();

        return redirect()->back()->with('success', __('taxes.msg_deleted'));
    }
}
