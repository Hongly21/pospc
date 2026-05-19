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

    <style>
        @media (max-width: 768px) {
            #purchaseTable thead {
                display: none;
            }

            #purchaseTable,
            #purchaseTable tbody,
            #purchaseTable tr,
            #purchaseTable td {
                display: block;
                width: 100%;
            }

            #purchaseTable tr {
                border: 1px solid var(--border-color, #dee2e6);
                border-radius: 10px;
                margin-bottom: 0.85rem;
                /* background: #fff; */
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
                padding: 0.75rem;
            }

            #purchaseTable td {
                border: 0 !important;
                padding: 0.35rem 0;
            }

            #purchaseTable td::before {
                content: attr(data-label);
                display: block;
                font-size: 0.8rem;
                font-weight: 700;
                color: #6c757d;
                margin-bottom: 0.25rem;
            }

            #purchaseTable td.action-cell::before {
                display: none;
            }

            #purchaseTable .remove-row {
                margin-top: 0.2rem;
            }

            #addRow,
            .purchase-submit-btn {
                width: 100%;
            }
        }

    </style>

    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    @endpush
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        let rowIdx = 1;

        /**
         * Initialize searchable search on a select element
         */
        function initSearch(element) {
            let placeholderText = $(element).find('option[value=""]').text() || "{{ __('Select') }}";
            $(element).select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: placeholderText.trim(),
                dropdownParent: $(element).parent() // Ensures dropdown moves with the row
            });
        }

        // Initial setup for the first row
        $(document).ready(function() {
            $('.searchable-select').each(function() {
                initSearch(this);
            });
        });

        document.getElementById('addRow').addEventListener('click', function() {
            let table = document.getElementById('purchaseTable').getElementsByTagName('tbody')[0];
            let firstRow = table.rows[0];

            // 1. Clone the row
            let newRow = firstRow.cloneNode(true);

            // 2. Cleanup Select2 artifacts from the clone before appending
            $(newRow).find('.select2-container').remove();
            let select = $(newRow).find('.product-select');

            // 3. Reset Select2 internal states
            select.removeClass('select2-hidden-accessible')
                .removeAttr('data-select2-id')
                .removeAttr('aria-hidden')
                .show();

            // 4. Update the name attribute with correct index
            newRow.innerHTML = newRow.innerHTML.replace(/items\[0\]/g, `items[${rowIdx}]`);

            // 5. Reset inputs
            let inputs = newRow.getElementsByTagName('input');
            for (let input of inputs) {
                input.value = '';
            }

            // 6. Reset select value
            $(newRow).find('select').val('');

            // 7. Append to table
            table.appendChild(newRow);

            // 8. Re-initialize search for the new row
            initSearch($(newRow).find('.product-select'));

            rowIdx++;
        });

        document.addEventListener('click', function(e) {
            if (e.target.closest('.remove-row')) {
                let row = e.target.closest('tr');
                if (document.querySelectorAll('#purchaseTable tbody tr').length > 1) {
                    row.remove();
                    calcTotal();
                }
            }
        });

        window.updatePrice = function(select) {
            let cost = select.options[select.selectedIndex].getAttribute('data-cost');
            let row = select.closest('tr');
            if (cost) row.querySelector('.cost-input').value = cost;
            calcRow(select);
        }

        window.calcRow = function(element) {
            let row = element.closest('tr');
            let cost = parseFloat(row.querySelector('.cost-input').value) || 0;
            let qty = parseFloat(row.querySelector('.qty-input').value) || 0;
            row.querySelector('.subtotal-display').value = (cost * qty).toFixed(2);
            calcTotal();
        }

        function calcTotal() {
            let total = 0;
            document.querySelectorAll('.subtotal-display').forEach(input => {
                total += parseFloat(input.value) || 0;
            });
            document.getElementById('grandTotal').innerText = total.toFixed(2);
        }
    </script>
@endsection
