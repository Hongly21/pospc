@extends('layouts.app')

@section('title', 'ប្រភេទទំនិញ')

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-table me-2"></i> បញ្ជីនៃប្រភេទទំនិញ</span>
            <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                <i class="fas fa-plus"></i> បន្ថែមប្រភេទទំនិញ
            </button>
        </div>
        <div class="card-body">
            <form action="{{ route('categories.index') }}" method="GET" class="row g-2 align-items-center mb-3">
                <div class="col-12 col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="ស្វែងរកតាមឈ្មោះ..."
                            value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <select name="status" class="form-select">
                        <option value=""> ស្ថានភាពទាំងអស់ </option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>ដំណើរការ (Active)
                        </option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>ផ្អាក (Inactive)
                        </option>
                    </select>
                </div>

                <div class="col-12 col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-outline-primary px-4 fw-bold">
                        ស្វែងរក
                    </button>
                    @if (request()->has('search') || request()->has('status'))
                        <a href="{{ route('categories.index') }}" class="btn btn-outline-danger">
                            <i class="fas fa-sync-alt"></i> សម្អាត
                        </a>
                    @endif
                </div>
            </form>
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th>លេខសម្គាល់</th>
                            <th>ឈ្មោះ</th>
                            <th>ស្ថានភាព</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                            <tr>
                                <td>{{ $category->CategoryID }}</td>
                                <td class="fw-bold">{{ $category->Name }}</td>
                                <td>
                                    @if ($category->status == 1)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    {{-- Edit Button --}}
                                    <button class="btn btn-sm btn-outline-warning text-yellow mb-1" data-bs-toggle="modal"
                                        data-bs-target="#editCategoryModal{{ $category->CategoryID }}">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    @if ($category->products && $category->products->count() > 0)
                                        <button type="button" class="btn btn-sm btn-outline-secondary" disabled
                                            title="មិនអាចលុបបានទេ ព្រោះមានទំនិញនៅក្នុងប្រភេទនេះ">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @else
                                        <form action="{{ route('categories.destroy', $category->CategoryID) }}"
                                            method="POST" class="d-inline delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-outline-danger btn-delete mb-1">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>

                            {{-- Edit Modal --}}
                            <div class="modal fade" id="editCategoryModal{{ $category->CategoryID }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header fw-bold text-dark">
                                            <h5 class="modal-title">កែប្រែទំនិញ</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="{{ route('categories.update', $category->CategoryID) }}"
                                            method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label>ឈ្មោះនៃប្រភេទទំនិញ</label>
                                                    <input type="text" name="Name" class="form-control"
                                                        value="{{ $category->Name }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">ស្ថានភាព (Status)</label>
                                                    <select name="status" class="form-select">
                                                        <option value="1"
                                                            {{ $category->status == 1 ? 'selected' : '' }}>Active
                                                            (ដំណើរការ)
                                                        </option>
                                                        <option value="0"
                                                            {{ $category->status == 0 ? 'selected' : '' }}>Inactive (ផ្អាក)
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-outline-secondary btn-sm fw-bold"
                                                    data-bs-dismiss="modal">បោះបង់</button>
                                                <button type="submit"
                                                    class="btn btn-outline-success btn-sm fw-bold">កែប្រែ</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">No categories found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    {{-- Add Modal --}}
    <div class="modal fade" id="addCategoryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header  text-dark">
                    <h5 class="modal-title fw-bold">បន្ថែមប្រភេទទំនិញ</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <form action="{{ route('categories.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label>ឈ្មោះប្រភេទទំនិញ <span class="text-danger">*</span></label>
                            <input type="text" name="Name" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary fw-bold"
                            data-bs-dismiss="modal">បោះបង់</button>
                        <button type="submit" class="btn btn-outline-success fw-bold">រក្សាទុក</button>
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
