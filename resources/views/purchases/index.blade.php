@extends('layouts.app')

@section('title', 'ប្រវត្តិនៃការបញ្ចាទិញចូលស្តុក')

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-table me-2"></i> ការបញ្ចាទិញចូលស្តុក</span>
            <a href="{{ route('purchases.create') }}" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-plus"></i> បញ្ចាទិញ
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th>លេខសំគាល់</th>
                            <th>កាលបរិច្ឆេទ</th>
                            <th>អ្នកផ្គត់ផ្គង់</th>
                            <th>ចំនួនទឺកប្រាក់សរុប</th>
                            <th>ស្ថានភាព</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($purchases as $purchase)
                            <tr>
                                <td class="fw-bold">PO-{{ str_pad($purchase->PurchaseID, 5, '0', STR_PAD_LEFT) }}</td>

                                <td>{{ \Carbon\Carbon::parse($purchase->Date)->format('d M Y') }}</td>

                                <td>{{ $purchase->supplier->Name ?? 'Unknown' }}</td>

                                <td class="text-success fw-bold">${{ number_format($purchase->Total, 2) }}</td>

                                <td><span class="badge bg-success">រួចរាល់</span></td>
                                <td>
                                    <a href="{{ route('purchases.show', $purchase->PurchaseID) }}"
                                        class="btn btn-sm btn-info text-white">
                                        <i class="fas fa-eye"></i> មើល
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No purchase history found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>


@endsection
