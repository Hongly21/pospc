@extends('layouts.app')

@section('title', 'ប្រវត្តិការកែតម្រូវស្តុក (Adjustment History)')

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 fw-bold text-primary">
                <i class="fas fa-history me-2"></i>ប្រវត្តិការកែតម្រូវស្តុក (Stock Adjustment History)
            </h6>
            <a href="{{ route('inventory.index') }}" class="btn btn-outline-secondary btn-sm fw-bold">
                <i class="fas fa-arrow-left me-1"></i> ត្រឡប់ក្រោយ (Back)
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('inventory.history') }}" method="GET"
                class="row bg-light p-3 rounded mb-4 mx-0 shadow-sm border">
                <div class="col-12 col-md-3 mb-2 mb-md-0">
                    <label class="form-label text-muted fs-8 fw-bold mb-1">ចាប់ពីថ្ងៃទី (Start Date)</label>
                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                </div>

                <div class="col-12 col-md-3 mb-2 mb-md-0">
                    <label class="form-label text-muted fs-8 fw-bold mb-1">ដល់ថ្ងៃទី (End Date)</label>
                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                </div>

                <div class="col-12 col-md-4 mb-2 mb-md-0">
                    <label class="form-label text-muted fs-8 fw-bold mb-1">ស្វែងរក (Search)</label>
                    <input type="text" name="search" class="form-control" placeholder="ឈ្មោះទំនិញ ឬ ឈ្មោះ Admin..."
                        value="{{ request('search') }}">
                </div>

                <div class="col-12 col-md-2 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-outline-primary flex-grow-1 fw-bold">
                        <i class="fas fa-filter me-1"></i> ស្វែងរក
                    </button>
                    @if (request()->has('start_date') || request()->has('search'))
                        <a href="{{ route('inventory.history') }}" class="btn btn-outline-danger" title="សម្អាត Filter">
                            <i class="fas fa-sync-alt"></i>
                        </a>
                    @endif
                </div>
            </form>
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th>ថ្ងៃខែម៉ោង (Date & Time)</th>
                            <th>អ្នកកែប្រែ (Admin)</th>
                            <th>ទំនិញ (Product)</th>
                            <th class="text-center">សកម្មភាព (Action)</th>
                            <th class="text-center">ចំនួន (Qty)</th>
                            <th>ហេតុផល (Reason)</th>
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
                                        <span class="badge bg-success"><i class="fas fa-plus me-1 fa-sm"></i> បន្ថែម
                                            (Add)</span>
                                    @else
                                        <span class="badge bg-danger"><i class="fas fa-minus me-1 fa-sm"></i> ដកចេញ
                                            (Subtract)
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
                                        <span class="text-muted fst-italic">- គ្មានហេតុផល -</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    មិនទាន់មានប្រវត្តិការកែប្រែស្តុកនៅឡើយទេ (No adjustment history found).</td>
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
