@extends('layouts.app')

@section('title', __('user_manage_title'))

@section('content')
    @include('partials.alerts')

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white border-bottom pt-3 pb-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-users text-primary me-2"></i>{{ __('user_list') }}</h5>
            <button type="button" class="btn btn-primary btn-sm fw-medium px-3 shadow-sm" data-bs-toggle="modal"
                data-bs-target="#addUserModal">
                <i class="fas fa-user-plus me-1"></i> {{ __('add_user') }}
            </button>
        </div>

        <div class="card-body bg-light rounded-bottom">
            <form action="{{ route('users.index') }}" method="GET"
                class="row g-2 align-items-center mb-4 bg-white p-2 rounded shadow-sm mx-0">
                <div class="col-12 col-md-5">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 bg-light"
                            placeholder="{{ __('search_placeholder') }}" value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-12 col-md-2">
                    <select name="role_id" class="form-select form-select-sm bg-light">
                        <option value="">{{ __('all_roles') }}</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->RoleID }}" {{ request('role_id') == $role->RoleID ? 'selected' : '' }}>
                                {{ __($role->RoleName) ?? 'Role ID: ' . $role->RoleID }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-2">
                    <select name="status" class="form-select form-select-sm bg-light">
                        <option value="">{{ __('all_status') }}</option>
                        <option value="Approved" {{ request('status') == 'Approved' ? 'selected' : '' }}>
                            {{ __('approved') }}</option>
                        <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>{{ __('pending') }}
                        </option>
                        <option value="Reject" {{ request('status') == 'Reject' ? 'selected' : '' }}>{{ __('reject') }}
                        </option>
                    </select>
                </div>

                @if (auth()->user() && auth()->user()->hasRole('Admin'))
                    <div class="col-12 col-md-2">
                        {{-- <label class="small text-muted mb-1 d-block">{{ __('recent_login_label') }}</label> --}}
                        <select name="recent_login" class="form-select form-select-sm bg-light">
                            <option value="">{{ __('recent_login_label') }}</option>
                            <option value="24h" {{ request('recent_login') == '24h' ? 'selected' : '' }}>
                                {{ __('recent_24h') }}</option>
                            <option value="7d" {{ request('recent_login') == '7d' ? 'selected' : '' }}>
                                {{ __('recent_7d') }}</option>
                            <option value="30d" {{ request('recent_login') == '30d' ? 'selected' : '' }}>
                                {{ __('recent_30d') }}</option>
                        </select>
                    </div>
                @endif

                <div class="col-12 col-md-1 d-flex gap-2 align-items-end">

                    <button type="submit" class="btn btn-sm btn-primary ">
                        <i class="fas fa-filter"></i>

                    </button>
                    @if (request()->has('search') || request()->has('role_id') || request()->has('status') || request()->has('recent_login'))
                        <a href="{{ route('users.index') }}" class="btn btn-sm btn-outline-secondary "
                            title="{{ __('clear_filter') }}">
                            <i class="fas fa-sync-alt"></i>
                        </a>
                    @endif

                </div>
                {{-- <div class="col-12 col-md-1 d-flex gap-2 align-items-end">

                </div> --}}
            </form>

            <div class="table-responsive bg-white rounded shadow-sm border border-light-subtle">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-secondary small text-uppercase">
                        <tr>
                            <th class="ps-3 py-3">{{ __('id') }}</th>
                            <th class="py-3">{{ __('name') }}</th>
                            <th class="py-3">{{ __('role') }}</th>
                            <th class="py-3">{{ __('status') }}</th>
                            <th class="py-3">{{ __('last_login') }}</th>
                            <th class="py-3">{{ __('last_logout') }}</th>
                            <th class="text-end pe-3 py-3">{{ __('actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse ($users as $row)
                            <tr>
                                <td class="ps-3 text-muted fw-medium">#{{ $row->UserID }}</td>
                                <td>
                                    <div class="d-flex align-items-center py-1">
                                        @if ($row->UserImage)
                                            <img src="{{ str_starts_with($row->UserImage, 'http') ? $row->UserImage : asset('storage/' . $row->UserImage) }}" alt="User Image"
                                                class="rounded-circle me-3 shadow-sm border border-primary-subtle user-avatar-sm object-fit-cover">
                                        @else
                                            <div
                                                class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3 shadow-sm border border-primary-subtle user-avatar-placeholder-sm fw-bold">
                                                {{ strtoupper(substr($row->Username, 0, 1)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <div class="fw-bold text-dark mb-1">{{ $row->Username }}</div>
                                            <div class="small text-muted">{{ $row->Email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if ($row->role)
                                        @php
                                            $roleTranslations = [
                                                'Admin' => 'Admin',
                                                'Manager' => __('manager'),
                                                'Staff' => __('staff'),
                                            ];

                                            $roleClasses = [
                                                'Admin' => 'bg-primary text-primary border-primary-subtle',
                                                'Manager' => 'bg-info text-info border-info-subtle',
                                                'Staff' => 'bg-secondary text-secondary border-secondary-subtle',
                                            ];

                                            $roleName = $row->role->RoleName;
                                        @endphp

                                        <span
                                            class="badge {{ $roleClasses[$roleName] ?? 'bg-dark text-dark border-dark-subtle' }} bg-opacity-10 border px-2 py-1">
                                            {{ $roleTranslations[$roleName] ?? $roleName }}
                                        </span>
                                    @else
                                        <span
                                            class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle px-2 py-1">{{ __('na') }}</span>
                                    @endif
                                </td>

                                <td>
                                    @if ($row->Status == 'Approved')
                                        <span
                                            class="badge bg-success bg-opacity-10 text-success border border-success-subtle px-2 py-1"><i
                                                class="fas fa-check-circle me-1"></i> {{ __('approved') }}</span>
                                    @else
                                        <span
                                            class="badge bg-warning bg-opacity-10 text-warning border border-warning-subtle px-2 py-1"><i
                                                class="fas fa-clock me-1"></i> {{ __('pending_approval') }}</span>
                                    @endif

                                    @if ($row->Status == 'Pending')
                                        <div class="ms-2 d-inline-block">
                                            <a href="{{ route('users.approve', $row->UserID) }}"
                                                class="btn btn-sm btn-light text-success border px-2 py-0"
                                                title="{{ __('approve') }}">
                                                <i class="fas fa-check"></i>
                                            </a>
                                            <a href="{{ route('users.reject', $row->UserID) }}"
                                                class="btn btn-sm btn-light text-danger border px-2 py-0"
                                                title="{{ __('reject') }}">
                                                <i class="fas fa-times"></i>
                                            </a>
                                        </div>
                                    @endif
                                </td>

                                <td class="text-center">
                                    @php
                                        $last = $row->last_login_at ? \Carbon\Carbon::parse($row->last_login_at) : null;
                                        $recency = 'muted';
                                        if ($last) {
                                            $days = $last->diffInDays();
                                            if ($days <= 1) {
                                                $recency = 'success';
                                            } elseif ($days <= 7) {
                                                $recency = 'warning';
                                            } else {
                                                $recency = 'secondary';
                                            }
                                        }
                                    @endphp
                                    <div class="small">
                                        @if ($last)
                                            {{-- <span
                                                class="badge bg-{{ $recency }} bg-opacity-10 text-{{ $recency }} me-1">&bull;</span> --}}
                                            <span class="text-muted">{{ $last->format('d-M-Y H:i A') }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </div>
                                    {{-- @if (auth()->user() && auth()->user()->hasRole('Admin') && $row->last_login_ip)
                                        <div class="small text-muted">{{ $row->last_login_ip }}</div>
                                    @endif --}}
                                </td>

                                <td class="text-center">
                                    <div class="small text-muted">
                                        {{ $row->last_logout_at ? \Carbon\Carbon::parse($row->last_logout_at)->format('d-M-Y H:i A') : '-' }}
                                    </div>
                                </td>

                                <td class="text-end pe-3">
                                    <div class="btn-group shadow-sm">
                                        {{-- if auth is manager and is an admin btn edit is disabled --}}
                                        @if (auth()->user()->hasRole('Manager') && $row->hasRole('Admin'))
                                            <button type="button" class="btn btn-sm btn-light text-warning border"
                                                disabled>
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        @else
                                            <button type="button" class="btn btn-sm btn-light text-warning border"
                                                data-bs-toggle="modal" data-bs-target="#editUserModal{{ $row->UserID }}">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        @endif

                                        {{-- if auth is manager and is an admin btn delete is disabled --}}
                                        @if (auth()->user()->hasRole('Manager') && $row->hasRole('Admin'))
                                            <button type="button" class="btn btn-sm btn-light text-danger border"
                                                disabled>
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @elseif ($row->orders_count > 0)
                                            <button type="button" class="btn btn-sm btn-light text-danger border"
                                                disabled title="{{ __('users.cannot_delete_with_orders') }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @else
                                            <button type="button" class="btn btn-sm btn-light text-danger border"
                                                onclick="deleteUser({{ $row->UserID }})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            {{-- {{-- update modal}} --}}
                            <div class="modal fade" id="editUserModal{{ $row->UserID }}" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content border-0 shadow">
                                        <div class="modal-header bg-light border-bottom-0">
                                            <h5 class="modal-title fw-bold text-dark"><i
                                                    class="fas fa-user-edit text-primary me-2"></i>{{ __('edit_user') }} -
                                                {{ $row->Username }}
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="{{ route('users.update') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $row->UserID }}">
                                            <div class="modal-body p-4">
                                                <div class="row g-3">
                                                    <div class="col-12 col-md-6">
                                                        <label
                                                            class="form-label small fw-bold text-muted">{{ __('name') }}</label>
                                                        <input type="text" class="form-control" name="name"
                                                            value="{{ $row->Username }}">
                                                    </div>

                                                    <div class="col-12 col-md-6">
                                                        <label
                                                            class="form-label small fw-bold text-muted">{{ __('email') }}</label>
                                                        <input type="email" class="form-control bg-light"
                                                            name="Email" value="{{ $row->Email }}" readonly>
                                                    </div>

                                                    <div class="col-12 col-md-6">
                                                        <label
                                                            class="form-label small fw-bold text-primary">{{ __('role') }}</label>

                                                        {{-- <select class="form-select border-primary bg-primary bg-opacity-10"
                                                            name="role_id">
                                                            @foreach ($roles as $role)
                                                                <option value="{{ $role->RoleID }}"
                                                                    {{ $role->RoleID == $row->RoleID ? 'selected' : '' }}>
                                                                    {{ __($role->RoleName) }}
                                                                </option>
                                                            @endforeach
                                                        </select> --}}

                                                        @if (Auth::user()->RoleID != 1)
                                                            <select
                                                                class="form-select border-primary bg-primary bg-opacity-10"
                                                                name="role_id" required>
                                                                <option value="{{ $row->RoleID }}">
                                                                    {{ __('choose_role') }}</option>
                                                                @foreach ($roles as $role)
                                                                    @if ($role->RoleID != 1)
                                                                        <option value="{{ $role->RoleID }}">
                                                                            {{ __($role->RoleName) }}</option>
                                                                    @endif
                                                                @endforeach
                                                            </select>
                                                        @else
                                                            <select
                                                                class="form-select border-primary bg-primary bg-opacity-10"
                                                                name="role_id" required>
                                                                <option value="{{ $row->RoleID }}">
                                                                    {{ __('choose_role') }}</option>
                                                                @foreach ($roles as $role)
                                                                    <option value="{{ $role->RoleID }}">
                                                                        {{ __($role->RoleName) }}</option>
                                                                @endforeach
                                                            </select>
                                                        @endif
                                                    </div>

                                                    <div class="col-12 col-md-6">
                                                        <label
                                                            class="form-label small fw-bold text-muted">{{ __('new_password') }}
                                                            <small
                                                                class="text-muted fw-normal">({{ __('optional') }})</small></label>
                                                        <div class="input-group">
                                                            <input type="password" class="form-control border-end-0"
                                                                name="password" id="edit-password-{{ $row->UserID }}"
                                                                placeholder="{{ __('leave_blank') }}">
                                                            <span class="input-group-text bg-white cursor-pointer"
                                                                onclick="toggleField('edit-password-{{ $row->UserID }}', 'edit-pwd-icon-{{ $row->UserID }}')">
                                                                <i class="fas fa-eye text-muted"
                                                                    id="edit-pwd-icon-{{ $row->UserID }}"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer bg-light border-top-0">
                                                <button type="button" class="btn btn-outline-secondary fw-bold px-4"
                                                    data-bs-dismiss="modal">{{ __('cancel') }}</button>
                                                <button type="submit"
                                                    class="btn btn-primary fw-bold px-4">{{ __('update_user') }}</button>
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
                                        <h5 class="fw-medium text-dark">{{ __('no_records_found') ?? 'No users found.' }}
                                        </h5>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{-- User pagination if exists --}}
                @if (method_exists($users, 'links'))
                    <div class="d-flex justify-content-start mt-3">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- add modal --}}
    <div class="modal fade" id="addUserModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light border-bottom-0">
                    <h5 class="modal-title fw-bold text-dark"><i
                            class="fas fa-user-plus text-primary me-2"></i>{{ __('add_user') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="addUserForm">
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-bold text-muted">{{ __('name') }} <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="add_name" required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-bold text-muted">{{ __('email') }} <span
                                        class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="add_email" required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-bold text-muted">{{ __('new_password') }} <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control border-end-0" id="add_password" required>
                                    <span class="input-group-text bg-white"
                                        onclick="toggleField('add_password', 'add-pwd-icon')" class="cursor-pointer">
                                        <i class="fas fa-eye text-muted" id="add-pwd-icon"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-bold text-muted">{{ __('confirm_password') }} <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control border-end-0"
                                        id="add_password_confirmation" required>
                                    <span class="input-group-text bg-white"
                                        onclick="toggleField('add_password_confirmation', 'add-pwd-confirm-icon')"
                                        class="cursor-pointer">
                                        <i class="fas fa-eye text-muted" id="add-pwd-confirm-icon"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold text-primary">{{ __('role') }} <span
                                        class="text-danger">*</span></label>

                                {{-- if auth is manager they cannot add an admin or set role to admin --}}
                                @if (Auth::user()->RoleID != 1)
                                    <select class="form-select border-primary bg-primary bg-opacity-10" id="add_role"
                                        required>
                                        <option value="">{{ __('choose_role') }}</option>
                                        @foreach ($roles as $role)
                                            @if ($role->RoleID != 1)
                                                <option value="{{ $role->RoleID }}">{{ __($role->RoleName) }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                @else
                                    <select class="form-select border-primary bg-primary bg-opacity-10" id="add_role"
                                        required>
                                        <option value="">{{ __('choose_role') }}</option>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->RoleID }}">{{ __($role->RoleName) }}</option>
                                        @endforeach
                                    </select>
                                @endif
                                {{-- <select class="form-select border-primary bg-primary bg-opacity-10" id="add_role"
                                    required>
                                    <option value="">{{ __('choose_role') }}</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->RoleID }}">{{ __($role->RoleName) }}</option>
                                    @endforeach
                                </select> --}}
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer bg-light border-top-0">
                    <button type="button" class="btn btn-outline-secondary fw-bold px-4"
                        data-bs-dismiss="modal">{{ __('cancel') }}</button>
                    <button type="button"
                        class="btn btn-primary fw-bold px-4 btn_save_user">{{ __('save_user') }}</button>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script>
            window.usersPageConfig = {
                routes: {
                    store: "{{ route('users.store') }}",
                    delete: "{{ route('users.delete') }}"
                },
                messages: {
                    validationError: "{{ __('validation_error') }}",
                    success: "{{ __('success') }}",
                    successAdd: "{{ __('success_add') }}",
                    error: "{{ __('pos.error') }}",
                    confirmDelete: "{{ __('confirm_delete') }}",
                    deleteWarning: "{{ __('delete_warning') }}",
                    deleteConfirmBtn: "{{ __('delete_btn_confirm') }}",
                    cancelBtn: "{{ __('cancel_btn') }}",
                    deletedMsg: "{{ __('users.msg_deleted') }}"
                }
            };
        </script>
        <script defer src="{{ asset('js/pages/users-index.js') }}"></script>
    @endpush

@endsection
