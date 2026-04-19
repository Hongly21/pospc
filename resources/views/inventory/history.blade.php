@extends('layouts.app')

@section('title', __('inventory.history_page_title'))

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 fw-bold text-primary">
                <i class="fas fa-history me-2"></i>{{ __('inventory.history_header') }}
            </h6>
            <a href="{{ route('inventory.index') }}" class="btn btn-outline-secondary btn-sm fw-bold">
                <i class="fas fa-arrow-left me-1"></i> {{ __('inventory.btn_back') }}
            </a>
        </div>
        <div class="card-body">
            {{-- Filter Form --}}
            <form action="{{ route('inventory.history') }}" method="GET"
                class="row bg-light p-3 rounded mb-4 mx-0 shadow-sm border">
                <div class="col-12 col-md-3 mb-2 mb-md-0">
                    <label class="form-label text-muted fs-8 fw-bold mb-1">{{ __('inventory.filter_start_date') }}</label>
                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                </div>

                <div class="col-12 col-md-3 mb-2 mb-md-0">
                    <label class="form-label text-muted fs-8 fw-bold mb-1">{{ __('inventory.filter_end_date') }}</label>
                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                </div>

                <div class="col-12 col-md-4 mb-2 mb-md-0">
                    <label class="form-label text-muted fs-8 fw-bold mb-1">{{ __('inventory.search_label') }}</label>
                    <input type="text" name="search" class="form-control" placeholder="{{ __('inventory.history_search_placeholder') }}"
                        value="{{ request('search') }}">
                </div>

                <div class="col-12 col-md-2 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-outline-primary flex-grow-1 fw-bold">
                        <i class="fas fa-filter me-1"></i> {{ __('inventory.search_button') }}
                    </button>
                    @if (request()->has('start_date') || request()->has('search'))
                        <a href="{{ route('inventory.history') }}" class="btn btn-outline-danger" title="{{ __('inventory.clear_button') }}">
                            <i class="fas fa-sync-alt"></i>
                        </a>
                    @endif
                </div>
            </form>

            {{-- History Table --}}
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('inventory.history_col_datetime') }}</th>
                            <th>{{ __('inventory.history_col_admin') }}</th>
                            <th>{{ __('inventory.product') }}</th>
                            <th class="text-center">{{ __('inventory.action') }}</th>
                            <th class="text-center">{{ __('inventory.qty') }}</th>
                            <th>{{ __('inventory.modal_reason_label') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($histories as $history)
                            <tr>
                                <td>
                                    {{ \Carbon\Carbon::parse($history->created_at)->format('d-M-Y H:i A') }}
                                </td>
                                <td>
                                    <span class="badge bg-secondary bg-opacity-9 ">
                                        <i class="fas fa-user me-1 fa-sm"></i> {{ $history->user->Username ?? 'Unknown' }}
                                    </span>
                                </td>
                                <td class="fw-bold text-primary">
                                    {{ $history->product->Name ?? 'Deleted Product' }}
                                </td>
                                <td class="text-center">
                                    @if ($history->Action == 'add')
                                        <span class="badge bg-success">
                                            <i class="fas fa-plus me-1 fa-sm"></i> {{ __('inventory.modal_add_stock') }}
                                        </span>
                                    @else
                                        <span class="badge bg-danger">
                                            <i class="fas fa-minus me-1 fa-sm"></i> {{ __('inventory.modal_subtract_stock') }}
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center fw-bold">
                                    {{ $history->Quantity }}
                                </td>
                                <td>
                                    @if ($history->Reason)
                                        <span class="text-muted">{{ $history->Reason }}</span>
                                    @else
                                        <span class="text-muted fst-italic">- {{ __('inventory.no_reason') }} -</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    {{ __('inventory.no_history_found') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end mt-3">
                {{ $histories->links() }}
            </div>
        </div>
    </div>
@endsection
