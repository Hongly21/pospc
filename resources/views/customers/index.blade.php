@extends('layouts.app')
@section('title', __('customer_information'))
@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4 mt-4">
        <button type="button" class="btn btn-outline-primary btn-sm shadow-sm" data-bs-toggle="modal"
            data-bs-target="#addCustomerModal">
            <i class="fas fa-plus me-1"></i> {{ __('add_new_customer') }}
        </button>
    </div>

    @include('partials.alerts')

    <div class="card">
        <div class="card-body">
            <form action="{{ route('customers.index') }}" method="GET" class="row w-100 g-2 mb-4">
                <div class="col-12 col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control"
                            placeholder="{{ __('search_placeholder') }}" value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <select name="status" class="form-select">
                        <option value="">{{ __('account_status') }} ({{ __('all') }})</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>{{ __('active') }}</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>{{ __('inactive') }}</option>
                    </select>
                </div>

                <div class="col-12 col-md-3">
                    <select name="debt_status" class="form-select border-primary text-primary">
                        <option value="" class="text-dark">{{ __('debt_status') }} ({{ __('all') }})</option>
                        <option value="Paid" {{ request('debt_status') == 'Paid' ? 'selected' : '' }}
                            class="text-success">{{ __('paid') }}</option>
                        <option value="Debt" {{ request('debt_status') == 'Debt' ? 'selected' : '' }} class="text-danger">
                            {{ __('has_debt') }}</option>
                    </select>
                </div>

                <div class="col-12 col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-outline-primary flex-grow-1 "><i class="fas fa-filter"></i>
                        {{ __('search_button') }}</button>
                    <a href="{{ route('customers.index') }}" class="btn btn-outline-danger px-3"><i
                            class="fas fa-sync-alt me-1 fa-sm"></i></a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>{{ __('customer_name') }}</th>
                            <th>{{ __('phone_number') }}</th>
                            <th class="text-center">{{ __('points') }}</th>
                            <th class="text-end pe-4">{{ __('debt') }}</th>
                            <th class="text-center">{{ __('status') }}</th>
                            <th class="text-end">{{ __('action') }}</th>
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
                                            $paid = \App\Models\Receipt::where('OrderID', $ord->OrderID)->sum('PaidAmount');
                                            $change = \App\Models\Receipt::where('OrderID', $ord->OrderID)->sum('ChangeAmount');
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
                                        <span class="badge bg-success">{{ __('active') }}</span>
                                    @else
                                        <span class="badge bg-danger">{{ __('inactive') }}</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-outline-warning mb-1"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editCustomerModal{{ $customer->CustomerID }}">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    <button type="button"
                                        class="btn btn-sm mb-1 btn-outline-{{ $customer->status == 1 ? 'danger' : 'success' }}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#statusCustomerModal{{ $customer->CustomerID }}"
                                        title="{{ __('status') }}">
                                        <i class="fas fa-{{ $customer->status == 1 ? 'ban' : 'check-circle' }} "></i>
                                    </button>

                                    {{-- edit status modal --}}
                                    <div class="modal fade" id="statusCustomerModal{{ $customer->CustomerID }}"
                                        tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-sm modal-dialog-centered text-start">
                                            <div class="modal-content border-0 shadow-lg">
                                                <div class="modal-header ">
                                                    <h6 class="modal-title fw-bold"><i
                                                            class="fas fa-exchange-alt me-2"></i>
                                                        {{ __('confirm_status_change') }}</h6>
                                                    <button type="button" class="btn-close"
                                                        data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body text-center mt-3">
                                                    <p class="mb-2">{{ __('confirm_message') }}
                                                        <b>{{ $customer->Name }}</b>
                                                    </p>
                                                    <p>{{ __('status') }}: <span
                                                            class="badge bg-{{ $customer->status == 1 ? 'danger' : 'success' }} fs-6">{{ $customer->status == 1 ? __('inactive') : __('active') }}</span>
                                                        ?</p>
                                                </div>
                                                <div class="modal-footer bg-light justify-content-center">
                                                    <form
                                                        action="{{ route('customers.destroy', $customer->CustomerID) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button"
                                                            class="btn btn-outline-secondary btn-sm"
                                                            data-bs-dismiss="modal">{{ __('no_cancel') }}</button>
                                                        <button type="submit"
                                                            class="btn btn-outline-{{ $customer->status == 1 ? 'danger' : 'success' }} btn-sm fw-bold">{{ __('yes_agree') }}</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            {{-- edit customer modal --}}
                            <div class="modal fade" id="editCustomerModal{{ $customer->CustomerID }}" tabindex="-1"
                                aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content border-0 shadow text-start">
                                        <div class="modal-header">
                                            <h5 class="modal-title text-dark fw-bold"><i
                                                    class="fas fa-user-edit me-2"></i> {{ __('edit_customer') }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('customers.update', $customer->CustomerID) }}"
                                            method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">{{ __('customer_name') }} <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" name="name" class="form-control"
                                                        value="{{ $customer->Name }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">{{ __('phone_number') }} <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" name="phone" class="form-control"
                                                        value="{{ $customer->PhoneNumber }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">{{ __('status') }}</label>
                                                    <select name="status" class="form-select">
                                                        <option value="1"
                                                            {{ $customer->status == 1 ? 'selected' : '' }}>{{ __('active') }}
                                                        </option>
                                                        <option value="0"
                                                            {{ $customer->status == 0 ? 'selected' : '' }}>{{ __('inactive') }}</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">{{ __('address') }}</label>
                                                    <textarea name="address" class="form-control" rows="2">{{ $customer->Address }}</textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer bg-light">
                                                <button type="button" class="btn btn-outline-secondary"
                                                    data-bs-dismiss="modal">{{ __('close') }}</button>
                                                <button type="submit"
                                                    class="btn btn-outline-success fw-bold">{{ __('update') }}</button>
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


    {{-- add modal --}}
    <div class="modal fade" id="addCustomerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-outline-success text-dark">
                    <h5 class="modal-title fw-bold"><i class="fas fa-user-plus me-2"></i> {{ __('add_new_customer') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form action="{{ route('customers.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('customer_name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('phone_number') }} <span class="text-danger">*</span></label>
                            <input type="text" name="phone" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('status') }}</label>
                            <select name="status" class="form-select">
                                <option value="1" selected>{{ __('active') }}</option>
                                <option value="0">{{ __('inactive') }}</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('address') }}</label>
                            <textarea name="address" class="form-control" rows="2" placeholder=""></textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('close') }}</button>
                        <button type="submit" class="btn btn-outline-success fw-bold">{{ __('save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
