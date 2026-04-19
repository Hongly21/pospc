@extends('layouts.app')

@section('title', __('expenses_list'))

@section('content')
    <div class="card shadow mb-4 border-0">
        <div class="card-body bg-light">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <form action="{{ route('expenses.index') }}" method="GET" class="row g-2">
                        <div class="col-md-5">
                            <input type="text" name="search" class="form-control"
                                placeholder="{{ __('search_expense_placeholder') }}" value="{{ request('search') }}">
                        </div>
                        <div class="col-md-4">
                            <input type="month" name="month" class="form-control" value="{{ request('month') }}">
                        </div>
                        <div class="col-md-3 d-flex gap-2">
                            <button type="submit" class="btn btn-outline-primary "><i class="fas fa-search"></i>
                                {{ __('search_btn') }}</button>
                            <a href="{{ route('expenses.index') }}" class="btn btn-outline-danger"><i
                                    class="fas fa-sync"></i></a>
                        </div>
                    </form>
                </div>
                <div class="col-md-4 text-end">
                    <div class="border border-danger rounded p-2 bg-white d-inline-block shadow-sm">
                        <span class="text-muted fw-bold small me-2">{{ __('total_expenses_label') }}:</span>
                        <span class="h4 text-danger fw-bold mb-0">${{ number_format($totalExpense, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- success message --}}
    @include('partials.alerts')

    <div class="card shadow border-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 fw-bold "><i class="fas fa-list me-2"></i> {{ __('expense_history') }}</h6>
            <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addExpenseModal">
                <i class="fas fa-plus me-1"></i> {{ __('record_new_expense') }}
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted">
                        <tr>
                            <th class="ps-3">{{ __('date') }}</th>
                            <th>{{ __('title') }}</th>
                            <th>{{ __('category') }}</th>
                            <th>{{ __('recorded_by') }}</th>
                            <th>{{ __('note') }}</th>
                            <th class="text-end fw-bold">{{ __('amount') }}</th>
                            <th class="text-center pe-3">{{ __('actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($expenses as $exp)
                            <tr>
                                <td class="ps-3">{{ \Carbon\Carbon::parse($exp->expense_date)->format('d-M-Y') }}</td>
                                <td class="fw-bold text-dark">{{ $exp->title }}</td>
                                <td><span class="badge bg-secondary">{{ __($exp->category) }}</span></td>
                                <td>{{ $exp->user->Username ?? 'Admin' }}</td>
                                <td class="text-muted small">{{ \Illuminate\Support\Str::limit($exp->note, 30) }}</td>
                                <td class="text-end fw-bold text-danger">-${{ number_format($exp->amount, 2) }}</td>
                                <td class="text-center pe-3">
                                    <button class="btn btn-sm btn-outline-warning mb-1 " data-bs-toggle="modal"
                                        data-bs-target="#editExpenseModal{{ $exp->id }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('expenses.destroy', $exp->id) }}" method="POST"
                                        class="d-inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-outline-danger btn-delete mb-1">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            {{-- Edit Modal --}}
                            <div class="modal fade" id="editExpenseModal{{ $exp->id }}" tabindex="-1"
                                aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content border-0 shadow">
                                        <div class="modal-header">
                                            <h5 class="modal-title fw-bold"><i class="fas fa-edit me-2 text-warning"></i>
                                                {{ __('edit_expense_info') }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('expenses.update', $exp->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">{{ __('title') }} <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" name="title" class="form-control"
                                                        value="{{ $exp->title }}" required>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-bold">{{ __('amount') }} ($) <span
                                                                class="text-danger">*</span></label>
                                                        <input type="number" step="0.01" name="amount"
                                                            class="form-control" value="{{ $exp->amount }}" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-bold">{{ __('date') }} <span
                                                                class="text-danger">*</span></label>
                                                        <input type="date" name="expense_date" class="form-control"
                                                            value="{{ $exp->expense_date }}" required>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">{{ __('category') }} <span
                                                            class="text-danger">*</span></label>
                                                    <select name="category" class="form-select" required>
                                                        <option value="Utilities"
                                                            {{ $exp->category == 'Utilities' ? 'selected' : '' }}>
                                                            {{ __('Utilities') }}</option>
                                                        <option value="Rent"
                                                            {{ $exp->category == 'Rent' ? 'selected' : '' }}>
                                                            {{ __('Rent') }}</option>
                                                        <option value="Payroll"
                                                            {{ $exp->category == 'Payroll' ? 'selected' : '' }}>
                                                            {{ __('Payroll') }}</option>
                                                        <option value="Supplies"
                                                            {{ $exp->category == 'Supplies' ? 'selected' : '' }}>
                                                            {{ __('Supplies') }}</option>
                                                        <option value="Others"
                                                            {{ $exp->category == 'Others' ? 'selected' : '' }}>
                                                            {{ __('Others') }}</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">{{ __('note') }}</label>
                                                    <textarea name="note" class="form-control" rows="2">{{ $exp->note }}</textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer bg-light">
                                                <button type="button" class="btn btn-outline-secondary btn-sm fw-bold"
                                                    data-bs-dismiss="modal">{{ __('cancel_btn') }}</button>
                                                <button type="submit"
                                                    class="btn btn-outline-warning fw-bold text-dark">{{ __('save_changes') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fas fa-folder-open fa-3x mb-3 text-light"></i>
                                    <h5>{{ __('no_expense_data') }}</h5>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-end p-3">
                {{ $expenses->links() }}
            </div>
        </div>
    </div>

    {{-- Add Modal --}}
    <div class="modal fade" id="addExpenseModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-0 shadow">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="fas fa-plus-circle me-2 text-primary"></i>
                        {{ __('record_new_expense') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('expenses.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('title') }} <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{ __('amount') }} ($) <span
                                        class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="amount" class="form-control"
                                    placeholder="0.00" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{ __('date') }} <span
                                        class="text-danger">*</span></label>
                                <input type="date" name="expense_date" class="form-control"
                                    value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('category') }} <span
                                    class="text-danger">*</span></label>
                            <select name="category" class="form-select" required>
                                <option value="Utilities">{{ __('Utilities') }}</option>
                                <option value="Rent">{{ __('Rent') }}</option>
                                <option value="Payroll">{{ __('Payroll') }}</option>
                                <option value="Supplies">{{ __('Supplies') }}</option>
                                <option value="Others">{{ __('Others') }}</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('note') }}</label>
                            <textarea name="note" class="form-control" rows="2" placeholder="{{ __('note_placeholder') }}"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-outline-secondary btn-sm fw-bold"
                            data-bs-dismiss="modal">{{ __('cancel_btn') }}</button>
                        <button type="submit"
                            class="btn btn-outline-primary btn-sm fw-bold">{{ __('save_user') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const deleteButtons = document.querySelectorAll('.btn-delete');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                const form = this.closest('form');
                Swal.fire({
                    title: "{{ __('confirm_delete') }}",
                    text: "{{ __('delete_warning') }}",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "{{ __('delete_btn_confirm') }}",
                    cancelButtonText: "{{ __('cancel_btn') }}"
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>
