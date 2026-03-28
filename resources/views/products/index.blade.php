@extends('layouts.app')

@section('title', 'ការគ្រប់គ្រងទំនិញ')

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-center">
                <strong>សូមពិនិត្យ BarCode ឡើងវិញ:</strong>
            </div>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-table me-2"></i>ទំនិញទាំងអស់</span>
            <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal"
                data-bs-target="#addProductModal">
                <i class="fas fa-plus me-1"></i> បន្ថែមទំនិញ
            </button>
        </div>
        <div class="card-body">
            <form action="{{ route('products.index') }}" method="GET" class="row g-2 align-items-center mb-3">
                <div class="col-12 col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="ស្វែងរកតាមឈ្មោះ ឬ Barcode..."
                            value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-12 col-md-3">
                    <select name="CategoryID" class="form-select">
                        <option value="">ប្រភេទទាំងអស់ (All Category)</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->CategoryID }}"
                                {{ request('CategoryID') == $category->CategoryID ? 'selected' : '' }}>
                                {{ $category->Name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-2">
                    <select name="status" class="form-select">
                        <option value="">ស្ថានភាពទាំងអស់</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>ដំណើរការ (Active)</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>ផ្អាក (Inactive)</option>
                    </select>
                </div>

                <div class="col-12 col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-outline-primary px-4">
                        ស្វែងរក
                    </button>
                    @if (request()->has('search') || request()->has('status') || request()->has('category_id'))
                        <a href="{{ route('products.index') }}" class="btn btn-outline-danger">
                            <i class="fas fa-sync-alt"></i> សម្អាត
                        </a>
                    @endif
                </div>
            </form>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>លេខសំគាល់</th>
                            <th>រូបភាព</th>
                            <th>ប្រភេទ</th>
                            <th>តម្លៃ</th>
                            <th>ស្តុក</th>
                            <th class="text-center">ស្ថានភាព</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                            <tr>
                                <td>{{ $product->ProductID }}</td>
                                <td class="fw-bold">
                                    <div class="d-flex align-items-center">
                                        @if ($product->Image)
                                            <img src="{{ asset($product->Image) }}" width="40" height="40"
                                                class="rounded me-2" style="object-fit: cover;">
                                        @else
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center me-2"
                                                style="width:40px; height:40px;">
                                                <i class="fas fa-image text-muted"></i>
                                            </div>
                                        @endif
                                        {{ $product->Name }}
                                    </div>
                                </td>
                                <td>{{ $product->category->Name ?? 'Uncategorized' }}</td>
                                <td>${{ number_format($product->SellPrice, 2) }}</td>
                                <td>
                                    <span
                                        class="badge {{ ($product->inventory->Quantity ?? 0) <= ($product->inventory->ReorderLevel ?? 0) ? 'bg-danger' : 'bg-success' }}">
                                        {{ $product->inventory->Quantity ?? 0 }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if ($product->Status == 1)
                                        <span
                                            class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle">ដំណើរការ</span>
                                    @else
                                        <span
                                            class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle">ផ្អាក</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-warning text-yellow me-1 mt-1"
                                        data-bs-toggle="modal" data-bs-target="#editProductModal{{ $product->ProductID }}">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    @if (auth()->user()->hasPermission('manage_products'))
                                        @if ($product->orderdetails && $product->orderdetails->count() > 0)
                                            <button type="button" class="btn btn-sm btn-outline-secondary mt-1 me-1"
                                                disabled title="មិនអាចលុបបានទេ ព្រោះទំនិញនេះមានប្រវត្តិលក់រួចហើយ">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @else
                                            <form action="{{ route('products.destroy', $product->ProductID) }}"
                                                method="POST" class="d-inline delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-danger mt-1 me-1 btn-delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    @endif
                                </td>
                            </tr>

                            {{-- Edit Product Modal --}}
                            <div class="modal fade" id="editProductModal{{ $product->ProductID }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header text-dark fw-bold">
                                            <h5 class="modal-title fw-bold"><i
                                                    class="fas fa-edit me-2 fw-bold"></i>កែប្រែទំនិញ:
                                                {{ $product->Name }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <form action="{{ route('products.update', $product->ProductID) }}" method="POST"
                                            enctype="multipart/form-data">
                                            @csrf
                                            @method('PUT')

                                            <div class="modal-body">
                                                <div class="row g-2">
                                                    <div class="col-12 col-md-6">
                                                        <label class="form-label small fw-bold">ឈ្មោះទំនិញ</label>
                                                        <input type="text" name="Name"
                                                            class="form-control form-control-sm"
                                                            value="{{ $product->Name }}" required>
                                                    </div>

                                                    <div class="col-12 col-md-6">
                                                        <label class="form-label small fw-bold">ប្រភេទ</label>
                                                        <select name="CategoryID" class="form-select form-select-sm"
                                                            required>
                                                            @foreach ($categories as $cat)
                                                                <option value="{{ $cat->CategoryID }}"
                                                                    {{ $product->CategoryID == $cat->CategoryID ? 'selected' : '' }}>
                                                                    {{ $cat->Name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="col-6 col-md-6">
                                                        <label class="form-label small fw-bold">ម៉ាក (Brand)</label>
                                                        <input type="text" name="Brand"
                                                            class="form-control form-control-sm"
                                                            value="{{ $product->Brand }}">
                                                    </div>
                                                    <div class="col-6 col-md-6">
                                                        <label class="form-label small fw-bold">ម៉ូដែល (Model)</label>
                                                        <input type="text" name="Model"
                                                            class="form-control form-control-sm"
                                                            value="{{ $product->Model }}">
                                                    </div>

                                                    <div class="col-6 col-md-4">
                                                        <label class="form-label small fw-bold">តម្លៃដើម ($)</label>
                                                        <input type="number" step="0.01" name="CostPrice"
                                                            class="form-control form-control-sm"
                                                            value="{{ $product->CostPrice }}" required>
                                                    </div>
                                                    <div class="col-6 col-md-4">
                                                        <label class="form-label small fw-bold">តម្លៃលក់ ($)</label>
                                                        <input type="number" step="0.01" name="SellPrice"
                                                            class="form-control form-control-sm"
                                                            value="{{ $product->SellPrice }}" required>
                                                    </div>
                                                    <div class="col-12 col-md-4">
                                                        <label class="form-label small fw-bold">ការធានា (ខែ)</label>
                                                        <input type="number" name="WarrantyMonths"
                                                            class="form-control form-control-sm"
                                                            value="{{ $product->WarrantyMonths }}">
                                                    </div>

                                                    <div class="col-6 col-md-6">
                                                        <label class="form-label small fw-bold">Barcode</label>
                                                        <input type="text" name="Barcode"
                                                            class="form-control form-control-sm"
                                                            value="{{ $product->Barcode }}">
                                                    </div>

                                                    <div class="col-6 col-md-6">
                                                        <label class="form-label small fw-bold text-primary">ស្ថានភាព
                                                            (Status)
                                                        </label>
                                                        <select name="status"
                                                            class="form-select form-select-sm border-primary">
                                                            <option value="1"
                                                                {{ $product->Status == 1 ? 'selected' : '' }}>Active
                                                                (ដំណើរការ)</option>
                                                            <option value="0"
                                                                {{ $product->Status == 0 ? 'selected' : '' }}>Inactive
                                                                (ផ្អាក)</option>
                                                        </select>
                                                    </div>

                                                    <div class="col-md-12">
                                                        <label class="form-label small fw-bold">កែប្រែរូបភាព</label>
                                                        <input type="file" name="Image"
                                                            class="form-control form-control-sm">
                                                        @if ($product->Image)
                                                            <div class="mt-2 p-2 bg-light rounded d-inline-block">
                                                                <small
                                                                    class="text-muted d-block mb-1">រូបភាពបច្ចុប្បន្ន:</small>
                                                                <img src="{{ asset($product->Image) }}" width="50"
                                                                    class="rounded border shadow-sm">
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <div class="col-md-12">
                                                        <label class="form-label small fw-bold">ការពិពណ៌នា</label>
                                                        <textarea name="Description" class="form-control form-control-sm" rows="2">{{ $product->Description }}</textarea>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="modal-footer bg-light">
                                                <button type="button" class="btn btn-outline-secondary fw-bold"
                                                    data-bs-dismiss="modal">បោះបង់</button>
                                                <button type="submit"
                                                    class="btn btn-outline-success fw-bold">រក្សាទុកការកែប្រែ</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="fas fa-box-open fa-3x mb-3 text-secondary"></i>
                                    <h5>មិនមានទំនិញនៅក្នុងប្រព័ន្ធទេ</h5>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-end mt-3">
                {{ $products->links() }}
            </div>
        </div>
    </div>

    {{-- add modal  --}}
    <div class="modal fade" id="addProductModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header  text-dark ">
                    <h5 class="modal-title fw-bold">បន្ថែមផលិតផលថ្មី</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-2">
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-bold">ឈ្មោះផលិតផល <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="Name" class="form-control form-control-sm" required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-bold">ប្រភេទ <span class="text-danger">*</span></label>
                                <select name="CategoryID" class="form-select form-select-sm" required>
                                    <option value="">ជ្រើសរើសប្រភេទ...</option>

                                    @foreach ($categories->where('status', 1) as $cat)
                                        <option value="{{ $cat->CategoryID }}">{{ $cat->Name }}</option>
                                    @endforeach

                                </select>
                            </div>

                            <div class="col-6 col-md-6">
                                <label class="form-label small fw-bold">ម៉ាក (Brand)</label>
                                <input type="text" name="Brand" class="form-control form-control-sm">
                            </div>
                            <div class="col-6 col-md-6">
                                <label class="form-label small fw-bold">ម៉ូដែល (Model)</label>
                                <input type="text" name="Model" class="form-control form-control-sm">
                            </div>

                            <div class="col-6 col-md-4">
                                <label class="form-label small fw-bold">ថ្លៃដើម ($)</label>
                                <input type="number" step="0.01" name="CostPrice"
                                    class="form-control form-control-sm" required>
                            </div>
                            <div class="col-6 col-md-4">
                                <label class="form-label small fw-bold">ថ្លៃលក់ ($)</label>
                                <input type="number" step="0.01" name="SellPrice"
                                    class="form-control form-control-sm" required>
                            </div>
                            <div class="col-6 col-md-4">
                                <label class="form-label small fw-bold">ស្តុក</label>
                                <input type="number" name="StockQuantity" class="form-control form-control-sm"
                                    value="0" required>
                            </div>

                            <div class="col-6 col-md-6">
                                <label class="form-label small fw-bold">ធានា (ខែ)</label>
                                <input type="number" name="WarrantyMonths" class="form-control form-control-sm"
                                    placeholder="0">
                            </div>
                            {{-- <div class="col-12 col-md-6">
                                <label class="form-label small fw-bold">Barcode</label>
                                <input type="text" name="Barcode" class="form-control form-control-sm">
                            </div> --}}
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-bold">Barcode</label>
                                <input type="text" name="Barcode"
                                    class="form-control form-control-sm @error('Barcode') is-invalid @enderror"
                                    value="{{ old('Barcode', $product->Barcode ?? '') }}">
                                @error('Barcode')
                                    <div class="invalid-feedback">
                                        Barcode នេះមានរួចហើយ! (This Barcode is already taken!)
                                    </div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label small fw-bold">រូបភាព</label>
                                <input type="file" name="Image" class="form-control form-control-sm">
                            </div>

                            <div class="col-12">
                                <label class="form-label small fw-bold">ការពិពណ៌នា</label>
                                <textarea name="Description" class="form-control form-control-sm" rows="2"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary fw-bold"
                            data-bs-dismiss="modal">បោះបង់</button>
                        <button type="submit" class="btn btn-outline-success fw-bold">រក្សាទុកផលិតផល</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

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
