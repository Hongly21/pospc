@extends('layouts.app')
@section('title', __('customer_information'))
@section('content')
    @include('partials.alerts')

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white border-bottom pt-3 pb-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-users text-primary me-2"></i>{{ __('customer_information') }}</h5>
            <button type="button" class="btn btn-primary btn-sm fw-medium px-3 shadow-sm" data-bs-toggle="modal"
                data-bs-target="#addCustomerModal">
                <i class="fas fa-plus me-1"></i> {{ __('add_new_customer') }}
            </button>
        </div>
        <div class="card-body bg-light rounded-bottom">
            <form action="{{ route('customers.index') }}" method="GET" class="row g-2 align-items-center mb-4 bg-white p-2 rounded shadow-sm mx-0">
                <div class="col-12 col-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 bg-light"
                            placeholder="{{ __('search_placeholder') }}" value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <select name="status" class="form-select form-select-sm bg-light">
                        <option value="">{{ __('account_status') }} ({{ __('all') }})</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>{{ __('active') }}</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>{{ __('inactive') }}</option>
                    </select>
                </div>

                <div class="col-12 col-md-3">
                    <select name="debt_status" class="form-select form-select-sm bg-light">
                        <option value="">{{ __('debt_status') }} ({{ __('all') }})</option>
                        <option value="Paid" {{ request('debt_status') == 'Paid' ? 'selected' : '' }}>{{ __('paid') }}</option>
                        <option value="Debt" {{ request('debt_status') == 'Debt' ? 'selected' : '' }}>{{ __('has_debt') }}</option>
                    </select>
                </div>

                <div class="col-12 col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary px-4 w-100">{{ __('search_button') }}</button>
                    @if (request()->has('search') || request()->has('status') || request()->has('debt_status'))
                        <a href="{{ route('customers.index') }}" class="btn btn-sm btn-outline-secondary px-3">
                            <i class="fas fa-sync-alt"></i>
                        </a>
                    @endif
                </div>
            </form>

            <div class="table-responsive bg-white rounded shadow-sm border border-light-subtle">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-secondary small text-uppercase">
                        <tr>
                            <th class="ps-3 py-3">ID</th>
                            <th class="py-3">{{ __('customer_name') }}</th>
                            <th class="py-3">{{ __('phone_number') }}</th>
                            <th class="text-center py-3">{{ __('points') }}</th>
                            <th class="text-end pe-4 py-3">{{ __('debt') }}</th>
                            <th class="text-center py-3">{{ __('status') }}</th>
                            <th class="text-end pe-3 py-3">{{ __('action') }}</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse ($customers as $customer)
                            <tr>
                                <td class="ps-3 text-muted fw-medium">#{{ $customer->CustomerID }}</td>
                                <td class="fw-bold text-dark">{{ $customer->Name }}</td>
                                <td>{{ $customer->PhoneNumber ?? 'N/A' }}</td>
                                <td class="text-center">
                                    <span class="badge bg-info bg-opacity-10 text-info border border-info-subtle px-2 py-1">{{ $customer->Points ?? 0 }}</span>
                                </td>

                                <td class="text-end pe-4 fw-medium">
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
                                        <span class="text-danger">${{ number_format($debt, 2) }}</span>
                                    @else
                                        <span class="text-success">$0.00</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($customer->status == 1)
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle px-2 py-1"><i class="fas fa-check-circle me-1"></i> {{ __('active') }}</span>
                                    @else
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle px-2 py-1"><i class="fas fa-times-circle me-1"></i> {{ __('inactive') }}</span>
                                    @endif
                                </td>
                                <td class="text-end pe-3">
                                    <div class="btn-group shadow-sm">
                                        <button type="button" class="btn btn-sm btn-light text-warning border"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editCustomerModal{{ $customer->CustomerID }}">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        <button type="button"
                                            class="btn btn-sm border btn-light text-{{ $customer->status == 1 ? 'danger' : 'success' }}"
                                            data-bs-toggle="modal"
                                            data-bs-target="#statusCustomerModal{{ $customer->CustomerID }}"
                                            title="{{ __('status') }}">
                                            <i class="fas fa-{{ $customer->status == 1 ? 'ban' : 'check-circle' }}"></i>
                                        </button>
                                    </div>

                                    {{-- edit status modal --}}
                                    <div class="modal fade" id="statusCustomerModal{{ $customer->CustomerID }}"
                                        tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-sm modal-dialog-centered text-start">
                                            <div class="modal-content border-0 shadow">
                                                <div class="modal-header bg-light border-bottom-0">
                                                    <h6 class="modal-title fw-bold text-dark"><i
                                                            class="fas fa-exchange-alt text-primary me-2"></i>
                                                        {{ __('confirm_status_change') }}</h6>
                                                    <button type="button" class="btn-close"
                                                        data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body p-4 text-center mt-3">
                                                    <p class="mb-2 text-muted">{{ __('confirm_message') }}
                                                        <b class="text-dark">{{ $customer->Name }}</b>
                                                    </p>
                                                    <p class="small fw-bold text-muted">{{ __('status') }}: <span
                                                            class="badge bg-{{ $customer->status == 1 ? 'danger' : 'success' }} bg-opacity-10 text-{{ $customer->status == 1 ? 'danger' : 'success' }} border border-{{ $customer->status == 1 ? 'danger' : 'success' }}-subtle px-2 py-1 fs-6">{{ $customer->status == 1 ? __('inactive') : __('active') }}</span>
                                                        ?</p>
                                                </div>
                                                <div class="modal-footer bg-light border-top-0 justify-content-center">
                                                    <form
                                                        action="{{ route('customers.destroy', $customer->CustomerID) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button"
                                                            class="btn btn-outline-secondary fw-bold px-4"
                                                            data-bs-dismiss="modal">{{ __('no_cancel') }}</button>
                                                        <button type="submit"
                                                            class="btn btn-{{ $customer->status == 1 ? 'danger' : 'success' }} fw-bold px-4">{{ __('yes_agree') }}</button>
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
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content border-0 shadow text-start">
                                        <div class="modal-header bg-light border-bottom-0">
                                            <h5 class="modal-title text-dark fw-bold"><i
                                                    class="fas fa-user-edit text-primary me-2"></i> {{ __('edit_customer') }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('customers.update', $customer->CustomerID) }}"
                                            method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body p-4">
                                                <div class="row g-3">
                                                    <div class="col-12 col-md-6">
                                                        <label class="form-label small fw-bold text-muted">{{ __('customer_name') }} <span
                                                                class="text-danger">*</span></label>
                                                        <input type="text" name="name" class="form-control"
                                                            value="{{ $customer->Name }}" required>
                                                    </div>
                                                    <div class="col-12 col-md-6">
                                                        <label class="form-label small fw-bold text-muted">{{ __('phone_number') }} <span
                                                                class="text-danger">*</span></label>
                                                        <input type="text" name="phone" class="form-control"
                                                            value="{{ $customer->PhoneNumber }}" required>
                                                    </div>
                                                    <div class="col-12 col-md-12">
                                                        <label class="form-label small fw-bold text-primary">{{ __('status') }}</label>
                                                        <select name="status" class="form-select border-primary bg-primary bg-opacity-10">
                                                            <option value="1"
                                                                {{ $customer->status == 1 ? 'selected' : '' }}>{{ __('active') }}
                                                            </option>
                                                            <option value="0"
                                                                {{ $customer->status == 0 ? 'selected' : '' }}>{{ __('inactive') }}</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-12">
                                                        <label class="form-label small fw-bold text-muted">{{ __('address') }}</label>
                                                        <textarea name="address" class="form-control" rows="2">{{ $customer->Address }}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer bg-light border-top-0">
                                                <button type="button" class="btn btn-outline-secondary fw-bold px-4"
                                                    data-bs-dismiss="modal">{{ __('close') }}</button>
                                                <button type="submit"
                                                    class="btn btn-primary fw-bold px-4">{{ __('update') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted bg-white">
                                    <div class="d-flex flex-column align-items-center justify-content-center py-4">
                                        <i class="fas fa-users fa-3x mb-3 text-secondary opacity-50"></i>
                                        <h5 class="fw-medium text-dark">{{ __('no_data') ?? 'No customers found.' }}</h5>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4 d-flex justify-content-end">
                {{ $customers->appends(request()->query())->links() }}
            </div>
        </div>
    </div>


    {{-- add modal --}}
    <div class="modal fade" id="addCustomerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light border-bottom-0">
                    <h5 class="modal-title fw-bold text-dark"><i class="fas fa-user-plus text-primary me-2"></i> {{ __('add_new_customer') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form action="{{ route('customers.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-bold text-muted">{{ __('customer_name') }} <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-bold text-muted">{{ __('phone_number') }} <span class="text-danger">*</span></label>
                                <input type="text" name="phone" class="form-control" required>
                            </div>
                            <div class="col-12 col-md-12">
                                <label class="form-label small fw-bold text-primary">{{ __('status') }}</label>
                                <select name="status" class="form-select border-primary bg-primary bg-opacity-10">
                                    <option value="1" selected>{{ __('active') }}</option>
                                    <option value="0">{{ __('inactive') }}</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold text-muted">{{ __('address') }}</label>
                                <textarea name="address" class="form-control" rows="2" placeholder=""></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-top-0">
                        <button type="button" class="btn btn-outline-secondary fw-bold px-4" data-bs-dismiss="modal">{{ __('close') }}</button>
                        <button type="submit" class="btn btn-primary fw-bold px-4">{{ __('save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
