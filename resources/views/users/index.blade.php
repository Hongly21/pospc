@extends('layouts.app')

@section('title', 'គ្រប់គ្រងអ្នកប្រើប្រាស់')

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center bg-white">
            <h6 class="m-0 fw-bold text-primary"><i class="fas fa-users me-2"></i> បញ្ជីឈ្មោះអ្នកប្រើប្រាស់</h6>
            <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal"
                data-bs-target="#addUserModal">
                <i class="fas fa-user-plus me-1"></i> បន្ថែមអ្នកប្រើប្រាស់
            </button>
        </div>
        <div class="card-body">
            <form action="{{ route('users.index') }}" method="GET" class="row w-100 g-2 mb-4">

                <div class="col-12 col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="ស្វែងរកតាមឈ្មោះ ឬ អ៊ីមែល..."
                            value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-12 col-md-3">
                    <select name="role_id" class="form-select">
                        <option value="">តួនាទីទាំងអស់ (All Roles)</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->RoleID }}" {{ request('role_id') == $role->RoleID ? 'selected' : '' }}>
                                {{ $role->RoleName ?? 'Role ID: ' . $role->RoleID }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-3">
                    <select name="status" class="form-select border-primary text-primary">
                        <option value="" class="text-dark">ស្ថានភាពទាំងអស់ (All Status)</option>
                        <option value="Approved" {{ request('status') == 'Approved' ? 'selected' : '' }}
                            class="text-success">អនុម័ត (Approved)</option>
                        <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }} class="text-warning">
                            រង់ចាំ (Pending)</option>
                        <option value="Reject" {{ request('status') == 'Reject' ? 'selected' : '' }} class="text-danger">
                            បដិសេធ (Reject)</option>
                    </select>
                </div>

                <div class="col-12 col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-outline-primary flex-grow-1 "><i class="fas fa-filter"></i>
                        ស្វែងរក</button>
                    <a href="{{ route('users.index') }}" class="btn btn-outline-danger px-3" title="Clear Filter"><i
                            class="fas fa-sync-alt"></i></a>
                </div>
            </form>
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>លេខសំគាល់</th>
                            <th>ឈ្មោះ</th>
                            <th>តួនាទី</th>
                            <th>ស្ថានភាព</th>
                            <th class="text-end">Actions</th>
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
                                                'Manager' => 'ម្ចាស់ហាង',
                                                'Staff' => 'បុគ្គលិក',
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
                                        <span class="badge bg-danger">មិនមានតួនាទី</span>
                                    @endif
                                </td>

                                <td>
                                    @if ($row->Status == 'Approved')
                                        <span class="badge bg-success bg-opacity-10 text-success px-3">អនុម័ត</span>
                                    @else
                                        <span class="badge bg-warning bg-opacity-10 text-warning px-3">កំពុងរងចាំ</span>
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
                                    {{-- <button class="btn btn-sm btn-outline-primary edit-btn mb-1" data-bs-toggle="modal"
                                        data-bs-target="#updateUserModal" data-id="{{ $row->UserID }}"
                                        data-name="{{ $row->Username }}" data-email="{{ $row->Email }}"
                                        data-roleid="{{ $row->RoleID }}"> <i class="fas fa-edit"></i>
                                    </button> --}}

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
                                            <h5 class="modal-title fw-bold">កែប្រែព័ត៌មានបុគ្គលិក - {{ $row->Username }}
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form action="{{route('users.update', $row->UserID)}}"  method="POST">
                                                <div class="mb-3">
                                                    <label class="form-label">ឈ្មោះ</label>
                                                    <input type="text" class="form-control" name="Username"
                                                        value="{{ $row->Username }}">
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label class="form-label">អ៊ីមែល</label>
                                                    <input type="email" class="form-control" name="Email"
                                                        value="{{ $row->Email }}" readonly>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">តួនាទី</label>
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
                                                    <label class="form-label">ពាក្យសម្ងាត់ថ្មី <small
                                                            class="text-muted">(ជម្រើស)</small></label>
                                                    <input type="password" class="form-control" id="password"
                                                        placeholder="ទុកឲ្យទទេដើម្បីរក្សាទុកដដែល">
                                                </div>
                                            </form>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-outline-secondary fw-bold"
                                                data-bs-dismiss="modal">បិទ</button>
                                            <button type="submit"
                                                class="btn btn-outline-primary fw-bold">កែប្រែ</button>
                                        </div>
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
                    <h5 class="modal-title fw-bold">បន្ថែមបុគ្គលិកថ្មី</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addUserForm">
                        <div class="mb-3">
                            <label class="form-label">ឈ្មោះពេញ</label>
                            <input type="text" class="form-control" id="add_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">អាសយដ្ឋានអ៊ីមែល</label>
                            <input type="email" class="form-control" id="add_email" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">ពាក្យសម្ងាត់</label>
                            <input type="password" class="form-control" id="add_password" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">កំណត់តួនាទី</label>
                            <select class="form-select" id="add_role">
                                @foreach ($roles as $role)
                                    <option value="{{ $role->RoleID }}">{{ $role->RoleName }}</option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary fw-bold"
                        data-bs-dismiss="modal">បោះបង់</button>
                    <button type="button" class="btn btn-outline-success fw-bold btn_save_user">រក្សាទុក</button>
                </div>
            </div>
        </div>
    </div>
    {{-- {{-- update modal}} --}}
    {{-- <div class="modal fade" id="updateUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header  text-dark">
                    <h5 class="modal-title fw-bold">កែប្រែព័ត៌មានបុគ្គលិក</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="updateUserForm">
                        <input type="hidden" id="update_id">

                        <div class="mb-3">
                            <label class="form-label">ឈ្មោះ</label>
                            <input type="text" class="form-control" id="update_name">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">អ៊ីមែល</label>
                            <input type="email" class="form-control" id="update_email" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">តួនាទី</label>
                            <select class="form-select" id="update_role">
                                @foreach ($roles as $role)
                                    <option value="{{ $role->RoleID }}">{{ $role->RoleName }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">ពាក្យសម្ងាត់ថ្មី <small class="text-muted">(ជម្រើស)</small></label>
                            <input type="password" class="form-control" id="update_password"
                                placeholder="ទុកឲ្យទទេដើម្បីរក្សាទុកដដែល">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary fw-bold" data-bs-dismiss="modal">បិទ</button>
                    <button type="button" class="btn btn-outline-primary fw-bold btn_update_user">កែប្រែ</button>
                </div>
            </div>
        </div>
    </div> --}}

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
                        Swal.fire('ជោគជ័យ', 'បានបន្ថែមអ្នកប្រើប្រាស់ដោយជោគជ័យ', 'success').then(
                            () =>
                            location.reload());
                    } else {
                        Swal.fire('Error', 'មានបញ្ហាក្នងការបន្ថែមអ្នកប្រើប្រាស់', 'error');
                    }
                }).fail(function(xhr) {
                    var errorMsg = JSON.parse(xhr.responseText).message;
                    Swal.fire('Validation Error', errorMsg, 'error');
                });
            });

            // $('.btn_update_user').click(function() {
            //     var data = {
            //         id: $('#update_id').val(),
            //         name: $('#update_name').val(),
            //         role_id: $('#update_role').val(),
            //         password: $('#update_password').val()
            //     };
            //     $.post("{{ route('users.update') }}", data, function(res) {
            //         if (res.status == 'success' || res == 'success') {
            //             Swal.fire('បានកែប្រែ', 'បានកែប្រែអ្នកប្រើប្រាស់ដោយជោគជ័យ', 'success').then(
            //                 () => location.reload());
            //         } else {
            //             Swal.fire('Error', 'បានបរាជ័យកែប្រែ.', 'error');
            //         }
            //     }).fail(function(xhr) {
            //         console.log(xhr.responseText);
            //         Swal.fire('Error', 'មានបញ្ហាមិនប្រក្រតី', 'error');
            //     });
            // });

            window.deleteUser = function(id) {
                Swal.fire({
                    title: 'តើអ្នកប្រាកដឬទេ?',
                    text: "ទិន្នន័យនេះនឹងត្រូវបានលុបជាអចិន្ត្រៃយ៍!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'លុប',
                    cancelButtonText: "បោះបង់"

                }).then((result) => {
                    if (result.isConfirmed) {
                        $.post("{{ route('users.delete') }}", {
                            id: id
                        }, function(res) {
                            Swal.fire('បានលុប', 'បានលុបអ្នកប្រើប្រាស់ដោយជោគជ័យ', 'success')
                                .then(() => location.reload());
                        });
                    }
                });
            }
        });
    </script>

@endsection
