@extends('layouts.app')

@section('title', 'បញ្ជីចំណាយ (Expenses)')

@section('content')
    <div class="card shadow mb-4 border-0">
        <div class="card-body bg-light">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <form action="{{ route('expenses.index') }}" method="GET" class="row g-2">
                        <div class="col-md-5">
                            <input type="text" name="search" class="form-control" placeholder="ស្វែងរកចំណងជើងចំណាយ..."
                                value="{{ request('search') }}">
                        </div>
                        <div class="col-md-4">
                            <input type="month" name="month" class="form-control" value="{{ request('month') }}">
                        </div>
                        <div class="col-md-3 d-flex gap-2">
                            <button type="submit" class="btn btn-outline-primary "><i class="fas fa-search"></i>
                                ស្វែងរក</button>
                            <a href="{{ route('expenses.index') }}" class="btn btn-outline-danger"><i
                                    class="fas fa-sync"></i></a>
                        </div>
                    </form>
                </div>
                <div class="col-md-4 text-end">
                    <div class="border border-danger rounded p-2 bg-white d-inline-block shadow-sm">
                        <span class="text-muted fw-bold small me-2">ចំណាយសរុប (Total Expenses):</span>
                        <span class="h4 text-danger fw-bold mb-0">${{ number_format($totalExpense, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- success message --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow border-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 fw-bold "><i class="fas fa-list me-2"></i> ប្រវត្តិការកត់ត្រាចំណាយ</h6>
            <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addExpenseModal">
                <i class="fas fa-plus me-1"></i> កត់ត្រាចំណាយថ្មី
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted">
                        <tr>
                            <th class="ps-3">កាលបរិច្ឆេទ</th>
                            <th>ចំណងជើង</th>
                            <th>ប្រភេទ</th>
                            <th>អ្នកកត់ត្រា</th>
                            <th>បរិយាយ (Note)</th>
                            <th class="text-end fw-bold">ទឹកប្រាក់</th>
                            <th class="text-center pe-3">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($expenses as $exp)
                            <tr>
                                <td class="ps-3">{{ \Carbon\Carbon::parse($exp->expense_date)->format('d-M-Y') }}</td>
                                <td class="fw-bold text-dark">{{ $exp->title }}</td>
                                <td><span class="badge bg-secondary">{{ $exp->category }}</span></td>
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
                                        <button type="button" class="btn btn-sm btn-outline-danger  btn-delete mb-1">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            <div class="modal fade" id="editExpenseModal{{ $exp->id }}" tabindex="-1"
                                aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content border-0 shadow">
                                        <div class="modal-header">
                                            <h5 class="modal-title fw-bold"><i class="fas fa-edit me-2 text-warning"></i>
                                                កែប្រែព័ត៌មានចំណាយ</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('expenses.update', $exp->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">ចំណងជើង (Title) <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" name="title" class="form-control"
                                                        value="{{ $exp->title }}" required>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-bold">ទឹកប្រាក់ ($) <span
                                                                class="text-danger">*</span></label>
                                                        <input type="number" step="0.01" name="amount"
                                                            class="form-control" value="{{ $exp->amount }}" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-bold">កាលបរិច្ឆេទ <span
                                                                class="text-danger">*</span></label>
                                                        <input type="date" name="expense_date" class="form-control"
                                                            value="{{ $exp->expense_date }}" required>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">ប្រភេទ (Category) <span
                                                            class="text-danger">*</span></label>
                                                    <select name="category" class="form-select" required>
                                                        <option value="វិក្កយបត្រ (Utilities)"
                                                            {{ $exp->category == 'វិក្កយបត្រ (Utilities)' ? 'selected' : '' }}>
                                                            វិក្កយបត្រ (Utilities)</option>
                                                        <option value="ជួលទីតាំង (Rent)"
                                                            {{ $exp->category == 'ជួលទីតាំង (Rent)' ? 'selected' : '' }}>
                                                            ជួលទីតាំង (Rent)</option>
                                                        <option value="ប្រាក់ខែបុគ្គលិក (Payroll)"
                                                            {{ $exp->category == 'ប្រាក់ខែបុគ្គលិក (Payroll)' ? 'selected' : '' }}>
                                                            ប្រាក់ខែបុគ្គលិក (Payroll)</option>
                                                        <option value="ទិញសម្ភារៈ (Supplies)"
                                                            {{ $exp->category == 'ទិញសម្ភារៈ (Supplies)' ? 'selected' : '' }}>
                                                            ទិញសម្ភារៈ (Supplies)</option>
                                                        <option value="ផ្សេងៗ (Others)"
                                                            {{ $exp->category == 'ផ្សេងៗ (Others)' ? 'selected' : '' }}>
                                                            ផ្សេងៗ (Others)</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">បរិយាយ (Note)</label>
                                                    <textarea name="note" class="form-control" rows="2">{{ $exp->note }}</textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer bg-light">
                                                <button type="button" class="btn btn-outline-secondary btn-sm fw-bold"
                                                    data-bs-dismiss="modal">បិទ</button>
                                                <button type="submit"
                                                    class="btn btn-outline-warning fw-bold text-dark">រក្សាទុកការកែប្រែ</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fas fa-folder-open fa-3x mb-3 text-light"></i>
                                    <h5>មិនទាន់មានទិន្នន័យចំណាយនៅឡើយទេ</h5>
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

    <div class="modal fade" id="addExpenseModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-0 shadow">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="fas fa-plus-circle me-2 text-primary"></i> កត់ត្រាចំណាយថ្មី
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('expenses.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">ចំណងជើង (Title) <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">ទឹកប្រាក់ ($) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="amount" class="form-control"
                                    placeholder="0.00" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">កាលបរិច្ឆេទ <span class="text-danger">*</span></label>
                                <input type="date" name="expense_date" class="form-control"
                                    value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">ប្រភេទ (Category) <span class="text-danger">*</span></label>
                            <select name="category" class="form-select" required>
                                <option value="វិក្កយបត្រ (Utilities)">វិក្កយបត្រ (Utilities)</option>
                                <option value="ជួលទីតាំង (Rent)">ជួលទីតាំង (Rent)</option>
                                <option value="ប្រាក់ខែបុគ្គលិក (Payroll)">ប្រាក់ខែបុគ្គលិក (Payroll)</option>
                                <option value="ទិញសម្ភារៈ (Supplies)">ទិញសម្ភារៈ (Supplies)</option>
                                <option value="ផ្សេងៗ (Others)">ផ្សេងៗ (Others)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">បរិយាយ (Note)</label>
                            <textarea name="note" class="form-control" rows="2" placeholder="ព័ត៌មានបន្ថែម..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-outline-secondary btn-sm fw-bold"
                            data-bs-dismiss="modal">បោះបង់</button>
                        <button type="submit" class="btn btn-outline-primary btn-sm fw-bold">រក្សាទុក</button>
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
                    title: "តើអ្នកប្រាកដឬទេ?",
                    text: "ទិន្នន័យនេះនឹងត្រូវបានលុបជាអចិន្ត្រៃយ៍!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "លុបវា!",
                    cancelButtonText: "បោះបង់"
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>
