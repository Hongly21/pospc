<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;

class CustomerController extends Controller
{

    public function storeAjax(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'required|string|max:20|unique:customers,PhoneNumber'
        ]);

        $customer = new Customer();
        $customer->Name = $request->name;
        $customer->PhoneNumber = $request->phone;
        $customer->Points = 0;
        $customer->status = 1;
        $customer->save();

        return response()->json([
            'id' => $customer->CustomerID,
            'name' => $customer->Name,
            'phone' => $customer->PhoneNumber
        ]);
    }

    public function index(Request $request)
    {
        $query = Customer::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('Name', 'LIKE', "%{$search}%")
                    ->orWhere('PhoneNumber', 'LIKE', "%{$search}%");
            });
        }

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        if ($request->has('debt_status') && $request->debt_status != '') {
            if ($request->debt_status == 'Debt') {
                $query->whereIn('CustomerID', function ($q) {
                    $q->select('CustomerID')
                        ->from('orders')
                        ->whereIn('Status', ['Partial', 'Unpaid']);
                });
            } elseif ($request->debt_status == 'Paid') {
                $query->whereNotIn('CustomerID', function ($q) {
                    $q->select('CustomerID')
                        ->from('orders')
                        ->whereIn('Status', ['Partial', 'Unpaid']);
                });
            }
        }

        $customers = $query->orderBy('CustomerID', 'desc')->paginate(15);

        return view('customers.index', compact('customers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'phone' => 'required|unique:customers,PhoneNumber'
        ]);

        Customer::create([
            'Name' => $request->name,
            'PhoneNumber' => $request->phone,
            'Address' => $request->address ?? null,
            'status' => $request->status ?? 1,
            'Points' => 0
        ]);

        return redirect()->route('customers.index')->with('success', 'បានបន្ថែមអតិថិជនថ្មីដោយជោគជ័យ។');
    }


    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $request->validate([
            'name' => 'required',
            'phone' => 'required|unique:customers,PhoneNumber,' . $id . ',CustomerID'
        ]);

        $customer->Name = $request->name;
        $customer->PhoneNumber = $request->phone;
        $customer->Address = $request->address;

        if ($request->has('status')) {
            $customer->status = $request->status;
        }

        $customer->save();

        return redirect()->route('customers.index')->with('success', 'ព័ត៌មានអតិថិជនត្រូវបានកែប្រែជោគជ័យ។');
    }


    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);

        $customer->status = $customer->status == 1 ? 0 : 1;
        $customer->save();

        $statusText = $customer->status == 1 ? 'Active (ដំណើរការ)' : 'Inactive (ផ្អាក)';

        return redirect()->back()->with('success', 'ស្ថានភាពអតិថិជនត្រូវបានប្តូរទៅជា "' . $statusText . '" ដោយជោគជ័យ។');
    }
}
