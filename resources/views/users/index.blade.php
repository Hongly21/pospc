@extends('layouts.app')

@section('title', __('user_manage_title'))

@section('content')
    @include('partials.alerts')
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center bg-white">
            <h6 class="m-0 fw-bold text-primary"><i class="fas fa-users me-2"></i> {{ __('user_list') }}</h6>
            <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal"
                data-bs-target="#addUserModal">
                <i class="fas fa-user-plus me-1"></i> {{ __('add_user') }}
            </button>
        </div>
        <div class="card-body">
            <form action="{{ route('users.index') }}" method="GET" class="row w-100 g-2 mb-4">

                <div class="col-12 col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control"
                            placeholder="{{ __('search_placeholder') }}" value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-12 col-md-3">
                    <select name="role_id" class="form-select">
                        <option value="">{{ __('all_roles') }}</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->RoleID }}" {{ request('role_id') == $role->RoleID ? 'selected' : '' }}>
                                {{ $role->RoleName ?? 'Role ID: ' . $role->RoleID }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-3">
                    <select name="status" class="form-select border-primary text-primary">
                        <option value="" class="text-dark">{{ __('all_status') }}</option>
                        <option value="Approved" {{ request('status') == 'Approved' ? 'selected' : '' }}
                            class="text-success">{{ __('approved') }}</option>
                        <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}
                            class="text-warning">
                            {{ __('pending') }}</option>
                        <option value="Reject" {{ request('status') == 'Reject' ? 'selected' : '' }} class="text-danger">
                            {{ __('reject') }}</option>
                    </select>
                </div>

                <div class="col-12 col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-outline-primary flex-grow-1 "><i class="fas fa-filter"></i>
                        {{ __('filter') }}</button>
                    <a href="{{ route('users.index') }}" class="btn btn-outline-danger px-3" title="Clear Filter"><i
                            class="fas fa-sync-alt"></i></a>
                </div>
            </form>
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('id') }}</th>
                            <th>{{ __('name') }}</th>
                            <th>{{ __('role') }}</th>
                            <th>{{ __('status') }}</th>
                            <th class="text-end">{{ __('actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $row)
                            <tr>
                                <td>{{ $row->UserID }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-3"
                                            style="width: 40px; height: 40px; font-weight:bold; color:#4e73df;">
                                            {{ strtoupper(substr($row->Username, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $row->Username }}</div>
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
                                                'Admin' => 'bg-primary',
                                                'Manager' => 'bg-info text-dark',
                                                'Staff' => 'bg-secondary',
                                            ];

                                            $roleName = $row->role->RoleName;
                                        @endphp

                                        <span
                                            class="badge rounded-pill px-3 py-2 {{ $roleClasses[$roleName] ?? 'bg-dark' }}">
                                            {{ $roleTranslations[$roleName] ?? $roleName }}
                                        </span>
                                    @else
                                        <span class="badge bg-danger">{{ __('na') }}</span>
                                    @endif
                                </td>

                                <td>
                                    @if ($row->Status == 'Approved')
                                        <span
                                            class="badge bg-success bg-opacity-10 text-success px-3">{{ __('approved') }}</span>
                                    @else
                                        <span class="badge bg-warning bg-opacity-10 text-warning px-3">{{ __('pending') }}
                                        </span>
                                    @endif
                                    @if ($row->Status == 'Pending')
                                        <a href="{{ route('users.approve', $row->UserID) }}"
                                            class="btn btn-sm btn-success me-1" title="Approve">
                                            <i class="fas fa-check"></i>
                                        </a>
                                        <a href="{{ route('users.reject', $row->UserID) }}"
                                            class="btn btn-sm btn-warning text-dark me-1" title="Reject">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    @endif
                                </td>

                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-warning text-yellow mb-1" data-bs-toggle="modal"
                                        data-bs-target="#editUserModal{{ $row->UserID }}">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    <button class="btn btn-sm btn-outline-danger mb-1"
                                        onclick="deleteUser({{ $row->UserID }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            {{-- {{-- update modal}} --}}
                            <div class="modal fade" id="editUserModal{{ $row->UserID }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header  text-dark">
                                            <h5 class="modal-title fw-bold">{{ __('edit_user') }} - {{ $row->Username }}
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="{{ route('users.update') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $row->UserID }}">
                                        <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">{{ __('name') }}</label>
                                                    <input type="text" class="form-control" name="name"
                                                        value="{{ $row->Username }}">
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">{{ __('email') }}</label>
                                                    <input type="email" class="form-control" name="Email"
                                                        value="{{ $row->Email }}" readonly>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">{{ __('role') }}</label>
                                                    <select class="form-select" name="role_id">
                                                        @foreach ($roles as $role)
                                                            <option value="{{ $role->RoleID }}"
                                                                {{ $role->RoleID == $row->RoleID ? 'selected' : '' }}>
                                                                {{ $role->RoleName }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">{{ __('new_password') }} <small
                                                            class="text-muted">({{ __('optional') }})</small></label>
                                                    <div class="input-group">
                                                        <input type="password" class="form-control" name="password"
                                                            id="edit-password-{{ $row->UserID }}" placeholder="{{ __('leave_blank') }}">
                                                        <span class="input-group-text" onclick="toggleField('edit-password-{{ $row->UserID }}', 'edit-pwd-icon-{{ $row->UserID }}')" style="cursor: pointer; background: transparent; border-left: none;">
                                                            <i class="fas fa-eye" id="edit-pwd-icon-{{ $row->UserID }}"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-outline-secondary fw-bold"
                                                data-bs-dismiss="modal">{{ __('cancel') }}</button>
                                            <button type="submit"
                                                class="btn btn-outline-primary fw-bold">{{ __('update_user') }}</button>
                                        </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- add modal --}}
    <div class="modal fade" id="addUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header text-dark fw-bold">
                    <h5 class="modal-title fw-bold">{{ __('add_user') }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addUserForm">
                        <div class="mb-3">
                            <label class="form-label">{{ __('name') }}</label>
                            <input type="text" class="form-control" id="add_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('email') }}</label>
                            <input type="email" class="form-control" id="add_email" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('new_password') }}</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="add_password" required>
                                <span class="input-group-text" onclick="toggleField('add_password', 'add-pwd-icon')" style="cursor: pointer; background: transparent; border-left: none;">
                                    <i class="fas fa-eye" id="add-pwd-icon"></i>
                                </span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('role') }}</label>
                            <select class="form-select" id="add_role">
                                <option value="">{{ __('choose_role') }}</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->RoleID }}">{{ $role->RoleName }}</option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary fw-bold"
                        data-bs-dismiss="modal">{{ __('cancel') }}</button>
                    <button type="button"
                        class="btn btn-outline-success fw-bold btn_save_user">{{ __('save_user') }}</button>
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
                    role: $('#add_role').val()
                };

                $.post("{{ route('users.store') }}", data, function(res) {
                    if (res.status == 'success' || res == 'success') {
                        // Using translation keys for "Success" and the message
                        Swal.fire("{{ __('success') }}", "{{ __('success_add') }}", 'success')
                            .then(
                                () => location.reload()
                            );
                    } else {
                        Swal.fire('Error', "{{ __('error_add') }}", 'error');
                    }
                }).fail(function(xhr) {
                    var errorMsg = JSON.parse(xhr.responseText).message;
                    // "Validation Error" can also be a key if you want
                    Swal.fire("{{ __('validation_error') }}", errorMsg, 'error');
                });
            });

            window.deleteUser = function(id) {
                Swal.fire({
                    title: "{{ __('confirm_delete') }}",
                    text: "{{ __('delete_warning') }}",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: "{{ __('delete_btn') }}",
                    cancelButtonText: "{{ __('cancel_btn') }}"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.post("{{ route('users.delete') }}", {
                            id: id
                        }, function(res) {
                            Swal.fire("{{ __('deleted_user') }}",
                                    "{{ __('success_delete') }}", 'success')
                                .then(() => location.reload());
                        });
                    }
                });
            }

            function toggleField(inputId, iconId) {
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
