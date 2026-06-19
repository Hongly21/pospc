<?php

namespace App\Http\Controllers;

use App\Models\Tax;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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

        $taxes = $query->orderBy('TaxID', 'desc')->paginate(10);

        return view('taxes.index', compact('taxes'));
    }


    public function store(Request $request)
    {
        // SECURITY: Only Admin/Manager can create taxes
        if (!$this->userHasAnyRole(['Admin', 'Manager'])) {
            Log::warning('unauthorized_tax_creation_attempt', ['model' => 'Tax']);
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

        Log::info('tax_created', ['model' => 'Tax', 'tax_id' => $tax->TaxID, 'name' => $tax->Name, 'rate' => $tax->Rate]);

        return redirect()->route('taxes.index')->with('success', __('taxes.msg_created'));
    }

    public function update(Request $request, $id)
    {
        // SECURITY: Only Admin/Manager can update taxes
        if (!$this->userHasAnyRole(['Admin', 'Manager'])) {
            Log::warning('unauthorized_tax_update_attempt', ['model' => 'Tax', 'tax_id' => $id]);
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

        Log::info('tax_updated', ['model' => 'Tax', 'tax_id' => $tax->TaxID, 'name' => $tax->Name, 'rate' => $tax->Rate]);

        return redirect()->back()->with('success', __('taxes.msg_updated'));
    }

    public function destroy($id)
    {
        // SECURITY: Only Admin can delete taxes
        if (!$this->userHasRole('Admin')) {
            Log::warning('unauthorized_tax_deletion_attempt', ['model' => 'Tax', 'tax_id' => $id]);
            abort(403, 'Unauthorized: Only admins can delete taxes');
        }

        $tax = Tax::findOrFail($id);

        $tax->delete();

        Log::info('tax_deleted', ['model' => 'Tax', 'tax_id' => $id, 'name' => $tax->Name]);

        return redirect()->back()->with('success', __('taxes.msg_deleted'));
    }

    private function userHasRole(string $roleName): bool
    {
        $user = Auth::user();

        return $user?->role?->RoleName !== null && strtolower($user->role->RoleName) === strtolower($roleName);
    }

    private function userHasAnyRole(array $roleNames): bool
    {
        foreach ($roleNames as $roleName) {
            if ($this->userHasRole($roleName)) {
                return true;
            }
        }

        return false;
    }
}
