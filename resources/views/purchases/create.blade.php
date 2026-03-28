@extends('layouts.app')

@section('title', 'បញ្ជាទិញថ្មី (បន្ថែមស្តុក)')

@section('content')
    <form action="{{ route('purchases.store') }}" method="POST">
        @csrf
        <div class="card shadow mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="m-0 fw-bold">បញ្ជាទិញថ្មី (បន្ថែមស្តុក)</h5>

                <button type="button" class="btn btn-sm btn-outline-primary">
                    <a href="{{ route('purchases.index') }}" class="text-decoration-none text-reset">
                        ត្រឡប់ទៅប្រវត្តិ
                    </a>
                </button>

            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <label class="fw-bold mb-2">អ្នកផ្គត់ផ្គង់</label>
                        <select name="SupplierID" class="form-select" required>
                            <option value=""> ជ្រើសរើសអ្នកផ្គត់ផ្គង់ </option>
                            @foreach ($suppliers as $s)
                                <option value="{{ $s->SupplierID }}">{{ $s->Name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold mb-2">កាលបរិច្ឆេទទិញ</label>
                        <input type="date" name="PurchaseDate" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-body">
                <table class="table table-bordered" id="purchaseTable">
                    <thead class="table-light">
                        <tr>
                            <th width="40%">ផលិតផល</th>
                            <th width="20%">តម្លៃ ($)</th>
                            <th width="20%">បរិមាណ</th>
                            <th width="20%">សរុប ($)</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <select name="items[0][product_id]" class="form-select product-select" required
                                    onchange="updatePrice(this)">
                                    <option value="">ជ្រើសរើសផលិតផល</option>

                                    @foreach ($products as $p)
                                        @php
                                            $qty = floatval($p->inventory->Quantity ?? 0);
                                            $stockStatus = '';
                                            if ($qty <= 0) {
                                                $stockStatus = "អស់ស្តុក (Qty: $qty)";
                                            } elseif ($qty <= 5) {
                                                $stockStatus = "ជិតអស់ (Qty: $qty)";
                                            } else {
                                                $stockStatus = "មានស្តុក (Qty: $qty)";
                                            }
                                        @endphp

                                        <option value="{{ $p->ProductID }}" data-cost="{{ $p->CostPrice }}">
                                            {{ $p->Name }} - {{ $stockStatus }}
                                        </option>
                                    @endforeach

                                </select>
                            </td>
                            <td><input type="number" name="items[0][cost]" step="0.01" class="form-control cost-input"
                                    oninput="calcRow(this)" required></td>
                            <td><input type="number" name="items[0][quantity]" class="form-control qty-input"
                                    oninput="calcRow(this)" required></td>
                            <td><input type="text" class="form-control subtotal-display" readonly></td>
                            <td><button type="button" class="btn btn-outline-danger btn-sm remove-row">លុប</button></td>
                        </tr>
                    </tbody>
                </table>
                <button type="button" class="btn btn-outline-secondary btn-sm" id="addRow">+ បន្ថែមទំនិញមួយទៀត</button>
            </div>
        </div>

        <div class="text-end">
            <h3>សរុប: $<span id="grandTotal">0.00</span></h3>
            <button type="submit" class="btn btn-outline-success mt-2">បញ្ជាក់ការទិញ & បន្ថែមស្តុក</button>
        </div>
    </form>


    <script>
        let rowIdx = 1;

        document.getElementById('addRow').addEventListener('click', function() {
            let table = document.getElementById('purchaseTable').getElementsByTagName('tbody')[0];
            let newRow = table.rows[0].cloneNode(true);

            newRow.innerHTML = newRow.innerHTML.replace(/items\[0\]/g, `items[${rowIdx}]`);
            let inputs = newRow.getElementsByTagName('input');
            for (let input of inputs) {
                input.value = '';
            }

            table.appendChild(newRow);
            rowIdx++;
        });

        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-row')) {
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
