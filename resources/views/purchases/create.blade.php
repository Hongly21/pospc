@extends('layouts.app')

@section('title', __('New Purchase (Stock-In)'))

@section('content')
    <form action="{{ route('purchases.store') }}" method="POST">
        @csrf
        @include('partials.alerts')

        <div class="card shadow mb-4">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="m-0 fw-bold">{{ __('New Purchase (Stock-In)') }}</h5>

                <a href="{{ route('purchases.index') }}" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-arrow-left me-1"></i> {{ __('Back to History') }}
                </a>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        {{-- choose supplier --}}
                        <label class="fw-bold mb-2">{{ __('Supplier') }}</label>
                        <select name="SupplierID" class="form-select searchable-select" required>
                            <option value=""> {{ __('Select Supplier') }} </option>
                            @foreach ($suppliers as $s)
                                <option value="{{ $s->SupplierID }}">{{ $s->Name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold mb-2">{{ __('Purchase Date') }}</label>
                        <input type="date" name="PurchaseDate" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="purchaseTable">
                        <thead class="table-light">
                            <tr>
                                <th width="40%">{{ __('Product') }}</th>
                                <th width="20%">{{ __('Cost') }} ($)</th>
                                <th width="20%">{{ __('Quantity') }}</th>
                                <th width="20%">{{ __('Subtotal') }} ($)</th>
                                <th width="50px"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td data-label="{{ __('Product') }}">
                                    <select name="items[0][product_id]" class="form-select product-select searchable-select"
                                        required onchange="updatePrice(this)">
                                        <option value="">{{ __('Select Product') }}</option>

                                        @foreach ($products as $p)
                                            @php
                                                $qty = floatval($p->inventory->Quantity ?? 0);
                                                if ($qty <= 0) {
                                                    $statusText = __('Out of Stock');
                                                } elseif ($qty <= 5) {
                                                    $statusText = __('Low Stock');
                                                } else {
                                                    $statusText = __('In Stock');
                                                }
                                            @endphp

                                            <option value="{{ $p->ProductID }}" data-cost="{{ $p->CostPrice }}">
                                                {{ $p->Name }} @if ($p->attributes->isNotEmpty())
                                                    ({{ $p->attributes->map(fn($a) => $a->AttributeName . ': ' . $a->AttributeValue)->implode(', ') }})
                                                @endif - {{ $statusText }} ({{ __('Qty') }}:
                                                {{ $qty }})
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td data-label="{{ __('Cost') }} ($)"><input type="number" name="items[0][cost]"
                                        step="0.01" class="form-control cost-input" oninput="calcRow(this)" required>
                                </td>
                                <td data-label="{{ __('Quantity') }}"><input type="number" name="items[0][quantity]"
                                        class="form-control qty-input" oninput="calcRow(this)" required></td>
                                <td data-label="{{ __('Subtotal') }} ($)"><input type="text"
                                        class="form-control subtotal-display" readonly></td>
                                <td class="action-cell">
                                    <button type="button" class="btn btn-outline-danger btn-sm remove-row w-100">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <template id="purchaseRowTemplate">
                        <tr>
                            <td data-label="{{ __('Product') }}">
                                <select name="items[__INDEX__][product_id]" class="form-select product-select searchable-select"
                                    required onchange="updatePrice(this)">
                                    <option value="">{{ __('Select Product') }}</option>

                                    @foreach ($products as $p)
                                        @php
                                            $qty = floatval($p->inventory->Quantity ?? 0);
                                            if ($qty <= 0) {
                                                $statusText = __('Out of Stock');
                                            } elseif ($qty <= 5) {
                                                $statusText = __('Low Stock');
                                            } else {
                                                $statusText = __('In Stock');
                                            }
                                        @endphp

                                        <option value="{{ $p->ProductID }}" data-cost="{{ $p->CostPrice }}">
                                            {{ $p->Name }} @if ($p->attributes->isNotEmpty())
                                                ({{ $p->attributes->map(fn($a) => $a->AttributeName . ': ' . $a->AttributeValue)->implode(', ') }})
                                            @endif - {{ $statusText }} ({{ __('Qty') }}: {{ $qty }})
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td data-label="{{ __('Cost') }} ($)"><input type="number" name="items[__INDEX__][cost]"
                                    step="0.01" class="form-control cost-input" oninput="calcRow(this)" required>
                            </td>
                            <td data-label="{{ __('Quantity') }}"><input type="number" name="items[__INDEX__][quantity]"
                                    class="form-control qty-input" oninput="calcRow(this)" required></td>
                            <td data-label="{{ __('Subtotal') }} ($)"><input type="text"
                                    class="form-control subtotal-display" readonly></td>
                            <td class="action-cell">
                                <button type="button" class="btn btn-outline-danger btn-sm remove-row w-100">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    </template>
                </div>
                <button type="button" class="btn btn-outline-secondary btn-sm" id="addRow">
                    <i class="fas fa-plus me-1"></i> {{ __('Add Another Item') }}
                </button>
            </div>
        </div>

        <div class="text-end mb-5">
            <h3 class="fw-bold">{{ __('Total') }}: $<span id="grandTotal">0.00</span></h3>
            <button type="submit" class="btn btn-success px-4 py-2 mt-2 shadow-sm purchase-submit-btn">
                <i class="fas fa-check-circle me-1"></i> {{ __('Confirm Purchase & Add Stock') }}
            </button>
        </div>
    </form>

    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
        <link rel="stylesheet" href="{{ asset('css/pages/purchases-create.css') }}" />
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script src="{{ asset('js/pages/purchases-create.js') }}"></script>
    @endpush
@endsection
