@extends('template.app')
@section('title', 'Surat Masuk')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Surat Masuk</h3>
                <p class="text-subtitle text-muted">Pencatatan, pengelolaan, disposisi, dan arsip dokumen surat masuk.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Surat Masuk</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card mb-4">
        <div class="card-header pb-2">
            <h5 class="card-title mb-0"><i class="bi bi-funnel-fill me-2 text-primary"></i>Filter Data Surat Masuk</h5>
        </div>
        <div class="card-body">
            <form id="formFilterSurat" class="row g-3">
                <div class="col-md-3">
                    <label for="filter_tanggal_mulai" class="form-label">Tanggal Surat (Dari)</label>
                    <input type="date" class="form-control" id="filter_tanggal_mulai" name="tanggal_mulai">
                </div>
                <div class="col-md-3">
                    <label for="filter_tanggal_selesai" class="form-label">Tanggal Surat (Sampai)</label>
                    <input type="date" class="form-control" id="filter_tanggal_selesai" name="tanggal_selesai">
                </div>
                <div class="col-md-3">
                    <label for="filter_status_disposisi" class="form-label">Status Disposisi</label>
                    <select class="form-select" id="filter_status_disposisi" name="status_disposisi">
                        <option value="">Semua Status</option>
                        <option value="Belum Didisposisikan">Belum Didisposisikan</option>
                        <option value="Sudah Didisposisikan">Sudah Didisposisikan</option>
                        <option value="Selesai">Selesai</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="button" class="btn btn-primary flex-grow-1" id="btnApplyFilter">
                        <i class="bi bi-search me-1"></i> Terapkan
                    </button>
                    <button type="button" class="btn btn-light-secondary" id="btnResetFilter" title="Reset Filter">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Main Table Card -->
    <section class="section">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="card-title mb-0">Daftar Surat Masuk</h5>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahSurat">
                    <i class="bi bi-plus-circle-fill me-1"></i> Tambah Surat Masuk
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="surat-masuk-table" style="width:100%">
                        <thead>
                            <tr>
                                <th style="width: 40px;">No</th>
                                <th>No. Surat</th>
                                <th>Tgl Surat</th>
                                <th>Tgl Terima</th>
                                <th>Asal Surat</th>
                                <th>Perihal</th>
                                <th>Tujuan Disposisi</th>
                                <th style="width: 100px;">Status</th>
                                <th style="width: 90px;">Arsip</th>
                                <th style="width: 110px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal Tambah Surat Masuk -->
