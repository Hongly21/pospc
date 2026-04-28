@extends('layouts.app')

@section('title', __('New Purchase (Stock-In)'))

@section('content') 
    <form action="{{ route('purchases.store') }}" method="POST">
        @csrf
        <div class="card shadow mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
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
                        <select name="SupplierID" class="form-select" required>
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
                                <td>
                                    <select name="items[0][product_id]" class="form-select product-select" required
                                        onchange="updatePrice(this)">
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
                                                {{ $p->Name }} @if($p->attributes->isNotEmpty()) ({{ $p->attributes->map(fn($a) => $a->AttributeName . ': ' . $a->AttributeValue)->implode(', ') }}) @endif - {{ $statusText }} ({{ __('Qty') }}: {{ $qty }})
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td><input type="number" name="items[0][cost]" step="0.01" class="form-control cost-input"
                                        oninput="calcRow(this)" required></td>
                                <td><input type="number" name="items[0][quantity]" class="form-control qty-input"
                                        oninput="calcRow(this)" required></td>
                                <td><input type="text" class="form-control subtotal-display" readonly></td>
                                <td>
                                    <button type="button" class="btn btn-outline-danger btn-sm remove-row">
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
            <button type="submit" class="btn btn-success px-4 py-2 mt-2 shadow-sm">
                <i class="fas fa-check-circle me-1"></i> {{ __('Confirm Purchase & Add Stock') }}
            </button>
        </div>
    </form>

    <script>
        // No changes needed to JS logic, it uses the cloned HTML which is already localized!
        let rowIdx = 1;
        document.getElementById('addRow').addEventListener('click', function() {
            let table = document.getElementById('purchaseTable').getElementsByTagName('tbody')[0];
            let firstRow = table.rows[0];
            let newRow = firstRow.cloneNode(true);

            newRow.innerHTML = newRow.innerHTML.replace(/items\[0\]/g, `items[${rowIdx}]`);
            let inputs = newRow.getElementsByTagName('input');
            for (let input of inputs) { input.value = ''; }

            table.appendChild(newRow);
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
