@extends('layouts.app')

@section('title', 'អ្នកផ្គត់ផ្គង់')

@section('content')
    {{-- សារជូនដំណឹង ជោគជ័យ --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- 🌟 ថ្មី: សារជូនដំណឹង បរាជ័យ (ពេលលុបមិនបាន) --}}
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-table me-2"></i>អ្នកផ្គត់ផ្គង់</span>
            <button class="btn btn-outline-primary shadow-sm btn-sm" data-bs-toggle="modal"
                data-bs-target="#addSupplierModal">
                <i class="fas fa-plus fa-sm text-dark-50"></i> បន្ថែមអ្នកផ្គត់ផ្គង់ថ្មី
            </button>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <form action="{{ route('suppliers.index') }}" method="GET" class="row w-100 g-2 mb-4">
                    <div class="col-12 col-md-8">
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control"
                                placeholder="ស្វែងរក..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-12 col-md-4 d-flex gap-2">
                        <button type="submit" class="btn btn-outline-primary px-4 flex-grow-1">
                            ស្វែងរក
                        </button>
                        @if (request()->has('search') && request('search') != '')
                            <a href="{{ route('suppliers.index') }}" class="btn btn-outline-danger">
                                <i class="fas fa-sync-alt"></i> សម្អាត
                            </a>
                        @endif
                    </div>
                </form>
                <table class="table table-bordered table-hover align-middle" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th>លេខសំគាល់</th>
                            <th>ឈ្មោះ</th>
                            <th>ទំនាក់ទំនង</th>
                            <th>អាស័យដ្ធាន</th>
                            <th>ស្ថានភាព</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($suppliers as $supplier)
                            <tr>
                                <td>{{ $supplier->SupplierID }}</td>
                                <td class="fw-bold">{{ $supplier->Name }}</td>
                                <td>{{ $supplier->Contact }}</td>
                                <td>{{ $supplier->Address ?? '-' }}</td>
                                <td><span
                                        class="badge bg-{{ $supplier->status == 1 ? 'success' : 'danger' }}">{{ $supplier->status == 1 ? 'ប្រើប្រាស់' : 'ផ្អាក' }}</span>
                                </td>
                                <td class="text-center">
                                    {{-- Edit Button --}}
                                    <button class="btn btn-sm btn-outline-warning text-yellow mt-1" data-bs-toggle="modal"
                                        data-bs-target="#updateSupplierModal{{ $supplier->SupplierID }}">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    {{-- 🌟 ថ្មី: ឆែកមើលថាតើគាត់មានប្រវត្តិទិញចូល (Purchases) ឬអត់ --}}
                                    @if ($supplier->purchases && $supplier->purchases->count() > 0)
                                        <button type="button" class="btn btn-sm btn-outline-secondary mt-1" disabled title="មិនអាចលុបបានទេ ព្រោះអ្នកផ្គត់ផ្គង់នេះមានប្រវត្តិទិញចូល">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @else
                                        <form action="{{ route('suppliers.destroy', $supplier->SupplierID) }}" method="POST"
                                            class="d-inline delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-outline-danger mt-1 btn-delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>

                            {{-- Edit Modal --}}
                            <div class="modal fade" id="updateSupplierModal{{ $supplier->SupplierID }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header fw-bold text-dark">
                                            <h5 class="modal-title fw-bold">កែប្រែអ្នកផ្គត់ផ្គង</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form action="{{ route('suppliers.update', $supplier->SupplierID) }}"
                                                method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="mb-3">
                                                    <label for="name" class="form-label">ឈ្មោះ</label>
                                                    <input type="text" class="form-control" name="Name"
                                                        value="{{ $supplier->Name }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="contact" class="form-label">ទំនាក់ទំនង</label>
                                                    <input type="text" class="form-control" name="Contact"
                                                        value="{{ $supplier->Contact }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="address" class="form-label">អាស័យដ្ធាន</label>
                                                    <input type="text" class="form-control" name="Address"
                                                        value="{{ $supplier->Address }}">
                                                </div>
                                                <div class="mb-3">
                                                    <label for="status" class="form-label">ស្ថានភាព</label>
                                                    <select class="form-select" name="status" required>
                                                        <option value="1"
                                                            {{ $supplier->status == 1 ? 'selected' : '' }}>
                                                            ប្រើប្រាស់</option>
                                                        <option value="0"
                                                            {{ $supplier->status == 0 ? 'selected' : '' }}>
                                                            ផ្អាក</option>
                                                    </select>
                                                </div>
                                                <div class="modal-footer px-0 pb-0">
                                                    <button type="button" class="btn btn-outline-secondary fw-bold"
                                                        data-bs-dismiss="modal">បោះបង់</button>
                                                    <button type="submit" class="btn btn-outline-primary fw-bold">កែប្រែ</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">រកមិនឃើញអ្នកផ្គត់ផ្គង់.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-end mt-3">
                {{ $suppliers->links() }}
            </div>
        </div>
    </div>

    {{-- Add Modal --}}
    <div class="modal fade" id="addSupplierModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header text-dark fw-bold">
                    <h5 class="modal-title fw-bold">បញ្ចុលអ្នកផ្គត់ផ្គង់ថ្មី</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('suppliers.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label>ឈ្មោះអ្នកផ្គត់ផ្គង់<span class="text-danger">*</span></label>
                            <input type="text" name="Name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>ទំនាក់ទំនង (លេខទូរស័ព្ទ/អីមែល) <span class="text-danger">*</span></label>
                            <input type="text" name="Contact" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>អាស័យដ្ធាន</label>
                            <textarea name="Address" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary fw-bold" data-bs-dismiss="modal">បោះបង់</button>
                        <button type="submit" class="btn btn-outline-primary fw-bold">រក្សាទុក</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deleteButtons = document.querySelectorAll('.btn-delete');

            deleteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    // 🌟 ថ្មី: ការពារការលុបមុនពេលផ្ទាំងលោតសួរ
                    e.preventDefault();
                    const form = this.closest('form');

                    Swal.fire({
                        title: "តើអ្នកប្រាកដឬទេ?",
                        text: "ទិន្នន័យនេះនឹងត្រូវបានលុបជាអចិន្ត្រៃយ៍!",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#d33",
                        cancelButtonColor: "#3085d6",
                        confirmButtonText: "បាទ, លុបវា!",
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
@endsection
