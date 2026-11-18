@extends('template.app')
@section('title', 'Manajemen Pengguna')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Manajemen Pengguna</h3>
                <p class="text-subtitle text-muted">Kelola data pengguna, role, status akun, dan reset password.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Manajemen Pengguna</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Daftar Pengguna</h5>
                @can('users.manage')
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahUser">
                    <i class="bi bi-person-plus-fill me-1"></i> Tambah Pengguna
                </button>
                @endcan
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="users-table" style="width:100%">
                        <thead>
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th>Nama</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th style="width: 90px;">Role</th>
                                <th style="width: 90px;">Status</th>
                                <th style="width: 140px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal Tambah Pengguna -->
<div class="modal fade" id="modalTambahUser" tabindex="-1" aria-labelledby="modalTambahUserLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white" id="modalTambahUserLabel">
                    <i class="bi bi-person-plus-fill me-2"></i>Tambah Pengguna Baru
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formTambahUser">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="create_name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="create_name" name="name" required placeholder="Masukkan nama lengkap">
                        <div class="invalid-feedback" id="error_create_name"></div>
                    </div>
                    <div class="mb-3">
                        <label for="create_username" class="form-label">Username <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="create_username" name="username" required placeholder="Masukkan username">
                        <div class="invalid-feedback" id="error_create_username"></div>
                    </div>
                    <div class="mb-3">
                        <label for="create_email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="create_email" name="email" required placeholder="nama@email.com">
                        <div class="invalid-feedback" id="error_create_email"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="create_role" class="form-label">Role <span class="text-danger">*</span></label>
                            <select class="form-select" id="create_role" name="role" required>
                                <option value="" disabled selected>Pilih Role</option>
                                @foreach ($roles as $r)
                                    <option value="{{ $r->name }}">{{ $r->name }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" id="error_create_role"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="create_is_active" class="form-label">Status Akun <span class="text-danger">*</span></label>
                            <select class="form-select" id="create_is_active" name="is_active" required>
                                <option value="1" selected>Aktif</option>
                                <option value="0">Nonaktif</option>
                            </select>
                            <div class="invalid-feedback" id="error_create_is_active"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="create_password" class="form-label">Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="create_password" name="password" required minlength="6" placeholder="Minimal 6 karakter">
                            <button class="btn btn-outline-secondary toggle-pwd" type="button" data-target="#create_password">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <div class="invalid-feedback d-block" id="error_create_password"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSubmitTambah">
                        <i class="bi bi-save me-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Pengguna -->
<div class="modal fade" id="modalEditUser" tabindex="-1" aria-labelledby="modalEditUserLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title text-white" id="modalEditUserLabel">
                    <i class="bi bi-pencil-square me-2"></i>Edit Pengguna
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditUser">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_id" name="id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                        <div class="invalid-feedback" id="error_edit_name"></div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_username" class="form-label">Username <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_username" name="username" required>
                        <div class="invalid-feedback" id="error_edit_username"></div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="edit_email" name="email" required>
                        <div class="invalid-feedback" id="error_edit_email"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_role" class="form-label">Role <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_role" name="role" required>
                                <option value="" disabled>Pilih Role</option>
                                @foreach ($roles as $r)
                                    <option value="{{ $r->name }}">{{ $r->name }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" id="error_edit_role"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_is_active" class="form-label">Status Akun <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_is_active" name="is_active" required>
                                <option value="1">Aktif</option>
                                <option value="0">Nonaktif</option>
                            </select>
                            <div class="invalid-feedback" id="error_edit_is_active"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning text-white" id="btnSubmitEdit">
                        <i class="bi bi-save me-1"></i> Perbarui
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Reset Password -->
<div class="modal fade" id="modalResetPassword" tabindex="-1" aria-labelledby="modalResetPasswordLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title text-dark" id="modalResetPasswordLabel">
                    <i class="bi bi-key-fill me-2"></i>Reset Password
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formResetPassword">
                @csrf
                <input type="hidden" id="reset_user_id">
                <div class="modal-body">
                    <p>Reset password untuk pengguna: <strong id="reset_user_name">-</strong></p>
                    <div class="mb-3">
                        <label for="new_password" class="form-label">Password Baru <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="new_password" name="new_password" required minlength="6" placeholder="Minimal 6 karakter">
                            <button class="btn btn-outline-secondary toggle-pwd" type="button" data-target="#new_password">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <div class="invalid-feedback d-block" id="error_new_password"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info text-dark" id="btnSubmitReset">
                        <i class="bi bi-check-lg me-1"></i> Reset Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
$(document).ready(function() {
    // CSRF Token setup for AJAX
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Inisialisasi DataTable Yajra dengan AJAX POST
    let table = $('#users-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
            url: "{{ route('users.data') }}",
            type: "POST",
            error: function(xhr, error, code) {
                console.error("DataTables AJAX Error:", xhr.responseText);
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
            { data: 'name', name: 'name' },
            { data: 'username', name: 'username' },
            { data: 'email', name: 'email' },
            { data: 'role_badge', name: 'role', className: 'text-center' },
            { data: 'status_badge', name: 'is_active', className: 'text-center' },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
        ],
        language: {
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ entri",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
            infoEmpty: "Menampilkan 0 sampai 0 dari 0 entri",
            infoFiltered: "(disaring dari _MAX_ total entri)",
            zeroRecords: "Tidak ada data yang ditemukan",
            processing: "Memuat data...",
            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: "Selanjutnya",
                previous: "Sebelumnya"
            }
        }
    });

    // Toggle Password Visibility
    $(document).on('click', '.toggle-pwd', function() {
        let targetInput = $($(this).data('target'));
        let icon = $(this).find('i');
        if (targetInput.attr('type') === 'password') {
            targetInput.attr('type', 'text');
            icon.removeClass('bi-eye').addClass('bi-eye-slash');
        } else {
            targetInput.attr('type', 'password');
            icon.removeClass('bi-eye-slash').addClass('bi-eye');
        }
    });

    // Reset error form
    function clearErrors(form) {
        $(form).find('.is-invalid').removeClass('is-invalid');
        $(form).find('.invalid-feedback').text('');
    }

    // Modal Tambah: Reset form on open
    $('#modalTambahUser').on('show.bs.modal', function() {
        $('#formTambahUser')[0].reset();
        clearErrors('#formTambahUser');
    });

    // Submit Tambah Pengguna
    $('#formTambahUser').on('submit', function(e) {
        e.preventDefault();
        clearErrors(this);
        let btn = $('#btnSubmitTambah');
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...');

        $.ajax({
            url: "{{ route('users.store') }}",
            type: "POST",
            data: $(this).serialize(),
            success: function(res) {
                btn.prop('disabled', false).html('<i class="bi bi-save me-1"></i> Simpan');
                $('#modalTambahUser').modal('hide');
                table.ajax.reload(null, false);
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: res.message,
                    timer: 2000,
                    showConfirmButton: false
                });
            },
            error: function(xhr) {
                btn.prop('disabled', false).html('<i class="bi bi-save me-1"></i> Simpan');
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, val) {
                        $('#create_' + key).addClass('is-invalid');
                        $('#error_create_' + key).text(val[0]);
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: xhr.responseJSON?.message || 'Terjadi kesalahan pada sistem.'
                    });
                }
            }
        });
    });

    // Buka Modal Edit Pengguna
    $(document).on('click', '.btn-edit', function() {
        clearErrors('#formEditUser');
        let id = $(this).data('id');
        let name = $(this).data('name');
        let username = $(this).data('username');
        let email = $(this).data('email');
        let role = $(this).data('role');
        let is_active = $(this).data('is_active');

        $('#edit_id').val(id);
        $('#edit_name').val(name);
        $('#edit_username').val(username);
        $('#edit_email').val(email);
        $('#edit_role').val(role);
        $('#edit_is_active').val(is_active);

        $('#modalEditUser').modal('show');
    });

    // Submit Edit Pengguna
    $('#formEditUser').on('submit', function(e) {
        e.preventDefault();
        clearErrors(this);
        let id = $('#edit_id').val();
        let btn = $('#btnSubmitEdit');
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Memperbarui...');

        $.ajax({
            url: "{{ url('users') }}/" + id,
            type: "POST",
            data: $(this).serialize(),
            success: function(res) {
                btn.prop('disabled', false).html('<i class="bi bi-save me-1"></i> Perbarui');
                $('#modalEditUser').modal('hide');
                table.ajax.reload(null, false);
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: res.message,
                    timer: 2000,
                    showConfirmButton: false
                });
            },
            error: function(xhr) {
                btn.prop('disabled', false).html('<i class="bi bi-save me-1"></i> Perbarui');
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, val) {
                        $('#edit_' + key).addClass('is-invalid');
                        $('#error_edit_' + key).text(val[0]);
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: xhr.responseJSON?.message || 'Terjadi kesalahan pada sistem.'
                    });
                }
            }
        });
    });

    // Toggle Aktifkan / Nonaktifkan Akun
    $(document).on('click', '.btn-toggle-status', function() {
        let id = $(this).data('id');
        let name = $(this).data('name');
        let action = $(this).data('action');

        Swal.fire({
            title: 'Konfirmasi',
            text: `Apakah Anda yakin ingin ${action} akun "${name}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Lanjutkan',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('users') }}/" + id + "/toggle-status",
                    type: "POST",
                    success: function(res) {
                        table.ajax.reload(null, false);
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: res.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: xhr.responseJSON?.message || 'Gagal mengubah status akun.'
                        });
                    }
                });
            }
        });
    });

    // Buka Modal Reset Password
    $(document).on('click', '.btn-reset-password', function() {
        clearErrors('#formResetPassword');
        let id = $(this).data('id');
        let name = $(this).data('name');

        $('#reset_user_id').val(id);
        $('#reset_user_name').text(name);
        $('#new_password').val('');

        $('#modalResetPassword').modal('show');
    });

    // Submit Reset Password
    $('#formResetPassword').on('submit', function(e) {
        e.preventDefault();
        clearErrors(this);
        let id = $('#reset_user_id').val();
        let btn = $('#btnSubmitReset');
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Mereset...');

        $.ajax({
            url: "{{ url('users') }}/" + id + "/reset-password",
            type: "POST",
            data: $(this).serialize(),
            success: function(res) {
                btn.prop('disabled', false).html('<i class="bi bi-check-lg me-1"></i> Reset Password');
                $('#modalResetPassword').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: res.message,
                    timer: 2000,
                    showConfirmButton: false
                });
            },
            error: function(xhr) {
                btn.prop('disabled', false).html('<i class="bi bi-check-lg me-1"></i> Reset Password');
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    if (errors.new_password) {
                        $('#new_password').addClass('is-invalid');
                        $('#error_new_password').text(errors.new_password[0]);
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: xhr.responseJSON?.message || 'Gagal mereset password.'
                    });
                }
            }
        });
    });

    // Hapus Pengguna
    $(document).on('click', '.btn-delete', function() {
        let id = $(this).data('id');
        let name = $(this).data('name');

        Swal.fire({
            title: 'Hapus Pengguna?',
            text: `Data pengguna "${name}" akan dihapus permanen.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('users') }}/" + id,
                    type: "DELETE",
                    success: function(res) {
                        table.ajax.reload(null, false);
                        Swal.fire({
                            icon: 'success',
                            title: 'Terhapus!',
                            text: res.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: xhr.responseJSON?.message || 'Gagal menghapus pengguna.'
                        });
                    }
                });
            }
        });
    });
});
</script>
@endpush

