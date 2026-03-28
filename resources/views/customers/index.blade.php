@extends('layouts.app')
@section('title', 'ព័ត៌មានអតិថិជន')
@section('content')


    <div class="d-flex justify-content-between align-items-center mb-4 mt-4">

        <button type="button" class="btn btn-outline-primary btn-sm shadow-sm" data-bs-toggle="modal"
            data-bs-target="#addCustomerModal">
            <i class="fas fa-plus me-1"></i> បន្ថែមអតិថិជនថ្មី
        </button>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i> មានបញ្ហាក្នុងការបញ្ចូលទិន្នន័យ (លេខទូរស័ព្ទអាចជាន់គ្នា)!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('customers.index') }}" method="GET" class="row w-100 g-2 mb-4">
                <div class="col-12 col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control"
                            placeholder="ស្វែងរកតាមឈ្មោះ ឬ លេខទូរស័ព្ទ..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <select name="status" class="form-select">
                        <option value="">ស្ថានភាពគណនី (All)</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active (ដំណើរការ)</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive (ផ្អាក)</option>
                    </select>
                </div>

                <div class="col-12 col-md-3">
                    <select name="debt_status" class="form-select border-primary text-primary">
                        <option value="" class="text-dark">ស្ថានភាពបំណុល (All)</option>
                        <option value="Paid" {{ request('debt_status') == 'Paid' ? 'selected' : '' }}
                            class="text-success">ទូទាត់រួច (No Debt)</option>
                        <option value="Debt" {{ request('debt_status') == 'Debt' ? 'selected' : '' }} class="text-danger">
                            កំពុងជំពាក់ (Has Debt)</option>
                    </select>
                </div>

                <div class="col-12 col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-outline-primary flex-grow-1 "><i class="fas fa-filter"></i>
                        ស្វែងរក</button>
                    <a href="{{ route('customers.index') }}" class="btn btn-outline-danger px-3"><i
                            class="fas fa-sync-alt me-1 fa-sm"></i></a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>ឈ្មោះអតិថិជន</th>
                            <th>លេខទូរស័ព្ទ</th>
                            <th class="text-center">ពិន្ទុ</th>
                            <th class="text-end pe-4">ជំពាក់ (Debt)</th>
                            <th class="text-center">ស្ថានភាព</th>
                            <th class="text-end">សកម្មភាព</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($customers as $customer)
                            <tr>
                                <td>{{ $customer->CustomerID }}</td>
                                <td class="fw-bold text-primary">{{ $customer->Name }}</td>
                                <td>{{ $customer->PhoneNumber ?? 'N/A' }}</td>
                                <td class="text-center"><span
                                        class="badge bg-info text-dark rounded-pill px-3">{{ $customer->Points ?? 0 }}</span>
                                </td>

                                <td class="text-end pe-4">
                                    @php
                                        $debt = 0;
                                        $unpaidOrders = \App\Models\Order::where('CustomerID', $customer->CustomerID)
                                            ->whereIn('Status', ['Partial', 'Unpaid'])
                                            ->get();
                                        foreach ($unpaidOrders as $ord) {
                                            $paid = \App\Models\Receipt::where('OrderID', $ord->OrderID)->sum(
                                                'PaidAmount',
                                            );
                                            $change = \App\Models\Receipt::where('OrderID', $ord->OrderID)->sum(
                                                'ChangeAmount',
                                            );
                                            $debt += max(0, $ord->TotalAmount - ($paid - $change));
                                        }
                                    @endphp

                                    @if ($debt > 0)
                                        <span class="text-danger fw-bold fs-6">${{ number_format($debt, 2) }}</span>
                                    @else
                                        <span class="text-muted">$0.00</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($customer->status == 1)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-outline-warning mb-1"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editCustomerModal{{ $customer->CustomerID }}">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    <form action="{{ route('customers.destroy', $customer->CustomerID) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                            class="btn btn-sm mb-1 btn-outline-{{ $customer->status == 1 ? 'danger' : 'success' }}"
                                            data-bs-toggle="modal"
                                            data-bs-target="#statusCustomerModal{{ $customer->CustomerID }}"
                                            title="ប្តូរស្ថានភាព">
                                            <i class="fas fa-{{ $customer->status == 1 ? 'ban' : 'check-circle' }} "></i>
                                        </button>

                                        {{-- edit status modal  --}}
                                        <div class="modal fade" id="statusCustomerModal{{ $customer->CustomerID }}"
                                            tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-sm modal-dialog-centered text-start">
                                                <div class="modal-content border-0 shadow-lg">
                                                    <div class="modal-header ">
                                                        <h6 class="modal-title fw-bold"><i
                                                                class="fas fa-exchange-alt me-2"></i>
                                                            បញ្ជាក់ការប្តូរស្ថានភាព</h6>
                                                        <button type="button" class="btn-close btn-close-white"
                                                            data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body text-center mt-3">
                                                        <p class="mb-2">តើអ្នកចង់ប្តូរស្ថានភាពអតិថិជន
                                                            <b>{{ $customer->Name }}</b>
                                                        </p>
                                                        <p>ទៅជា <span
                                                                class="badge bg-{{ $customer->status == 1 ? 'danger' : 'success' }} fs-6">{{ $customer->status == 1 ? 'Inactive (ផ្អាក)' : 'Active (ដំណើរការ)' }}</span>
                                                            មែនទេ?</p>
                                                    </div>
                                                    <div class="modal-footer bg-light justify-content-center">
                                                        <form
                                                            action="{{ route('customers.destroy', $customer->CustomerID) }}"
                                                            method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button"
                                                                class="btn btn-outline-secondary btn-sm"
                                                                data-bs-dismiss="modal">ទេ បោះបង់</button>
                                                            <button type="submit"
                                                                class="btn btn-outline-{{ $customer->status == 1 ? 'danger' : 'success' }} btn-sm fw-bold">បាទ/ចាស
                                                                យល់ព្រម</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </td>
                            </tr>

                            {{-- edit customer modal  --}}
                            <div class="modal fade" id="editCustomerModal{{ $customer->CustomerID }}" tabindex="-1"
                                aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content border-0 shadow text-start">
                                        <div class="modal-header">
                                            <h5 class="modal-title text-dark fw-bold"><i
                                                    class="fas fa-user-edit me-2"></i> កែប្រែព័ត៌មានអតិថិជន</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('customers.update', $customer->CustomerID) }}"
                                            method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">ឈ្មោះអតិថិជន <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" name="name" class="form-control"
                                                        value="{{ $customer->Name }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">លេខទូរស័ព្ទ <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" name="phone" class="form-control"
                                                        value="{{ $customer->PhoneNumber }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">ស្ថានភាព</label>
                                                    <select name="status" class="form-select">
                                                        <option value="1"
                                                            {{ $customer->status == 1 ? 'selected' : '' }}>Active
                                                            (ដំណើរការ)
                                                        </option>
                                                        <option value="0"
                                                            {{ $customer->status == 0 ? 'selected' : '' }}>Inactive
                                                            (ផ្អាក)</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">អាសយដ្ឋាន</label>
                                                    <textarea name="address" class="form-control" rows="2">{{ $customer->Address }}</textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer bg-light">
                                                <button type="button" class="btn btn-outline-secondary"
                                                    data-bs-dismiss="modal">បិទ</button>
                                                <button type="submit"
                                                    class="btn btn-outline-success fw-bold">កែប្រែទិន្នន័យ</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3 d-flex justify-content-end">
                {{ $customers->appends(request()->query())->links() }}
            </div>
        </div>
    </div>


    {{-- add modal  --}}
    <div class="modal fade" id="addCustomerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-outline-success text-dark">
                    <h5 class="modal-title fw-bold"><i class="fas fa-user-plus me-2"></i> បន្ថែមអតិថិជនថ្មី</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form action="{{ route('customers.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">ឈ្មោះអតិថិជន <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">លេខទូរស័ព្ទ <span class="text-danger">*</span></label>
                            <input type="text" name="phone" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">ស្ថានភាព</label>
                            <select name="status" class="form-select">
                                <option value="1" selected>Active (ដំណើរការ)</option>
                                <option value="0">Inactive (ផ្អាក)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">អាសយដ្ឋាន</label>
                            <textarea name="address" class="form-control" rows="2" placeholder="បញ្ចូលអាសយដ្ឋានទីតាំង (បើមាន)"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">បិទ</button>
                        <button type="submit" class="btn btn-outline-success fw-bold">រក្សាទុក</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