<div class="modal fade" id="modalTambahSurat" tabindex="-1" aria-labelledby="modalTambahSuratLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white" id="modalTambahSuratLabel">
                    <i class="bi bi-envelope-plus-fill me-2"></i>Tambah Surat Masuk Baru
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formTambahSurat" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="create_nomor_surat" class="form-label">Nomor Surat <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="create_nomor_surat" name="nomor_surat" required placeholder="Contoh: 005/123/DISP-2026">
                            <div class="invalid-feedback" id="error_create_nomor_surat"></div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="create_tanggal_surat" class="form-label">Tanggal Surat <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="create_tanggal_surat" name="tanggal_surat" required value="{{ date('Y-m-d') }}">
                            <div class="invalid-feedback" id="error_create_tanggal_surat"></div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="create_tanggal_terima" class="form-label">Tanggal Terima <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="create_tanggal_terima" name="tanggal_terima" required value="{{ date('Y-m-d') }}">
                            <small class="text-muted d-block mt-1">Otomatis hari ini</small>
                            <div class="invalid-feedback" id="error_create_tanggal_terima"></div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="create_asal_surat" class="form-label">Asal Surat / Pengirim <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="create_asal_surat" name="asal_surat" required placeholder="Instansi / Pengirim">
                            <div class="invalid-feedback" id="error_create_asal_surat"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="create_tujuan_disposisi" class="form-label">Tujuan / Disposisi Kepada <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="create_tujuan_disposisi" name="tujuan_disposisi" required placeholder="Contoh: Kepala Dinas / Sekretaris / Kasubag">
                            <div class="invalid-feedback" id="error_create_tujuan_disposisi"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="create_perihal" class="form-label">Perihal Surat <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="create_perihal" name="perihal" rows="2" required placeholder="Ringkasan perihal / isi surat"></textarea>
                        <div class="invalid-feedback" id="error_create_perihal"></div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="create_status_disposisi" class="form-label">Status Disposisi <span class="text-danger">*</span></label>
                            <select class="form-select" id="create_status_disposisi" name="status_disposisi" required>
                                <option value="Belum Didisposisikan" selected>Belum Didisposisikan</option>
                                <option value="Sudah Didisposisikan">Sudah Didisposisikan</option>
                                <option value="Selesai">Selesai</option>
                            </select>
                            <div class="invalid-feedback" id="error_create_status_disposisi"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="create_file_arsip" class="form-label">Upload Arsip Surat (PDF, JPG, PNG)</label>
                            <input type="file" class="form-control" id="create_file_arsip" name="file_arsip" accept=".pdf,.jpg,.jpeg,.png">
                            <small class="text-muted d-block mt-1">Maksimal ukuran file: 10 MB</small>
                            <div class="invalid-feedback" id="error_create_file_arsip"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="create_keterangan" class="form-label">Keterangan / Catatan Tambahan</label>
                        <textarea class="form-control" id="create_keterangan" name="keterangan" rows="2" placeholder="Catatan tambahan (opsional)"></textarea>
                        <div class="invalid-feedback" id="error_create_keterangan"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSubmitTambah">
                        <i class="bi bi-save me-1"></i> Simpan Surat
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Surat Masuk -->
<div class="modal fade" id="modalEditSurat" tabindex="-1" aria-labelledby="modalEditSuratLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title text-white" id="modalEditSuratLabel">
                    <i class="bi bi-pencil-square me-2"></i>Edit Surat Masuk
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditSurat" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="edit_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_nomor_surat" class="form-label">Nomor Surat <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_nomor_surat" name="nomor_surat" required>
                            <div class="invalid-feedback" id="error_edit_nomor_surat"></div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="edit_tanggal_surat" class="form-label">Tanggal Surat <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="edit_tanggal_surat" name="tanggal_surat" required>
                            <div class="invalid-feedback" id="error_edit_tanggal_surat"></div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="edit_tanggal_terima" class="form-label">Tanggal Terima <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="edit_tanggal_terima" name="tanggal_terima" required>
                            <div class="invalid-feedback" id="error_edit_tanggal_terima"></div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_asal_surat" class="form-label">Asal Surat / Pengirim <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_asal_surat" name="asal_surat" required>
                            <div class="invalid-feedback" id="error_edit_asal_surat"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_tujuan_disposisi" class="form-label">Tujuan / Disposisi Kepada <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_tujuan_disposisi" name="tujuan_disposisi" required>
                            <div class="invalid-feedback" id="error_edit_tujuan_disposisi"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="edit_perihal" class="form-label">Perihal Surat <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="edit_perihal" name="perihal" rows="2" required></textarea>
                        <div class="invalid-feedback" id="error_edit_perihal"></div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_status_disposisi" class="form-label">Status Disposisi <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_status_disposisi" name="status_disposisi" required>
                                <option value="Belum Didisposisikan">Belum Didisposisikan</option>
                                <option value="Sudah Didisposisikan">Sudah Didisposisikan</option>
                                <option value="Selesai">Selesai</option>
                            </select>
                            <div class="invalid-feedback" id="error_edit_status_disposisi"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_file_arsip" class="form-label">Ganti File Arsip (Opsional)</label>
                            <input type="file" class="form-control" id="edit_file_arsip" name="file_arsip" accept=".pdf,.jpg,.jpeg,.png">
                            <small class="text-muted d-block mt-1">Kosongkan jika tidak ingin mengganti file. Maksimal: 10 MB</small>
                            <div id="edit_current_file_info" class="mt-2"></div>
                            <div class="invalid-feedback" id="error_edit_file_arsip"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="edit_keterangan" class="form-label">Keterangan / Catatan Tambahan</label>
                        <textarea class="form-control" id="edit_keterangan" name="keterangan" rows="2"></textarea>
                        <div class="invalid-feedback" id="error_edit_keterangan"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning text-white" id="btnSubmitEdit">
                        <i class="bi bi-save me-1"></i> Perbarui Surat
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Detail Surat Masuk -->
<div class="modal fade" id="modalDetailSurat" tabindex="-1" aria-labelledby="modalDetailSuratLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title text-dark" id="modalDetailSuratLabel">
                    <i class="bi bi-info-circle-fill me-2"></i>Detail Surat Masuk
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered mb-0">
                        <tbody>
                            <tr>
                                <th style="width: 30%;" class="bg-light">Nomor Surat</th>
                                <td id="detail_nomor_surat" class="fw-bold">-</td>
                            </tr>
                            <tr>
                                <th class="bg-light">Tanggal Surat</th>
                                <td id="detail_tanggal_surat">-</td>
                            </tr>
                            <tr>
                                <th class="bg-light">Tanggal Terima</th>
                                <td id="detail_tanggal_terima">-</td>
                            </tr>
                            <tr>
                                <th class="bg-light">Asal Surat / Pengirim</th>
                                <td id="detail_asal_surat">-</td>
                            </tr>
                            <tr>
                                <th class="bg-light">Perihal</th>
                                <td id="detail_perihal">-</td>
                            </tr>
                            <tr>
                                <th class="bg-light">Tujuan / Disposisi Kepada</th>
                                <td id="detail_tujuan_disposisi" class="fw-bold text-primary">-</td>
                            </tr>
                            <tr>
                                <th class="bg-light">Status Disposisi</th>
                                <td id="detail_status_disposisi">-</td>
                            </tr>
                            <tr>
                                <th class="bg-light">Keterangan</th>
                                <td id="detail_keterangan">-</td>
                            </tr>
                            <tr>
                                <th class="bg-light">Diinput Oleh</th>
                                <td id="detail_creator">-</td>
                            </tr>
                            <tr>
                                <th class="bg-light">File Arsip</th>
                                <td id="detail_file_arsip">-</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Pratinjau File -->
