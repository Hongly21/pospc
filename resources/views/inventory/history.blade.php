@extends('layouts.app')

@section('title', __('inventory.history_page_title'))

@section('content')
    <div class="card border-0 shadow-sm rounded-3"> 
        <div class="card-header bg-white border-bottom pt-3 pb-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-dark">
                <i class="fas fa-history text-primary me-2"></i>{{ __('inventory.history_header') }}
            </h5>
            <a href="{{ route('inventory.index') }}" class="btn btn-outline-secondary btn-sm fw-medium px-3 shadow-sm">
                <i class="fas fa-arrow-left me-1"></i> {{ __('inventory.btn_back') }}
            </a>
        </div>
        <div class="card-body bg-light rounded-bottom">
            {{-- Filter Form --}}
            <form action="{{ route('inventory.history') }}" method="GET"
                class="row g-2 align-items-center mb-4 bg-white p-2 rounded shadow-sm mx-0">
                <div class="col-12 col-md-3">
                    <label class="form-label text-muted small fw-bold mb-1">{{ __('inventory.filter_start_date') }}</label>
                    <input type="date" name="start_date" class="form-control form-control-sm bg-light" value="{{ request('start_date') }}">
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label text-muted small fw-bold mb-1">{{ __('inventory.filter_end_date') }}</label>
                    <input type="date" name="end_date" class="form-control form-control-sm bg-light" value="{{ request('end_date') }}">
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label text-muted small fw-bold mb-1">{{ __('inventory.search_label') }}</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 bg-light" placeholder="{{ __('inventory.history_search_placeholder') }}"
                            value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-12 col-md-2 d-flex align-items-end gap-2 mt-auto pb-1">
                    <button type="submit" class="btn btn-sm btn-primary px-3 flex-grow-1 fw-bold">
                        <i class="fas fa-filter me-1"></i> {{ __('inventory.search_button') }}
                    </button>
                    @if (request()->has('start_date') || request()->has('search'))
                        <a href="{{ route('inventory.history') }}" class="btn btn-sm btn-outline-secondary px-3" title="{{ __('inventory.clear_button') }}">
                            <i class="fas fa-sync-alt"></i>
                        </a>
                    @endif
                </div>
            </form>

            {{-- History Table --}}
            <div class="table-responsive bg-white rounded shadow-sm border border-light-subtle">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-secondary small text-uppercase">
                        <tr>
                            <th class="ps-3 py-3">{{ __('inventory.history_col_datetime') }}</th>
                            <th class="py-3">{{ __('inventory.history_col_admin') }}</th>
                            <th class="py-3">{{ __('inventory.product') }}</th>
                            <th class="text-center py-3">{{ __('inventory.action') }}</th>
                            <th class="text-center py-3">{{ __('inventory.qty') }}</th>
                            <th class="pe-3 py-3">{{ __('inventory.modal_reason_label') }}</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse($histories as $history)
                            <tr>
                                <td class="ps-3 text-muted fw-medium">
                                    {{ \Carbon\Carbon::parse($history->created_at)->format('d-M-Y H:i A') }}
                                </td>
                                <td>
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle px-2 py-1">
                                        <i class="fas fa-user me-1 fa-sm"></i> {{ $history->user->Username ?? 'Unknown' }}
                                    </span>
                                </td>
                                <td class="fw-bold text-dark">
                                    {{ $history->product->Name ?? 'Deleted Product' }}
                                </td>
                                <td class="text-center">
                                    @if ($history->Action == 'add')
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle px-2 py-1">
                                            <i class="fas fa-plus me-1 fa-sm"></i> {{ __('inventory.modal_add_stock') }}
                                        </span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle px-2 py-1">
                                            <i class="fas fa-minus me-1 fa-sm"></i> {{ __('inventory.modal_subtract_stock') }}
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center fw-bold">
                                    {{ $history->Quantity }}
                                </td>
                                <td class="pe-3">
                                    @if ($history->Reason)
                                        <span class="text-muted small">{{ $history->Reason }}</span>
                                    @else
                                        <span class="text-muted small fst-italic">- {{ __('inventory.no_reason') }} -</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted bg-white">
                                    <div class="d-flex flex-column align-items-center justify-content-center py-4">
                                        <i class="fas fa-history fa-3x mb-3 text-secondary opacity-50"></i>
                                        <h5 class="fw-medium text-dark">{{ __('inventory.no_history_found') }}</h5>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end mt-4">
                {{ $histories->links() }}
            </div>
        </div>
    </div>
@endsection
