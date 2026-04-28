<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::with('user')->orderBy('expense_date', 'desc')->orderBy('id', 'desc');

        // ស្វែងរកតាមឈ្មោះចំណាយ
        if ($request->filled('search')) {
            $query->where('title', 'LIKE', '%' . $request->search . '%');
        }

        // Filter តាមខែ
        if ($request->filled('month')) {
            $query->whereMonth('expense_date', Carbon::parse($request->month)->month)
                ->whereYear('expense_date', Carbon::parse($request->month)->year);
        }

        $expenses = $query->paginate(15)->appends($request->query());
        $totalExpense = $query->sum('amount');

        return view('expenses.index', compact('expenses', 'totalExpense'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
            'category' => 'required|string',
        ]);

        Expense::create([
            'title' => $request->title,
            'amount' => $request->amount,
            'expense_date' => $request->expense_date,
            'category' => $request->category,
            'note' => $request->note,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('expenses.index')->with('success', __('expenses.msg_created'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
        ]);

        $expense = Expense::findOrFail($id);
        $expense->update($request->all());

        return redirect()->route('expenses.index')->with('success', __('expenses.msg_updated'));
    }

    public function destroy($id)
    {
        Expense::findOrFail($id)->delete();
        return redirect()->route('expenses.index')->with('success', __('expenses.msg_deleted'));
    }
}
