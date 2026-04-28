@extends('layouts.app')

@section('title', __('Purchase History'))

@section('content')
    @include('partials.alerts')

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white border-bottom pt-3 pb-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-shopping-cart text-primary me-2"></i>{{ __('Stock Purchase History') }}</h5>
            <a href="{{ route('purchases.create') }}" class="btn btn-primary btn-sm fw-medium px-3 shadow-sm">
                <i class="fas fa-plus me-1"></i> {{ __('Purchase') }}
            </a>
        </div>
        <div class="card-body bg-light rounded-bottom">
            <div class="table-responsive bg-white rounded shadow-sm border border-light-subtle">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-secondary small text-uppercase">
                        <tr>
                            <th class="ps-3 py-3">{{ __('Reference ID') }}</th>
                            <th class="py-3">{{ __('Date') }}</th>
                            <th class="py-3">{{ __('Supplier') }}</th>
                            <th class="py-3">{{ __('Total Amount') }}</th>
                            <th class="py-3">{{ __('Status') }}</th>
                            <th class="text-end pe-3 py-3">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse($purchases as $purchase)
                            <tr>
                                <td class="ps-3 fw-medium text-muted">PO-{{ str_pad($purchase->PurchaseID, 5, '0', STR_PAD_LEFT) }}</td>

                                <td class="text-dark">{{ \Carbon\Carbon::parse($purchase->Date)->format('d M Y') }}</td>

                                <td class="fw-bold text-dark">{{ $purchase->supplier->Name ?? __('Unknown') }}</td>

                                <td class="text-success fw-bold">${{ number_format($purchase->Total, 2) }}</td>

                                <td><span class="badge bg-success bg-opacity-10 text-success border border-success-subtle px-2 py-1"><i class="fas fa-check-circle me-1"></i> {{ __('Completed') }}</span></td>
                                <td class="text-end pe-3">
                                    <div class="btn-group shadow-sm">
                                        <a href="{{ route('purchases.show', $purchase->PurchaseID) }}"
                                            class="btn btn-sm btn-light text-info border" title="{{ __('View') }}">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted bg-white">
                                    <div class="d-flex flex-column align-items-center justify-content-center py-4">
                                        <i class="fas fa-shopping-cart fa-3x mb-3 text-secondary opacity-50"></i>
                                        <h5 class="fw-medium text-dark">{{ __('No purchase history found.') }}</h5>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                {{-- User pagination if exists --}}
                @if(method_exists($purchases, 'links'))
                    <div class="d-flex justify-content-end mt-3">
                        {{ $purchases->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
