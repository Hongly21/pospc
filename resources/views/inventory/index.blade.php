@extends('layouts.app')

@section('title', 'ការកែតម្រូវស្តុក')

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

        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 fw-bold text-primary"><i class="fas fa-boxes me-2"></i>ស្ថានភាពស្តុកបច្ចុប្បន្ន</h6>

            <a href="{{ route('inventory.history') }}" class="btn btn-outline-primary btn-sm ">
                <i class="fas fa-history me-1"></i> មើលប្រវត្តិ (History Log)
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('inventory.index') }}" method="GET" class="row w-100 g-2 mb-4">
                <div class="col-12 col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="ស្វែងរក..."
                            value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <select name="CategoryID" class="form-select">
                        <option value="">ប្រភេទទាំងអស់ (All Category)</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->CategoryID }}"
                                {{ request('CategoryID') == $cat->CategoryID ? 'selected' : '' }}>
                                {{ $cat->Name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-outline-primary px-4 flex-grow-1">
                        ស្វែងរក
                    </button>

                    {{-- Show Clear button if search OR category is filtered --}}
                    @if (request()->filled('search') || request()->filled('CategoryID'))
                        <a href="{{ route('inventory.index') }}" class="btn btn-outline-danger">
                            <i class="fas fa-sync-alt"></i> សម្អាត
                        </a>
                    @endif
                </div>
            </form>
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th>ទំនិញ</th>
                            <th>ប្រភេទទំនិញ</th>
                            <th>ស្តុកបច្ចុប្បន្ន</th>
                            <th>Reorder Level</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                            <tr>
                                <td class="fw-bold">
                                    <div class="d-flex align-items-center">
                                        @if ($product->Image)
                                            <img src="{{ asset($product->Image) }}" width="40" height="40"
                                                class="rounded me-2 border" style="object-fit: cover;">
                                        @else
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center me-2 border"
                                                style="width:40px; height:40px;">
                                                <i class="fas fa-box text-muted"></i>
                                            </div>
                                        @endif
                                        {{ $product->Name }}
                                    </div>
                                </td>
                                <td>{{ $product->category->Name ?? '-' }}</td>
                                <td>
                                    <span
                                        class="badge {{ ($product->inventory->Quantity ?? 0) <= ($product->inventory->ReorderLevel ?? 0) ? 'bg-danger' : 'bg-success' }} fs-8">
                                        ចំនួន {{ $product->inventory->Quantity ?? 0 }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-warning text-dark fs-8">
                                        {{ $product->inventory->ReorderLevel ?? 0 }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    {{-- edit reorder level --}}
                                    <button class="btn btn-outline-secondary btn-sm mb-1" data-bs-toggle="modal"
                                        data-bs-target="#adjustStockreorderModal{{ $product->ProductID }}">
                                        <i class="fas fa-sliders-h"></i> ប្តូរកម្រិតទិញចូល
                                    </button> <button class="btn btn-outline-primary btn-sm mb-1" data-bs-toggle="modal"
                                        data-bs-target="#adjustStockModal{{ $product->ProductID }}">
                                        <i class="fas fa-sliders-h"></i> កែប្រែស្តុក
                                    </button>

                                </td>
                            </tr>
                            {{-- Adjust Stock Modal --}}
                            <div class="modal fade" id="adjustStockModal{{ $product->ProductID }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header text-dark fw-bold">
                                            <h5 class="modal-title fw-bold">ការកែតម្រូវស្តុក: {{ $product->Name }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="{{ route('inventory.update') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $product->ProductID }}">

                                            <div class="modal-body">
                                                <div class="alert alert-info">
                                                    ស្តុកបច្ចុប្បន្ន:
                                                    <strong>{{ $product->inventory->Quantity ?? 0 }}</strong>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">Action</label>
                                                    <select name="action" class="form-select">
                                                        <option value="add">បន្ថែមស្តុក (+)</option>
                                                        <option value="subtract">បន្ថយស្តុក (-)</option>
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">បរិមាណ (Quantity to Adjust)</label>
                                                    <input type="number" name="quantity" class="form-control"
                                                        min="1" required>
                                                </div>
                                                {{--
                                                <div class="mb-3">
                                                    <label
                                                        class="form-label fw-bold text-warning-emphasis">កម្រិតត្រូវទិញបន្ថែម
                                                        (Reorder Level)
                                                    </label>
                                                    <input type="number" name="reorder_level" class="form-control"
                                                        value="{{ $product->inventory->ReorderLevel ?? 0 }}" min="0"
                                                        required>
                                                </div> --}}

                                                <div class="mb-3">
                                                    <label class="form-label">ហេតុផល (Optional Reason)</label>
                                                    <input type="text" name="reason" class="form-control"
                                                        placeholder="e.g. Broken, Found extra...">
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-outline-secondary fw-bold"
                                                    data-bs-dismiss="modal">បោះបង់</button>
                                                <button type="submit"
                                                    class="btn btn-outline-primary fw-bold">រក្សារទុក</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            {{-- Adjust Stock Modal reorder --}}
                            <div class="modal fade" id="adjustStockreorderModal{{ $product->ProductID }}"
                                tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header text-dark fw-bold">
                                            <h5 class="modal-title fw-bold">ការកែតម្រូវស្តុក: {{ $product->Name }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="{{ route('inventory.updatereorder') }}" method="GET">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $product->ProductID }}">
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label  text-warning-emphasis">កម្រិតត្រូវទិញបន្ថែម
                                                        (Reorder Level)
                                                    </label>
                                                    <input type="number" name="reorder_level" class="form-control"
                                                        value="{{ $product->inventory->ReorderLevel ?? 0 }}"
                                                        min="0" required>
                                                </div>

                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-outline-secondary fw-bold"
                                                    data-bs-dismiss="modal">បោះបង់</button>
                                                <button type="submit"
                                                    class="btn btn-outline-primary fw-bold">រក្សារទុក</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No products found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