<div class="modal fade" id="modalPreviewFile" tabindex="-1" aria-labelledby="modalPreviewFileLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title text-white" id="modalPreviewFileLabel">
                    <i class="bi bi-file-earmark-text me-2"></i>Pratinjau Arsip Surat
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0" style="min-height: 550px; background: #525659; display: flex; align-items: center; justify-content: center;">
                <div id="previewContainer" style="width: 100%; height: 650px;">
                    <!-- Content will be injected dynamically -->
                </div>
            </div>
            <div class="modal-footer bg-light justify-content-between">
                <div>
                    <span id="previewFileName" class="text-muted fw-bold"></span>
                </div>
                <div>
                    <a href="#" id="previewDownloadBtn" class="btn btn-primary" target="_blank">
                        <i class="bi bi-download me-1"></i> Unduh File
                    </a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
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

    // Inisialisasi DataTable Surat Masuk dengan AJAX POST
    let table = $('#surat-masuk-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
            url: "{{ route('surat-masuk.data') }}",
            type: "POST",
            data: function(d) {
                d.status_disposisi = $('#filter_status_disposisi').val();
                d.tanggal_mulai = $('#filter_tanggal_mulai').val();
                d.tanggal_selesai = $('#filter_tanggal_selesai').val();
            },
            error: function(xhr, error, code) {
                console.error("DataTables AJAX Error:", xhr.responseText);
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
            { data: 'nomor_surat', name: 'surat_masuk.nomor_surat' },
            { data: 'tanggal_surat', name: 'surat_masuk.tanggal_surat', className: 'text-center' },
            { data: 'tanggal_terima', name: 'surat_masuk.tanggal_terima', className: 'text-center' },
            { data: 'asal_surat', name: 'surat_masuk.asal_surat' },
            { data: 'perihal', name: 'surat_masuk.perihal' },
            { data: 'tujuan_disposisi', name: 'surat_masuk.tujuan_disposisi' },
            { data: 'status_badge', name: 'surat_masuk.status_disposisi', className: 'text-center' },
            { data: 'file_badge', name: 'surat_masuk.file_arsip', orderable: false, searchable: false, className: 'text-center' },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
        ],
        language: {
            search: "Cari Surat:",
            lengthMenu: "Tampilkan _MENU_ entri",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ surat",
            infoEmpty: "Menampilkan 0 sampai 0 dari 0 surat",
            infoFiltered: "(disaring dari _MAX_ total surat)",
            zeroRecords: "Tidak ada surat masuk yang cocok",
            processing: "Memuat data...",
            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: "Selanjutnya",
                previous: "Sebelumnya"
            }
        }
    });

    // Terapkan Filter
    $('#btnApplyFilter').on('click', function() {
        table.ajax.reload();
    });

    // Reset Filter
    $('#btnResetFilter').on('click', function() {
        $('#formFilterSurat')[0].reset();
        table.ajax.reload();
    });

    // Clear error feedback in form
    function clearErrors(form) {
        $(form).find('.is-invalid').removeClass('is-invalid');
        $(form).find('.invalid-feedback').text('');
    }

    // Modal Tambah: reset saat dibuka
    $('#modalTambahSurat').on('show.bs.modal', function() {
        $('#formTambahSurat')[0].reset();
        $('#create_tanggal_surat').val(new Date().toISOString().slice(0, 10));
        $('#create_tanggal_terima').val(new Date().toISOString().slice(0, 10));
        clearErrors('#formTambahSurat');
    });

    // Submit Tambah Surat Masuk
    $('#formTambahSurat').on('submit', function(e) {
        e.preventDefault();
        clearErrors(this);
        let btn = $('#btnSubmitTambah');
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...');

        let formData = new FormData(this);

        $.ajax({
            url: "{{ route('surat-masuk.store') }}",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function(res) {
                btn.prop('disabled', false).html('<i class="bi bi-save me-1"></i> Simpan Surat');
                $('#modalTambahSurat').modal('hide');
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
                btn.prop('disabled', false).html('<i class="bi bi-save me-1"></i> Simpan Surat');
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

    // Buka Modal Detail Surat Masuk
    $(document).on('click', '.btn-detail', function() {
        let id = $(this).data('id');
        $.ajax({
            url: "{{ url('surat-masuk') }}/" + id,
            type: "GET",
            success: function(res) {
                if (res.success) {
                    let s = res.data;
                    $('#detail_nomor_surat').text(s.nomor_surat);
                    $('#detail_tanggal_surat').text(s.tanggal_surat);
                    $('#detail_tanggal_terima').text(s.tanggal_terima);
                    $('#detail_asal_surat').text(s.asal_surat);
                    $('#detail_perihal').text(s.perihal);
                    $('#detail_tujuan_disposisi').text(s.tujuan_disposisi);

                    let badgeClass = s.status_disposisi === 'Selesai' ? 'bg-success' : (s.status_disposisi === 'Sudah Didisposisikan' ? 'bg-info text-dark' : 'bg-warning text-dark');
                    $('#detail_status_disposisi').html('<span class="badge ' + badgeClass + '">' + s.status_disposisi + '</span>');
                    $('#detail_keterangan').text(s.keterangan || '-');
                    $('#detail_creator').text(s.creator_name ? s.creator_name + ' (pada ' + s.created_at + ')' : '-');

                    if (res.file_url) {
                        $('#detail_file_arsip').html(
                            '<div class="d-flex align-items-center gap-2">' +
                            '<span class="badge bg-secondary">' + res.file_ext.toUpperCase() + ' (' + res.file_size + ')</span>' +
                            '<button type="button" class="btn btn-sm btn-outline-primary btn-preview-file" data-url="' + res.file_url + '" data-ext="' + res.file_ext + '" data-nomor="' + s.nomor_surat + '"><i class="bi bi-eye me-1"></i> Pratinjau</button>' +
                            '<a href="' + res.download_url + '" class="btn btn-sm btn-outline-success"><i class="bi bi-download me-1"></i> Unduh</a>' +
                            '</div>'
                        );
                    } else {
                        $('#detail_file_arsip').html('<span class="text-muted"><i class="bi bi-dash-circle me-1"></i>Tidak ada arsip yang diunggah</span>');
                    }

                    $('#modalDetailSurat').modal('show');
                }
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Gagal memuat detail surat.'
                });
            }
        });
    });

    // Buka Modal Edit Surat Masuk
    $(document).on('click', '.btn-edit', function() {
        let id = $(this).data('id');
        clearErrors('#formEditSurat');

        $.ajax({
            url: "{{ url('surat-masuk') }}/" + id,
            type: "GET",
            success: function(res) {
                if (res.success) {
                    let s = res.data;
                    $('#edit_id').val(s.id);
                    $('#edit_nomor_surat').val(s.nomor_surat);
                    $('#edit_tanggal_surat').val(s.tanggal_surat);
                    $('#edit_tanggal_terima').val(s.tanggal_terima);
                    $('#edit_asal_surat').val(s.asal_surat);
                    $('#edit_tujuan_disposisi').val(s.tujuan_disposisi);
                    $('#edit_perihal').val(s.perihal);
                    $('#edit_status_disposisi').val(s.status_disposisi);
                    $('#edit_keterangan').val(s.keterangan);
                    $('#edit_file_arsip').val('');

                    if (res.file_url) {
                        $('#edit_current_file_info').html(
                            '<div class="alert alert-secondary py-1 px-2 mb-0 d-flex justify-content-between align-items-center">' +
                            '<span><i class="bi bi-paperclip me-1"></i>Arsip saat ini: <strong>' + res.file_ext.toUpperCase() + '</strong> (' + res.file_size + ')</span>' +
                            '<a href="' + res.download_url + '" class="btn btn-xs btn-outline-primary py-0"><i class="bi bi-download"></i></a>' +
                            '</div>'
                        );
                    } else {
                        $('#edit_current_file_info').html('<small class="text-muted"><i class="bi bi-info-circle me-1"></i>Belum ada file arsip yang diunggah.</small>');
                    }

                    $('#modalEditSurat').modal('show');
                }
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Gagal memuat data surat.'
                });
            }
        });
    });

    // Submit Edit Surat Masuk
    $('#formEditSurat').on('submit', function(e) {
        e.preventDefault();
        clearErrors(this);
        let id = $('#edit_id').val();
        let btn = $('#btnSubmitEdit');
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Memperbarui...');

        let formData = new FormData(this);

        $.ajax({
            url: "{{ url('surat-masuk') }}/" + id + "/update",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function(res) {
                btn.prop('disabled', false).html('<i class="bi bi-save me-1"></i> Perbarui Surat');
                $('#modalEditSurat').modal('hide');
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
                btn.prop('disabled', false).html('<i class="bi bi-save me-1"></i> Perbarui Surat');
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

    // Hapus Surat Masuk
    $(document).on('click', '.btn-delete', function() {
        let id = $(this).data('id');
        let nomor = $(this).data('nomor');

        Swal.fire({
            title: 'Hapus Surat Masuk?',
            text: `Surat nomor "${nomor}" beserta file arsipnya akan dihapus permanen.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('surat-masuk') }}/" + id,
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
                            text: xhr.responseJSON?.message || 'Gagal menghapus data surat.'
                        });
                    }
                });
            }
        });
    });

    // Pratinjau File Modal
    $(document).on('click', '.btn-preview-file', function() {
        let url = $(this).data('url');
        let ext = ($(this).data('ext') || '').toLowerCase();
        let nomor = $(this).data('nomor') || 'Surat Masuk';

        $('#modalPreviewFileLabel').html('<i class="bi bi-file-earmark-text me-2"></i>Pratinjau: ' + nomor);
        $('#previewFileName').text(nomor);
        $('#previewDownloadBtn').attr('href', url.replace('/preview', '/download'));

        let container = $('#previewContainer');
        container.empty();

        if (['jpg', 'jpeg', 'png'].includes(ext)) {
            container.css('display', 'flex').css('justify-content', 'center').css('align-items', 'center');
            container.html('<img src="' + url + '" alt="Pratinjau Gambar" style="max-width: 100%; max-height: 100%; object-fit: contain; border-radius: 4px;">');
        } else if (ext === 'pdf') {
            container.css('display', 'block');
            container.html('<iframe src="' + url + '" style="width: 100%; height: 100%; border: none;"></iframe>');
        } else {
            container.html('<div class="text-white text-center p-5"><i class="bi bi-file-earmark-x fs-1 d-block mb-3"></i>Format file tidak dapat dipratinjau langsung. Silakan unduh file untuk membukanya.</div>');
        }

        $('#modalPreviewFile').modal('show');
    });
});
</script>
@endpush

