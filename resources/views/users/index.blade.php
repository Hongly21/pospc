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
            <form action="{{ route('users.index') }}" method="GET" class="row g-2 align-items-center mb-4 bg-white p-2 rounded shadow-sm mx-0">
                <div class="col-12 col-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 bg-light"
                            placeholder="{{ __('search_placeholder') }}" value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-12 col-md-3">
                    <select name="role_id" class="form-select form-select-sm bg-light">
                        <option value="">{{ __('all_roles') }}</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->RoleID }}" {{ request('role_id') == $role->RoleID ? 'selected' : '' }}>
                                {{ __($role->RoleName) ?? 'Role ID: ' . $role->RoleID }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-3">
                    <select name="status" class="form-select form-select-sm bg-light">
                        <option value="">{{ __('all_status') }}</option>
                        <option value="Approved" {{ request('status') == 'Approved' ? 'selected' : '' }}>{{ __('approved') }}</option>
                        <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>{{ __('pending') }}</option>
                        <option value="Reject" {{ request('status') == 'Reject' ? 'selected' : '' }}>{{ __('reject') }}</option>
                    </select>
                </div>

                <div class="col-12 col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary px-4 w-100">{{ __('filter') }}</button>
                    @if (request()->has('search') || request()->has('role_id') || request()->has('status'))
                        <a href="{{ route('users.index') }}" class="btn btn-sm btn-outline-secondary px-3" title="{{ __('clear_filter') }}">
                            <i class="fas fa-sync-alt"></i>
                        </a>
                    @endif
                </div>
            </form>

            <div class="table-responsive bg-white rounded shadow-sm border border-light-subtle">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-secondary small text-uppercase">
                        <tr>
                            <th class="ps-3 py-3">{{ __('id') }}</th>
                            <th class="py-3">{{ __('name') }}</th>
                            <th class="py-3">{{ __('role') }}</th>
                            <th class="py-3">{{ __('status') }}</th>
                            <th class="text-end pe-3 py-3">{{ __('actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse ($users as $row)
                            <tr>
                                <td class="ps-3 text-muted fw-medium">#{{ $row->UserID }}</td>
                                <td>
                                    <div class="d-flex align-items-center py-1">
                                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3 shadow-sm border border-primary-subtle"
                                            style="width: 40px; height: 40px; font-weight:bold;">
                                            {{ strtoupper(substr($row->Username, 0, 1)) }}
                                        </div>
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

                                        <span class="badge {{ $roleClasses[$roleName] ?? 'bg-dark text-dark border-dark-subtle' }} bg-opacity-10 border px-2 py-1">
                                            {{ $roleTranslations[$roleName] ?? $roleName }}
                                        </span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle px-2 py-1">{{ __('na') }}</span>
                                    @endif
                                </td>

                                <td>
                                    @if ($row->Status == 'Approved')
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle px-2 py-1"><i class="fas fa-check-circle me-1"></i> {{ __('approved') }}</span>
                                    @else
                                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning-subtle px-2 py-1"><i class="fas fa-clock me-1"></i> {{ __('pending') }}</span>
                                    @endif
                                    
                                    @if ($row->Status == 'Pending')
                                        <div class="ms-2 d-inline-block">
                                            <a href="{{ route('users.approve', $row->UserID) }}"
                                                class="btn btn-sm btn-light text-success border px-2 py-0" title="{{ __('approve') }}">
                                                <i class="fas fa-check"></i>
                                            </a>
                                            <a href="{{ route('users.reject', $row->UserID) }}"
                                                class="btn btn-sm btn-light text-danger border px-2 py-0" title="{{ __('reject') }}">
                                                <i class="fas fa-times"></i>
                                            </a>
                                        </div>
                                    @endif
                                </td>

                                <td class="text-end pe-3">
                                    <div class="btn-group shadow-sm">
                                        <button type="button" class="btn btn-sm btn-light text-warning border" data-bs-toggle="modal"
                                            data-bs-target="#editUserModal{{ $row->UserID }}">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        <button type="button" class="btn btn-sm btn-light text-danger border"
                                            onclick="deleteUser({{ $row->UserID }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            {{-- {{-- update modal}} --}}
                            <div class="modal fade" id="editUserModal{{ $row->UserID }}" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content border-0 shadow">
                                        <div class="modal-header bg-light border-bottom-0">
                                            <h5 class="modal-title fw-bold text-dark"><i class="fas fa-user-edit text-primary me-2"></i>{{ __('edit_user') }} - {{ $row->Username }}
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="{{ route('users.update') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $row->UserID }}">
                                        <div class="modal-body p-4">
                                            <div class="row g-3">
                                                <div class="col-12 col-md-6">
                                                    <label class="form-label small fw-bold text-muted">{{ __('name') }}</label>
                                                    <input type="text" class="form-control" name="name"
                                                        value="{{ $row->Username }}">
                                                </div>

                                                <div class="col-12 col-md-6">
                                                    <label class="form-label small fw-bold text-muted">{{ __('email') }}</label>
                                                    <input type="email" class="form-control bg-light" name="Email"
                                                        value="{{ $row->Email }}" readonly>
                                                </div>

                                                <div class="col-12 col-md-6">
                                                    <label class="form-label small fw-bold text-primary">{{ __('role') }}</label>
                                                    <select class="form-select border-primary bg-primary bg-opacity-10" name="role_id">
                                                        @foreach ($roles as $role)
                                                            <option value="{{ $role->RoleID }}"
                                                                {{ $role->RoleID == $row->RoleID ? 'selected' : '' }}>
                                                                {{ __($role->RoleName) }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-12 col-md-6">
                                                    <label class="form-label small fw-bold text-muted">{{ __('new_password') }} <small
                                                            class="text-muted fw-normal">({{ __('optional') }})</small></label>
                                                    <div class="input-group">
                                                        <input type="password" class="form-control border-end-0" name="password"
                                                            id="edit-password-{{ $row->UserID }}" placeholder="{{ __('leave_blank') }}">
                                                        <span class="input-group-text bg-white" onclick="toggleField('edit-password-{{ $row->UserID }}', 'edit-pwd-icon-{{ $row->UserID }}')" style="cursor: pointer;">
                                                            <i class="fas fa-eye text-muted" id="edit-pwd-icon-{{ $row->UserID }}"></i>
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
                                <td colspan="5" class="text-center py-5 text-muted bg-white">
                                    <div class="d-flex flex-column align-items-center justify-content-center py-4">
                                        <i class="fas fa-users fa-3x mb-3 text-secondary opacity-50"></i>
                                        <h5 class="fw-medium text-dark">{{ __('no_records_found') ?? 'No users found.' }}</h5>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                {{-- User pagination if exists --}}
                @if(method_exists($users, 'links'))
                    <div class="d-flex justify-content-end mt-3">
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
                    <h5 class="modal-title fw-bold text-dark"><i class="fas fa-user-plus text-primary me-2"></i>{{ __('add_user') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="addUserForm">
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-bold text-muted">{{ __('name') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="add_name" required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-bold text-muted">{{ __('email') }} <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="add_email" required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-bold text-muted">{{ __('new_password') }} <span
                                        class="text-danger">*</span></label>
                                <div class="form-text small mt-0 mb-1 text-muted">{{ __('password_requirements') }}</div>
                                <div class="input-group">
                                    <input type="password" class="form-control border-end-0" id="add_password" required>
                                    <span class="input-group-text bg-white"
                                        onclick="toggleField('add_password', 'add-pwd-icon')" style="cursor: pointer;">
                                        <i class="fas fa-eye text-muted" id="add-pwd-icon"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-bold text-muted">{{ __('confirm_password') }} <span
                                        class="text-danger">*</span></label>
                                <div class="form-text small mt-0 mb-1 text-muted">&nbsp;</div>
                                <div class="input-group">
                                    <input type="password" class="form-control border-end-0"
                                        id="add_password_confirmation" required>
                                    <span class="input-group-text bg-white"
                                        onclick="toggleField('add_password_confirmation', 'add-pwd-confirm-icon')"
                                        style="cursor: pointer;">
                                        <i class="fas fa-eye text-muted" id="add-pwd-confirm-icon"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold text-primary">{{ __('role') }} <span class="text-danger">*</span></label>
                                <select class="form-select border-primary bg-primary bg-opacity-10" id="add_role" required>
                                    <option value="">{{ __('choose_role') }}</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->RoleID }}">{{ __($role->RoleName) }}</option>
                                    @endforeach
                                </select>
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
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $(document).ready(function() {
            $('.edit-btn').click(function() {
                var id = $(this).data('id');
                var name = $(this).data('name');
                var email = $(this).data('email');
                var roleID = $(this).data('roleid');

                $('#update_id').val(id);
                $('#update_name').val(name);
                $('#update_email').val(email);
                $('#update_role').val(roleID);
            });

            $('.btn_save_user').click(function() {
                var data = {
                    name: $('#add_name').val(),
                    email: $('#add_email').val(),
                    password: $('#add_password').val(),
                    password_confirmation: $('#add_password_confirmation').val(),
                    role: $('#add_role').val()
                };

                $.post("{{ route('users.store') }}", data, function(res) {
                    if (res.status == 'success' || res == 'success') {
                        // Using translation keys for "Success" and the message
                        Swal.fire("{{ __('success') }}", "{{ __('success_add') }}", 'success')
                            .then(
                                () => location.reload()
                            );
                    }
                }).fail(function(xhr) {
                    let errors = xhr.responseJSON.errors;
                    let errorMsg = '';
                    $.each(errors, function(key, value) {
                        errorMsg += value[0] + '<br>';
                    });
                    Swal.fire({
                        icon: 'error',
                        title: "{{ __('validation_error') }}",
                        html: errorMsg,
                    });
                });
            });

            window.deleteUser = function(id) {
                Swal.fire({
                    title: "{{ __('confirm_delete') }}",
                    text: "{{ __('delete_warning') }}",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: "{{ __('delete_btn_confirm') }}",
                    cancelButtonText: "{{ __('cancel_btn') }}"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.post("{{ route('users.delete') }}", {
                            id: id
                        }, function(res) {
                            Swal.fire("{{ __('users.msg_deleted') }}",
                                    "{{ __('users.msg_deleted') }}", 'success')
                                .then(() => location.reload());
                        });
                    }
                });
            }

            window.toggleField = function(inputId, iconId) {
                const input = document.getElementById(inputId);
                const icon = document.getElementById(iconId);
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            }
        });
    </script>

@endsection
