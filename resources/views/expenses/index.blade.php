@extends('layouts.app')

@section('title', __('expenses_list'))

@section('content')
    @include('partials.alerts')

    <div class="card border-0 shadow-sm rounded-3">
        {{-- Card Header --}}
        <div class="card-header bg-white border-bottom pt-3 pb-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-list text-primary me-2"></i>{{ __('expense_history') }}</h5>
            <button type="button" class="btn btn-primary btn-sm fw-medium px-3 shadow-sm" data-bs-toggle="modal"
                data-bs-target="#addExpenseModal">
                <i class="fas fa-plus me-1"></i> {{ __('record_new_expense') }}
            </button>
        </div>

        {{-- Card Body --}}
        <div class="card-body bg-light rounded-bottom">
            {{-- Filter and Summary Bar --}}
            <div class="row mb-4 mx-0 bg-white p-2 rounded shadow-sm align-items-center">
                <div class="col-12 col-lg-8">
                    <form action="{{ route('expenses.index') }}" method="GET" class="row g-2 align-items-center">
                        <div class="col-12 col-md-5">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-search"></i></span>
                                <input type="text" name="search" class="form-control border-start-0 bg-light"
                                    placeholder="{{ __('search_expense_placeholder') }}" value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <input type="month" name="month" class="form-control form-control-sm bg-light" value="{{ request('month') }}">
                        </div>
                        <div class="col-12 col-md-3 d-flex gap-2">
                            <button type="submit" class="btn btn-sm btn-primary px-4 w-100">{{ __('search_btn') }}</button>
                            @if (request()->has('search') || request()->has('month'))
                                <a href="{{ route('expenses.index') }}" class="btn btn-sm btn-outline-secondary px-3">
                                    <i class="fas fa-sync-alt"></i>
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
                <div class="col-12 col-lg-4 mt-2 mt-lg-0 text-lg-end">
                    <div class="bg-danger bg-opacity-10 border border-danger-subtle rounded p-2 d-inline-block">
                        <span class="text-danger fw-bold small me-2">{{ __('total_expenses_label') }}:</span>
                        <span class="h5 text-danger fw-bold mb-0">${{ number_format($totalExpense, 2) }}</span>
                    </div>
                </div>
            </div>

            {{-- Expenses Table --}}
            <div class="table-responsive bg-white rounded shadow-sm border border-light-subtle">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-secondary small text-uppercase">
                        <tr>
                            <th class="ps-3 py-3">{{ __('date') }}</th>
                            <th class="py-3">{{ __('title') }}</th>
                            <th class="py-3">{{ __('category') }}</th>
                            <th class="py-3">{{ __('recorded_by') }}</th>
                            <th class="py-3">{{ __('note') }}</th>
                            <th class="text-end fw-bold py-3">{{ __('amount') }}</th>
                            <th class="text-end pe-3 py-3">{{ __('actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse ($expenses as $exp)
                            <tr>
                                <td class="ps-3 text-muted fw-medium">{{ \Carbon\Carbon::parse($exp->expense_date)->format('d-M-Y') }}</td>
                                <td class="fw-bold text-dark">{{ $exp->title }}</td>
                                <td><span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle px-2 py-1">{{ __($exp->category) }}</span></td>
                                <td>{{ $exp->user->Username ?? 'Admin' }}</td>
                                <td class="text-muted small">{{ \Illuminate\Support\Str::limit($exp->note, 30) }}</td>
                                <td class="text-end fw-bold text-danger">-${{ number_format($exp->amount, 2) }}</td>
                                <td class="text-end pe-3">
                                    <div class="btn-group shadow-sm">
                                        {{-- Data attributes added here to pass row values securely to the single modal --}}
                                        <button type="button" class="btn btn-sm btn-light text-warning border"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editExpenseModal"
                                            data-id="{{ $exp->id }}"
                                            data-title="{{ $exp->title }}"
                                            data-amount="{{ $exp->amount }}"
                                            data-date="{{ $exp->expense_date }}"
                                            data-category="{{ $exp->category }}"
                                            data-note="{{ $exp->note }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('expenses.destroy', $exp->id) }}" method="POST"
                                            class="d-inline delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-light text-danger border btn-delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted bg-white">
                                    <div class="d-flex flex-column align-items-center justify-content-center py-4">
                                        <i class="fas fa-list fa-3x mb-3 text-secondary opacity-50"></i>
                                        <h5 class="fw-medium text-dark">{{ __('no_expense_data') }}</h5>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination Links --}}
            <div class="mt-4">
                @if(method_exists($expenses, 'links'))
                    <div class="d-flex justify-content-start mt-3">
                        {{ $expenses->appends(request()->query())->links() }}
                    </div>
                @endif
             </div>
        </div>
    </div>

    {{-- Single Add Expense Modal --}}
    <div class="modal fade" id="addExpenseModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light border-bottom-0">
                    <h5 class="modal-title fw-bold text-dark"><i class="fas fa-plus-circle text-primary me-2"></i>
                        {{ __('record_new_expense') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('expenses.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label small fw-bold text-muted">{{ __('title') }} <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control" required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-bold text-muted">{{ __('amount') }} ($) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="amount" class="form-control" placeholder="0.00" required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-bold text-muted">{{ __('date') }} <span class="text-danger">*</span></label>
                                <input type="date" name="expense_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold text-muted">{{ __('category') }} <span class="text-danger">*</span></label>
                                <select name="category" class="form-select" required>
                                    <option value="Utilities">{{ __('Utilities') }}</option>
                                    <option value="Rent">{{ __('Rent') }}</option>
                                    <option value="Payroll">{{ __('Payroll') }}</option>
                                    <option value="Supplies">{{ __('Supplies') }}</option>
                                    <option value="Others">{{ __('Others') }}</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold text-muted">{{ __('note') }}</label>
                                <textarea name="note" class="form-control" rows="2" placeholder="{{ __('note_placeholder') }}"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-top-0">
                        <button type="button" class="btn btn-outline-secondary fw-bold px-4" data-bs-dismiss="modal">{{ __('cancel_btn') }}</button>
                        <button type="submit" class="btn btn-primary fw-bold px-4">{{ __('save_changes') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Single Edit Expense Modal (Now Cleanly Outside Table Loop) --}}
    <div class="modal fade" id="editExpenseModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light border-bottom-0">
                    <h5 class="modal-title fw-bold text-dark"><i class="fas fa-edit text-primary me-2"></i>
                        {{ __('edit_expense_info') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                {{-- Form action is empty by default; set dynamically by JavaScript below --}}
                <form id="editExpenseForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label small fw-bold text-muted">{{ __('title') }} <span class="text-danger">*</span></label>
                                <input type="text" id="edit_title" name="title" class="form-control" required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-bold text-muted">{{ __('amount') }} ($) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" id="edit_amount" name="amount" class="form-control" required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-bold text-muted">{{ __('date') }} <span class="text-danger">*</span></label>
                                <input type="date" id="edit_date" name="expense_date" class="form-control" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold text-muted">{{ __('category') }} <span class="text-danger">*</span></label>
                                <select id="edit_category" name="category" class="form-select" required>
                                    <option value="Utilities">{{ __('Utilities') }}</option>
                                    <option value="Rent">{{ __('Rent') }}</option>
                                    <option value="Payroll">{{ __('Payroll') }}</option>
                                    <option value="Supplies">{{ __('Supplies') }}</option>
                                    <option value="Others">{{ __('Others') }}</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold text-muted">{{ __('note') }}</label>
                                <textarea id="edit_note" name="note" class="form-control" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-top-0">
                        <button type="button" class="btn btn-outline-secondary fw-bold px-4" data-bs-dismiss="modal">{{ __('cancel_btn') }}</button>
                        <button type="submit" class="btn btn-primary fw-bold px-4">{{ __('save_changes') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Scripts Configuration --}}
    @push('scripts')
        <script>
            window.expensesIndexConfig = {
                swalTitle: "{{ __('confirm_delete') }}",
                swalText: "{{ __('delete_warning') }}",
                swalConfirmBtn: "{{ __('delete_btn_confirm') }}",
                swalCancelBtn: "{{ __('cancel_btn') }}"
            };

            // JavaScript to handle loading raw data into the modal dynamically
            document.addEventListener('DOMContentLoaded', function () {
                const editModal = document.getElementById('editExpenseModal');
                if (editModal) {
                    editModal.addEventListener('show.bs.modal', function (event) {
                        const button = event.relatedTarget; // Button that triggered the modal

                        // Extract info from data-* attributes
                        const id = button.getAttribute('data-id');
                        const title = button.getAttribute('data-title');
                        const amount = button.getAttribute('data-amount');
                        const date = button.getAttribute('data-date');
                        const category = button.getAttribute('data-category');
                        const note = button.getAttribute('data-note');

                        // Dynamically compile the route path using JavaScript
                        const form = document.getElementById('editExpenseForm');
                        form.action = `/expenses/${id}`;

                        // Update the modal's input fields
                        document.getElementById('edit_title').value = title;
                        document.getElementById('edit_amount').value = amount;
                        document.getElementById('edit_date').value = date;
                        document.getElementById('edit_category').value = category;
                        document.getElementById('edit_note').value = note || '';
                    });
                }
            });
        </script>
        <script defer src="{{ asset('js/pages/expenses-index.js') }}"></script>
    @endpush
@endsection
