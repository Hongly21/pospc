@extends('layouts.app')

@section('title', __('Purchase History'))

@section('content')
    @include('partials.alerts')

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-table me-2"></i> {{ __('Stock Purchase History') }}</span>
            <a href="{{ route('purchases.create') }}" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-plus"></i> {{ __('Purchase') }}
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('Reference ID') }}</th>
                            <th>{{ __('Date') }}</th>
                            <th>{{ __('Supplier') }}</th>
                            <th>{{ __('Total Amount') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($purchases as $purchase)
                            <tr>
                                <td class="fw-bold">PO-{{ str_pad($purchase->PurchaseID, 5, '0', STR_PAD_LEFT) }}</td>

                                <td>{{ \Carbon\Carbon::parse($purchase->Date)->format('d M Y') }}</td>

                                <td>{{ $purchase->supplier->Name ?? __('Unknown') }}</td>

                                <td class="text-success fw-bold">${{ number_format($purchase->Total, 2) }}</td>

                                <td><span class="badge bg-success">{{ __('Completed') }}</span></td>
                                <td>
                                    <a href="{{ route('purchases.show', $purchase->PurchaseID) }}"
                                        class="btn btn-sm btn-info text-white">
                                        <i class="fas fa-eye"></i> {{ __('View') }}
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    {{ __('No purchase history found.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
