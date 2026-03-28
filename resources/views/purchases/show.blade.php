@extends('layouts.app')

@section('title', 'Purchase Details')

@section('content')
<div class="container-fluid">

    <a href="{{ route('purchases.index') }}" class="btn btn-secondary mb-3">
        <i class="fas fa-arrow-left"></i> ត្រលប់ក្រោយ
    </a>

    <div class="card shadow mb-4">
        <div class="card-header py-3  text-dark d-flex justify-content-between">
            <h5 class="m-0 fw-bold">ការបញ្ជាទិញ #{{ str_pad($purchase->PurchaseID, 5, '0', STR_PAD_LEFT) }}</h5>
            <span class="badge bg-light border text-primary fs-6">{{ $purchase->Status ?? 'រួចរាល់' }}</span>
        </div>
        <div class="card-body">

            <div class="row mb-4">
                <div class="col-sm-4">
                    <h6 class="fw-bold text-gray-800">អ្នកផ្គត់ផ្គង:</h6>
                    <p class="mb-1">{{ $purchase->supplier->Name ?? 'Unknown' }}</p>
                    <p class="mb-0 text-muted">{{ $purchase->supplier->Contact ?? '' }}</p>
                </div>
                <div class="col-sm-4">
                    <h6 class="fw-bold text-gray-800">កាលវបរិច្ឆេទ:</h6>
                    <p>{{ \Carbon\Carbon::parse($purchase->Date)->format('d M Y, h:i A') }}</p>
                </div>
                <div class="col-sm-4 text-end">
                    <h6 class="fw-bold text-gray-800">ចំនួនទឺកប្រាក់សរុប:</h6>
                    <h4 class="text-success fw-bold">${{ number_format($purchase->Total, 2) }}</h4>
                </div>
            </div>

            <hr>

            <h6 class="fw-bold text-gray-800 mb-3">ទំនិញដែលបានបញ្ចាទិញ:</h6>
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ឈ្មោះទំនិញ</th>
                            <th class="text-center">ចំនួន</th>
                            <th class="text-end">តម្លៃ​ / 1</th>
                            <th class="text-end">សរុប</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($purchase->details as $detail)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($detail->product && $detail->product->Image)
                                        <img src="{{ asset($detail->product->Image) }}" width="40" class="rounded me-2 border">
                                    @endif
                                    {{ $detail->product->Name ?? 'Deleted Product' }}
                                </div>
                            </td>
                            <td class="text-center">{{ $detail->Qty }}</td>
                            <td class="text-end">${{ number_format($detail->CostPrice, 2) }}</td>
                            <td class="text-end fw-bold">${{ number_format($detail->Qty * $detail->CostPrice, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="3" class="text-end fw-bold">សរុបទាំងអស់:</td>
                            <td class="text-end fw-bold text-success">${{ number_format($purchase->Total, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

        </div>
    </div>
</div>
@endsection
