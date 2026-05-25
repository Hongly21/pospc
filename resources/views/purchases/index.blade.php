@extends('layouts.app')

@section('title', __('Purchase History'))

@section('content')
    @include('partials.alerts')

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white border-bottom pt-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-shopping-cart text-primary me-2"></i>{{ __('Stock Purchase History') }}</h5>
            <a href="{{ route('purchases.create') }}" class="btn btn-primary btn-sm fw-medium px-3 shadow-sm">
                <i class="fas fa-plus me-1"></i> {{ __('Purchase') }}
            </a>
        </div>
        <div class="card-body bg-light rounded-bottom">
            {{-- Filter & Search Form --}}
            <form action="{{ route('purchases.index') }}" method="GET" class="row w-100 g-2 mb-4 d-print-none">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="search" class="form-label small fw-bold text-muted mb-1">{{ __('Search') }}</label>
                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" name="search" id="search" class="form-control border-start-0 ps-0 bg-white"
                                placeholder="{{ __('Supplier name, total amount...') }}"
                                value="{{ request('search') }}">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label for="start_date" class="form-label small fw-bold text-muted mb-1">{{ __('inventory.filter_start_date') }}</label>
                        <input type="date" name="start_date" id="start_date" class="form-control shadow-sm bg-white"
                            value="{{ request('start_date') }}">
                    </div>

                    <div class="col-md-3">
                        <label for="end_date" class="form-label small fw-bold text-muted mb-1">{{ __('inventory.filter_end_date') }}</label>
                        <input type="date" name="end_date" id="end_date" class="form-control shadow-sm bg-white"
                            value="{{ request('end_date') }}">
                    </div>

                    <div class="col-md-2">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary shadow-sm flex-grow-1">
                                 {{ __('Search') }}
                            </button>

                            {{-- Clear Button displays dynamically if a query parameters exist --}}
                            @if(request()->anyFilled(['search', 'start_date', 'end_date']))
                                <a href="{{ route('purchases.index') }}" class="btn btn-light border shadow-sm" title="{{ __('Clear Filters') }}">
                                    <i class="fas fa-sync-alt"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </form>
            {{-- --- NEW: Total Spent Summary Card --- --}}
            <div class="alert alert-info border-info-subtle shadow-sm d-flex justify-content-between align-items-center mb-4 py-3">
                <div class="d-flex align-items-center">
                    <div class="bg-white rounded-circle d-flex justify-content-center align-items-center me-3 shadow-sm" style="width: 40px; height: 40px;">
                        <i class="fas fa-dollar-sign text-info fs-5"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 text-info-emphasis fw-bold">{{ __('Total_Amount_Spent') }}</h6>
                    </div>
                </div>
                <h4 class="mb-0 fw-bold text-info-emphasis">${{ number_format($totalSpent, 2) }}</h4>
            </div>

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
                {{-- Pagination --}}
                @if(method_exists($purchases, 'links'))
                    <div class="d-flex justify-content-start mt-3">
                        {{ $purchases->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
