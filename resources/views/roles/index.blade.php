@extends('template.app')
@section('title', 'Manajemen Role & Hak Akses')

@section('content')
<div class="page-heading">
    <div class="page-title mb-3">
        <div class="row align-items-center">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3><i class="bi bi-shield-lock-fill me-2 text-primary"></i>Manajemen Role & Hak Akses</h3>
                <p class="text-subtitle text-muted">Kelola peran pengguna (roles) dan konfigurasi izin akses (permissions) menggunakan Spatie.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Manajemen Role</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    

    <!-- Data Table Card -->
    <div class="card border-0 shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center py-3 bg-white border-bottom">
            <h5 class="card-title mb-0">
                <i class="bi bi-list-stars me-2 text-primary"></i>Daftar Role & Hak Akses
            </h5>
            <button type="button" class="btn btn-primary" id="btnBukaModalTambah">
                <i class="bi bi-plus-circle me-1"></i> Tambah Role Baru
            </button>
        </div>
        <div class="card-body pt-3">
            <div class="table-responsive">
                <table class="table table-hover table-striped w-100" id="roles-table">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">No</th>
                            <th style="min-width: 150px;">Nama Role</th>
                            <th style="min-width: 250px;">Hak Akses (Permissions)</th>
                            <th class="text-center" style="width: 130px;">Pengguna</th>
                            <th class="text-center" style="width: 120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Role -->
