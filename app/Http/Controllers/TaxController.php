<?php

namespace App\Http\Controllers;

use App\Models\Tax;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Services\AuditLogger;

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
        // SECURITY: Only Admin/Manager can create taxes
        if (!Auth::user()->hasAnyRole(['Admin', 'Manager'])) {
            AuditLogger::log(
                'unauthorized_tax_creation_attempt',
                'Tax',
                [],
                'failed'
            );
            abort(403, 'Unauthorized: Only admins and managers can create taxes');
        }

        $request->merge([
            'Name' => preg_replace('/\s+/', ' ', trim((string) $request->input('Name'))),
        ]);

        $request->validate([
            'Name' => ['required', 'string', 'max:255', Rule::unique('taxes', 'Name')],
            'Rate' => 'required|numeric|min:0',
            'Description' => 'nullable|string|max:1000',
            'Status' => 'required|boolean',
        ]);

        $tax = Tax::create([
            'Name' => $request->Name,
            'Rate' => $request->Rate,
            'Description' => $request->Description,
            'Status' => $request->Status,
        ]);

        AuditLogger::log(
            'tax_created',
            'Tax',
            ['tax_id' => $tax->TaxID, 'name' => $tax->Name, 'rate' => $tax->Rate],
            'success'
        );

        return redirect()->route('taxes.index')->with('success', __('taxes.msg_created'));
    }

    public function update(Request $request, $id)
    {
        // SECURITY: Only Admin/Manager can update taxes
        if (!Auth::user()->hasAnyRole(['Admin', 'Manager'])) {
            AuditLogger::log(
                'unauthorized_tax_update_attempt',
                'Tax',
                ['tax_id' => $id],
                'failed'
            );
            abort(403, 'Unauthorized: Only admins and managers can update taxes');
        }

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

        AuditLogger::log(
            'tax_updated',
            'Tax',
            ['tax_id' => $tax->TaxID, 'name' => $tax->Name, 'rate' => $tax->Rate],
            'success'
        );

        return redirect()->back()->with('success', __('taxes.msg_updated'));
    }

    public function destroy($id)
    {
        // SECURITY: Only Admin can delete taxes
        if (!Auth::user()->hasRole('Admin')) {
            AuditLogger::log(
                'unauthorized_tax_deletion_attempt',
                'Tax',
                ['tax_id' => $id],
                'failed'
            );
            abort(403, 'Unauthorized: Only admins can delete taxes');
        }

        $tax = Tax::findOrFail($id);

        if ($tax->products()->exists() || $tax->categories()->exists()) {
            AuditLogger::log(
                'tax_deletion_prevented',
                'Tax',
                ['tax_id' => $id, 'reason' => 'has_dependencies'],
                'failed'
            );
            return redirect()->back()->with('error', __('taxes.msg_cannot_delete'));
        }

        $tax->delete();

        AuditLogger::log(
            'tax_deleted',
            'Tax',
            ['tax_id' => $id, 'name' => $tax->Name],
            'success'
        );

        return redirect()->back()->with('success', __('taxes.msg_deleted'));
    }
}
