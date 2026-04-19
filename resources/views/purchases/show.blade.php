@extends('layouts.app')

@section('title', __('Purchase Details'))

@section('content')
<div class="container-fluid">

    <a href="{{ route('purchases.index') }}" class="btn btn-secondary mb-3">
        <i class="fas fa-arrow-left me-1"></i> {{ __('Back') }}
    </a>

    <div class="card shadow mb-4">
        <div class="card-header py-3 text-dark d-flex justify-content-between align-items-center">
            <h5 class="m-0 fw-bold">{{ __('Purchase') }} #{{ str_pad($purchase->PurchaseID, 5, '0', STR_PAD_LEFT) }}</h5>
            <span class="badge bg-light border text-primary fs-6">
                {{ $purchase->Status ? __($purchase->Status) : __('Completed') }}
            </span>
        </div>
        <div class="card-body">

            <div class="row mb-4">
                <div class="col-sm-4">
                    <h6 class="fw-bold text-gray-800">{{ __('Supplier') }}:</h6>
                    <p class="mb-1 text-primary">{{ $purchase->supplier->Name ?? __('Unknown') }}</p>
                    <p class="mb-0 text-muted small">{{ $purchase->supplier->Contact ?? '' }}</p>
                </div>
                <div class="col-sm-4">
                    <h6 class="fw-bold text-gray-800">{{ __('Date') }}:</h6>
                    <p>{{ \Carbon\Carbon::parse($purchase->Date)->format('d M Y, h:i A') }}</p>
                </div>
                <div class="col-sm-4 text-end">
                    <h6 class="fw-bold text-gray-800">{{ __('Total Amount') }}:</h6>
                    <h4 class="text-success fw-bold">${{ number_format($purchase->Total, 2) }}</h4>
                </div>
            </div>

            <hr>

            <h6 class="fw-bold text-gray-800 mb-3">{{ __('Purchased Items') }}:</h6>
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('Product Name') }}</th>
                            <th class="text-center">{{ __('Qty') }}</th>
                            <th class="text-end">{{ __('Unit Cost') }}</th>
                            <th class="text-end">{{ __('Subtotal') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($purchase->details as $detail)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($detail->product && $detail->product->Image)
                                        <img src="{{ asset('storage/' . $detail->product->Image) }}" width="40" height="40" class="rounded me-2 border object-fit-cover">
                                    @endif
                                    {{ $detail->product->Name ?? __('Deleted Product') }}
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
                            <td colspan="3" class="text-end fw-bold">{{ __('Grand Total') }}:</td>
                            <td class="text-end fw-bold text-success fs-5">${{ number_format($purchase->Total, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

        </div>
    </div>
</div>
@endsection