<div class="modal fade" id="modalTambahRole" tabindex="-1" aria-labelledby="modalTambahRoleLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <form id="formTambahRole" class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title text-white" id="modalTambahRoleLabel">
                    <i class="bi bi-plus-circle me-2"></i>Tambah Role & Atur Hak Akses
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" style="max-height: calc(85vh - 130px); overflow-y: auto;">
                <div class="mb-4">
                    <label for="create_name" class="form-label fw-bold">Nama Role <span class="text-danger">*</span></label>
                    <input type="text" class="form-control form-control-lg" id="create_name" name="name" placeholder="Contoh: Tata Usaha, Arsiparis, Operator..." required>
                    <div class="invalid-feedback" id="error_create_name"></div>
                    <small class="text-muted">Nama peran pengguna dalam sistem.</small>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-shield-check me-2 text-primary"></i>Pilih Hak Akses (Permissions)</h6>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-primary btn-sm" id="btnCheckAllTambah">Centang Semua</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="btnUncheckAllTambah">Hapus Semua</button>
                    </div>
                </div>

                <div class="accordion" id="accordionPermissionsTambah">
                    @foreach ($permissionGroups as $groupName => $perms)
                        <div class="accordion-item mb-2 border rounded">
                            <h2 class="accordion-header" id="heading_tambah_{{ Str::slug($groupName) }}">
                                <button class="accordion-button py-2 bg-light text-dark fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapse_tambah_{{ Str::slug($groupName) }}" aria-expanded="true">
                                    <i class="bi bi-folder-fill me-2 text-primary"></i>{{ $groupName }} ({{ count($perms) }})
                                </button>
                            </h2>
                            <div id="collapse_tambah_{{ Str::slug($groupName) }}" class="accordion-collapse collapse show">
                                <div class="accordion-body p-3">
                                    <div class="row g-2">
                                        @foreach ($perms as $permName => $permDesc)
                                            <div class="col-md-6">
                                                <div class="form-check p-2 border rounded bg-white hover-shadow-sm">
                                                    <input class="form-check-input perm-checkbox-tambah ms-1" type="checkbox" name="permissions[]" value="{{ $permName }}" id="perm_tambah_{{ Str::slug($permName) }}">
                                                    <label class="form-check-label ms-2 d-block cursor-pointer" for="perm_tambah_{{ Str::slug($permName) }}">
                                                        <strong class="d-block font-monospace text-primary small">{{ $permName }}</strong>
                                                        <span class="text-muted small">{{ $permDesc }}</span>
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary px-4" id="btnSubmitTambahRole">
                    <i class="bi bi-save me-1"></i> Simpan Role
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Role -->
<div class="modal fade" id="modalEditRole" tabindex="-1" aria-labelledby="modalEditRoleLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <form id="formEditRole" class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title fw-bold" id="modalEditRoleLabel">
                    <i class="bi bi-pencil-square me-2"></i>Ubah Role & Hak Akses
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" style="max-height: calc(85vh - 130px); overflow-y: auto;">
                <input type="hidden" id="edit_id" name="id">
                <div class="mb-4">
                    <label for="edit_name" class="form-label fw-bold">Nama Role <span class="text-danger">*</span></label>
                    <input type="text" class="form-control form-control-lg" id="edit_name" name="name" required>
                    <div class="invalid-feedback" id="error_edit_name"></div>
                    <small class="text-muted" id="edit_name_help">Nama peran pengguna dalam sistem.</small>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-shield-check me-2 text-warning"></i>Atur Hak Akses (Permissions)</h6>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-primary btn-sm" id="btnCheckAllEdit">Centang Semua</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="btnUncheckAllEdit">Hapus Semua</button>
                    </div>
                </div>

                <div class="accordion" id="accordionPermissionsEdit">
                    @foreach ($permissionGroups as $groupName => $perms)
                        <div class="accordion-item mb-2 border rounded">
                            <h2 class="accordion-header" id="heading_edit_{{ Str::slug($groupName) }}">
                                <button class="accordion-button py-2 bg-light text-dark fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapse_edit_{{ Str::slug($groupName) }}" aria-expanded="true">
                                    <i class="bi bi-folder-fill me-2 text-warning"></i>{{ $groupName }} ({{ count($perms) }})
                                </button>
                            </h2>
                            <div id="collapse_edit_{{ Str::slug($groupName) }}" class="accordion-collapse collapse show">
                                <div class="accordion-body p-3">
                                    <div class="row g-2">
                                        @foreach ($perms as $permName => $permDesc)
                                            <div class="col-md-6">
                                                <div class="form-check p-2 border rounded bg-white hover-shadow-sm">
                                                    <input class="form-check-input perm-checkbox-edit ms-1" type="checkbox" name="permissions[]" value="{{ $permName }}" id="perm_edit_{{ Str::slug($permName) }}">
                                                    <label class="form-check-label ms-2 d-block cursor-pointer" for="perm_edit_{{ Str::slug($permName) }}">
                                                        <strong class="d-block font-monospace text-primary small">{{ $permName }}</strong>
                                                        <span class="text-muted small">{{ $permDesc }}</span>
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-warning px-4 text-dark fw-bold" id="btnSubmitEditRole">
                    <i class="bi bi-save me-1"></i> Perbarui Role
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('js')
<script>
$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Inisialisasi DataTable Role
    let table = $('#roles-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('roles.data') }}",
            type: "POST",
            error: function(xhr) {
                console.error("DataTables Error:", xhr.responseText);
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
            { data: 'name', name: 'name', className: 'fw-bold text-dark' },
            { data: 'permissions_badge', name: 'permissions_badge', orderable: false, searchable: false },
            { data: 'users_count_badge', name: 'users_count', className: 'text-center' },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
        ],
        language: {
            search: "Cari Role:",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ role",
            infoEmpty: "Menampilkan 0 sampai 0 dari 0 role",
            infoFiltered: "(disaring dari _MAX_ total data)",
            zeroRecords: "Tidak ada data role yang cocok",
            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: "Selanjutnya",
                previous: "Sebelumnya"
            }
        }
    });

    // Reset error form
    function clearErrors(form) {
        $(form).find('.is-invalid').removeClass('is-invalid');
        $(form).find('.invalid-feedback').text('');
    }

    // Centang / Hapus Semua Checkbox
    $('#btnCheckAllTambah').on('click', function() {
        $('.perm-checkbox-tambah').prop('checked', true);
    });
    $('#btnUncheckAllTambah').on('click', function() {
        $('.perm-checkbox-tambah').prop('checked', false);
    });

    $('#btnCheckAllEdit').on('click', function() {
        $('.perm-checkbox-edit').prop('checked', true);
    });
    $('#btnUncheckAllEdit').on('click', function() {
        $('.perm-checkbox-edit').prop('checked', false);
    });

    // Buka Modal Tambah Role
    $('#btnBukaModalTambah').on('click', function() {
        $('#formTambahRole')[0].reset();
        clearErrors('#formTambahRole');
        $('.perm-checkbox-tambah').prop('checked', false);
        $('#modalTambahRole').modal('show');
    });

    // Submit Tambah Role
    $('#formTambahRole').on('submit', function(e) {
        e.preventDefault();
        clearErrors(this);
        let btn = $('#btnSubmitTambahRole');
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...');

        $.ajax({
            url: "{{ route('roles.store') }}",
            type: "POST",
            data: $(this).serialize(),
            success: function(res) {
                btn.prop('disabled', false).html('<i class="bi bi-save me-1"></i> Simpan Role');
                if (res.success) {
                    $('#modalTambahRole').modal('hide');
                    table.ajax.reload(null, false);
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: res.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).html('<i class="bi bi-save me-1"></i> Simpan Role');
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    if (errors.name) {
                        $('#create_name').addClass('is-invalid');
                        $('#error_create_name').text(errors.name[0]);
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: xhr.responseJSON?.message || 'Terjadi kesalahan sistem.'
                    });
                }
            }
        });
    });

    // Buka Modal Edit Role
    $(document).on('click', '.btn-edit-role', function() {
        let id = $(this).data('id');
        clearErrors('#formEditRole');
        $('.perm-checkbox-edit').prop('checked', false);

        $.ajax({
            url: "{{ url('roles') }}/" + id,
            type: "GET",
            success: function(res) {
                if (res.success) {
                    let r = res.data;
                    $('#edit_id').val(r.id);
                    $('#edit_name').val(r.name);

                    if (r.is_system) {
                        $('#edit_name').prop('readonly', true);
                        $('#edit_name_help').text('Nama role utama sistem (Admin) tidak dapat diubah.');
                        $('.perm-checkbox-edit').prop('checked', true).prop('disabled', true);
                        $('#btnCheckAllEdit, #btnUncheckAllEdit').prop('disabled', true);
                    } else {
                        $('#edit_name').prop('readonly', false);
                        $('#edit_name_help').text('Nama peran pengguna dalam sistem.');
                        $('.perm-checkbox-edit').prop('disabled', false);
                        $('#btnCheckAllEdit, #btnUncheckAllEdit').prop('disabled', false);

                        // Centang permissions yang dimiliki
                        if (r.permissions && r.permissions.length > 0) {
                            r.permissions.forEach(function(permName) {
                                $('input.perm-checkbox-edit[value="' + permName + '"]').prop('checked', true);
                            });
                        }
                    }

                    $('#modalEditRole').modal('show');
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Gagal memuat data role.'
                });
            }
        });
    });

    // Submit Edit Role
    $('#formEditRole').on('submit', function(e) {
        e.preventDefault();
        clearErrors(this);
        let id = $('#edit_id').val();
        let btn = $('#btnSubmitEditRole');
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Memperbarui...');

        $.ajax({
            url: "{{ url('roles') }}/" + id,
            type: "PUT",
            data: $(this).serialize(),
            success: function(res) {
                btn.prop('disabled', false).html('<i class="bi bi-save me-1"></i> Perbarui Role');
                if (res.success) {
                    $('#modalEditRole').modal('hide');
                    table.ajax.reload(null, false);
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: res.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).html('<i class="bi bi-save me-1"></i> Perbarui Role');
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    if (errors.name) {
                        $('#edit_name').addClass('is-invalid');
                        $('#error_edit_name').text(errors.name[0]);
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: xhr.responseJSON?.message || 'Terjadi kesalahan sistem.'
                    });
                }
            }
        });
    });

    // Hapus Role
    $(document).on('click', '.btn-delete-role', function() {
        let id = $(this).data('id');
        let name = $(this).data('name');

        Swal.fire({
            title: 'Hapus Role?',
            text: 'Role "' + name + '" akan dihapus secara permanen. Tindakan ini tidak dapat dibatalkan.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="bi bi-trash-fill me-1"></i> Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('roles') }}/" + id,
                    type: "DELETE",
                    success: function(res) {
                        if (res.success) {
                            table.ajax.reload(null, false);
                            Swal.fire({
                                icon: 'success',
                                title: 'Terhapus!',
                                text: res.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Menghapus',
                            text: xhr.responseJSON?.message || 'Terjadi kesalahan saat menghapus role.'
                        });
                    }
                });
            }
        });
    });
});
</script>
@endpush

