@extends('template.app')
@section('title', 'Data Surat')

@section('content')
<div class="page-heading">
    <div class="page-title mb-3">
        <div class="row align-items-center">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3 id="pageHeaderTitle">Data Surat</h3>
                <p class="text-subtitle text-muted">Pencatatan, pencarian, dan pengelolaan arsip Surat Masuk & Surat Keluar.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page" id="breadcrumbCurrent">Data Surat</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <!-- Filter & Pencarian Card -->
    <div class="card mb-4">
        <div class="card-header pb-2 d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <i class="bi bi-funnel-fill me-2 text-primary"></i>Pencarian & Penyaringan Data
            </h5>
            <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFilter" aria-expanded="true">
                <i class="bi bi-chevron-down"></i>
            </button>
        </div>
        <div class="collapse show" id="collapseFilter">
            <div class="card-body pt-2">
                <form id="formFilterSurat" class="row g-3">
                    <div class="col-md-3">
                        <label for="filter_nomor_surat" class="form-label">Nomor Surat</label>
                        <input type="text" class="form-control" id="filter_nomor_surat" name="nomor_surat" placeholder="Cari nomor surat...">
                    </div>
                    <div class="col-md-3">
                        <label for="filter_perihal" class="form-label">Perihal (Kata Kunci)</label>
                        <input type="text" class="form-control" id="filter_perihal" name="perihal" placeholder="Cari kata kunci perihal...">
                    </div>
                    <div class="col-md-2">
                        <label for="filter_jenis_surat" class="form-label">Jenis Surat</label>
                        <select class="form-select" id="filter_jenis_surat" name="jenis_surat">
                            @if(auth()->user()?->can('surat-masuk.view') && auth()->user()?->can('surat-keluar.view'))
                                <option value="">Semua Jenis</option>
                            @endif
                            @can('surat-masuk.view')
                                <option value="Masuk" {{ $defaultJenis === 'Masuk' || !auth()->user()?->can('surat-keluar.view') ? 'selected' : '' }}>Surat Masuk</option>
                            @endcan
                            @can('surat-keluar.view')
                                <option value="Keluar" {{ $defaultJenis === 'Keluar' || !auth()->user()?->can('surat-masuk.view') ? 'selected' : '' }}>Surat Keluar</option>
                            @endcan
                        </select>
                    </div>
                    <div class="col-md-2" id="filter_disposisi_wrapper">
                        <label for="filter_status_disposisi" class="form-label">Status Disposisi</label>
                        <select class="form-select" id="filter_status_disposisi" name="status_disposisi">
                            <option value="">Semua Status</option>
                            <option value="Belum Didisposisikan">Belum Didisposisikan</option>
                            <option value="Sudah Didisposisikan">Sudah Didisposisikan</option>
                            <option value="Selesai">Selesai</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end gap-2">
                        <button type="button" class="btn btn-primary flex-grow-1" id="btnApplyFilter">
                            <i class="bi bi-search me-1"></i> Terapkan
                        </button>
                        <button type="button" class="btn btn-light-secondary" id="btnResetFilter" title="Reset Filter">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </button>
                    </div>

                    <div class="col-md-3">
                        <label for="filter_tanggal_mulai" class="form-label">Tanggal Surat (Dari)</label>
                        <input type="date" class="form-control" id="filter_tanggal_mulai" name="tanggal_mulai">
                    </div>
                    <div class="col-md-3">
                        <label for="filter_tanggal_selesai" class="form-label">Tanggal Surat (Sampai)</label>
                        <input type="date" class="form-control" id="filter_tanggal_selesai" name="tanggal_selesai">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Main Table Card -->
    <section class="section">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="card-title mb-0" id="tableCardTitle">Daftar Surat</h5>
                @canany(['surat-masuk.create', 'surat-keluar.create'])
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahSurat">
                    <i class="bi bi-plus-circle-fill me-1"></i> Tambah Surat
                </button>
                @endcanany
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="surat-table" style="width:100%">
                        <thead>
                            <tr>
                                <th style="width: 35px;">No</th>
                                <th style="width: 100px;">Jenis</th>
                                <th>No. Surat</th>
                                <th>Tgl Surat</th>
                                <th>Asal / Tujuan</th>
                                <th>Perihal</th>
                                <th style="width: 100px;">Status</th>
                                <th style="width: 90px;">Arsip</th>
                                <th style="width: 125px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal Tambah Surat -->
<div class="modal fade" id="modalTambahSurat" tabindex="-1" aria-labelledby="modalTambahSuratLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white" id="modalTambahSuratLabel">
                    <i class="bi bi-plus-circle me-2"></i>Tambah Data Surat Baru
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formTambahSurat" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <!-- Pilihan Jenis Surat -->
                    <div class="mb-3 p-3 bg-light rounded border">
                        <label class="form-label fw-bold d-block mb-2">Pilih Jenis Surat <span class="text-danger">*</span></label>
                        <div class="btn-group w-100 mb-2" role="group">
                            @can('surat-masuk.create')
                            <input type="radio" class="btn-check jenis-surat-radio" name="jenis_surat" id="create_jenis_masuk" value="Masuk" {{ auth()->user()?->can('surat-masuk.create') ? 'checked' : '' }}>
                            <label class="btn btn-outline-success py-2" for="create_jenis_masuk">
                                <i class="bi bi-box-arrow-in-down me-1"></i> Surat Masuk
                            </label>
                            @endcan

                            @can('surat-keluar.create')
                            <input type="radio" class="btn-check jenis-surat-radio" name="jenis_surat" id="create_jenis_keluar" value="Keluar" {{ !auth()->user()?->can('surat-masuk.create') ? 'checked' : '' }}>
                            <label class="btn btn-outline-primary py-2" for="create_jenis_keluar">
                                <i class="bi bi-box-arrow-up-right me-1"></i> Surat Keluar
                            </label>
                            @endcan
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <!-- Input Nomor Surat Masuk -->
                            <div id="wrapper_create_nomor_masuk">
                                <div class="row g-2">
                                    <div class="col-md-4">
                                        <label for="create_no_agenda" class="form-label">No. Agenda</label>
                                        <input type="number" class="form-control bg-light font-monospace fw-bold text-center" id="create_no_agenda" name="no_agenda" placeholder="1" readonly>
                                        <small class="text-muted small">Otomatis urut</small>
                                    </div>
                                    <div class="col-md-8">
                                        <label for="create_nomor_surat_input" class="form-label">Nomor Surat <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="create_nomor_surat_input" placeholder="Contoh: 005/123/DISP-2026">
                                    </div>
                                </div>
                            </div>

                            <!-- Input Nomor Surat Keluar (Per Bagian) -->
                            <div id="wrapper_create_nomor_keluar" class="d-none">
                                <label class="form-label fw-bold mb-1">Nomor Surat Keluar <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control text-center font-monospace" id="create_nomor_kode" placeholder="800" style="max-width: 90px;" title="Kode Klasifikasi Surat (contoh: 800)">
                                    <span class="input-group-text fw-bold">/</span>
                                    <input type="text" class="form-control text-center font-monospace fw-bold bg-light" id="create_nomor_urut" placeholder="001" style="max-width: 85px;" title="Nomor Urut Otomatis (mulai 001)">
                                    <span class="input-group-text fw-bold">-</span>
                                    <input type="text" class="form-control text-center font-monospace" id="create_nomor_instansi" placeholder="SMK" style="max-width: 100px;" title="Kode Instansi / Unit (contoh: SMK)">
                                    <span class="input-group-text fw-bold">/</span>
                                    <input type="text" class="form-control text-center font-monospace" id="create_nomor_bulan" placeholder="XI" style="max-width: 85px;" title="Bulan Romawi (contoh: XI)">
                                </div>
                                <div class="mt-1 d-flex justify-content-between align-items-center flex-wrap gap-1">
                                    <small class="text-muted">Format: <strong class="text-primary font-monospace" id="create_nomor_preview">800/001-SMK/XI</strong></small>
                                    <button type="button" class="btn btn-xs btn-outline-primary py-0 px-2" id="btnRefreshNomorKeluar" title="Ambil nomor urut terbaru dari database">
                                        <i class="bi bi-arrow-repeat me-1"></i> No Urut Auto
                                    </button>
                                </div>
                            </div>

                            <!-- Hidden input nomor_surat yang dikirim ke backend -->
                            <input type="hidden" id="create_nomor_surat" name="nomor_surat" required>
                            <div class="invalid-feedback d-block" id="error_create_nomor_surat"></div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="create_tanggal_surat" class="form-label">Tanggal Surat <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="create_tanggal_surat" name="tanggal_surat" required value="{{ date('Y-m-d') }}">
                            <div class="invalid-feedback" id="error_create_tanggal_surat"></div>
                        </div>
                        <div class="col-md-3 mb-3 field-masuk-only">
                            <label for="create_tanggal_terima" class="form-label">Tanggal Terima <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="create_tanggal_terima" name="tanggal_terima" value="{{ date('Y-m-d') }}">
                            <small class="text-muted d-block mt-1">Otomatis hari ini</small>
                            <div class="invalid-feedback" id="error_create_tanggal_terima"></div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Khusus Surat Masuk: Asal Surat / Pengirim -->
                        <div class="col-md-6 mb-3 field-masuk-only">
                            <label for="create_asal_surat" class="form-label">Asal Surat / Pengirim <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="create_asal_surat" name="asal_surat" placeholder="Instansi / Pengirim">
                            <div class="invalid-feedback" id="error_create_asal_surat"></div>
                        </div>
                        <!-- Khusus Surat Keluar: Penerima Surat -->
                        <div class="col-md-6 mb-3 field-keluar-only d-none">
                            <label for="create_penerima_surat" class="form-label">Penerima Surat <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="create_penerima_surat" name="penerima_surat" placeholder="Contoh: Bapak/Ibu Dewan Guru">
                            <div class="invalid-feedback" id="error_create_penerima_surat"></div>
                        </div>
                        <!-- Tujuan Surat / Disposisi -->
                        <div class="col-md-6 mb-3" id="wrapper_create_tujuan">
                            <label for="create_tujuan_surat" class="form-label" id="label_create_tujuan">Tujuan / Disposisi Kepada <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="create_tujuan_surat" name="tujuan_surat" required placeholder="Contoh: SMK Negeri 1 Sadaniang">
                            <div class="invalid-feedback" id="error_create_tujuan_surat"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="create_perihal" class="form-label">Perihal Surat <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="create_perihal" name="perihal" rows="2" required placeholder="Contoh: Undangan Rapat Dewan Guru mengenai Penilaian Sumatif Akhir Semester (PSAS)"></textarea>
                        <div class="invalid-feedback" id="error_create_perihal"></div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3 field-masuk-only">
                            <label for="create_status_disposisi" class="form-label">Status Disposisi <span class="text-danger">*</span></label>
                            <select class="form-select" id="create_status_disposisi" name="status_disposisi">
                                <option value="Belum Didisposisikan" selected>Belum Didisposisikan</option>
                                <option value="Sudah Didisposisikan">Sudah Didisposisikan</option>
                                <option value="Selesai">Selesai</option>
                            </select>
                            <div class="invalid-feedback" id="error_create_status_disposisi"></div>
                        </div>
                        <div class="col-md-6 mb-3 field-masuk-only">
                            <label for="create_file_arsip" class="form-label">Upload Arsip Surat (PDF, JPG, PNG)</label>
                            <input type="file" class="form-control" id="create_file_arsip" name="file_arsip" accept=".pdf,.jpg,.jpeg,.png">
                            <small class="text-muted d-block mt-1">Maksimal ukuran file: 10 MB</small>
                            <div class="invalid-feedback" id="error_create_file_arsip"></div>
                        </div>
                    </div>

                    <!-- Bagian Khusus Surat Keluar: Isi Pengantar, Rincian Kegiatan, & Penutup -->
                    <div class="field-keluar-only d-none">
                        <div class="mb-3">
                            <label for="create_isi_surat" class="form-label fw-bold">Isi Surat / Pengantar <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="create_isi_surat" name="isi_surat" rows="3" placeholder="Paragraf pengantar / pembuka sebelum rincian pelaksanaan kegiatan..."></textarea>
                        </div>

                        <!-- Card Rincian Kegiatan -->
                        <div class="card border mb-3 bg-light">
                            <div class="card-body p-3">
                                <h6 class="fw-bold text-primary mb-3">
                                    <i class="bi bi-calendar-event me-2"></i>Rincian Waktu & Tempat Kegiatan
                                </h6>
                                <div class="row g-2">
                                    <div class="col-md-6 mb-2">
                                        <label for="create_hari_tanggal" class="form-label small fw-bold">Hari/Tanggal</label>
                                        <input type="text" class="form-control" id="create_hari_tanggal" name="hari_tanggal" placeholder="Contoh: Kamis, 12 November 2026">
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label for="create_waktu" class="form-label small fw-bold">Waktu</label>
                                        <input type="text" class="form-control" id="create_waktu" name="waktu" placeholder="Contoh: Pukul 09.00 WIB s.d. selesai">
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label for="create_tempat" class="form-label small fw-bold">Tempat</label>
                                        <input type="text" class="form-control" id="create_tempat" name="tempat" placeholder="Contoh: Ruang Guru SMK Negeri 1 Sadaniang">
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label for="create_agenda" class="form-label small fw-bold">Agenda Kegiatan</label>
                                        <input type="text" class="form-control" id="create_agenda" name="agenda" placeholder="Contoh: Rapat Persiapan dan Koordinasi Pelaksanaan Kegiatan.">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="create_penutup" class="form-label fw-bold">Paragraf Penutup Surat</label>
                            <textarea class="form-control" id="create_penutup" name="penutup" rows="3" placeholder="Paragraf penutup surat..."></textarea>
                        </div>
                    </div>

                    <!-- Bagian Khusus Surat Masuk: Keterangan -->
                    <div class="mb-3 field-masuk-only">
                        <label for="create_keterangan" class="form-label">Keterangan / Catatan Tambahan</label>
                        <textarea class="form-control" id="create_keterangan" name="keterangan" rows="3" placeholder="Catatan tambahan (opsional)"></textarea>
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

<!-- Modal Edit Surat -->
<div class="modal fade" id="modalEditSurat" tabindex="-1" aria-labelledby="modalEditSuratLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title text-white" id="modalEditSuratLabel">
                    <i class="bi bi-pencil-square me-2"></i>Edit Data Surat
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditSurat" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="edit_id">
                <div class="modal-body">
                    <!-- Pilihan Jenis Surat Edit -->
                    <div class="mb-3 p-3 bg-light rounded border">
                        <label class="form-label fw-bold d-block mb-2">Jenis Surat <span class="text-danger">*</span></label>
                        <div class="btn-group w-100 mb-2" role="group">
                            <input type="radio" class="btn-check edit-jenis-radio" name="jenis_surat" id="edit_jenis_masuk" value="Masuk">
                            <label class="btn btn-outline-success py-2" for="edit_jenis_masuk">
                                <i class="bi bi-box-arrow-in-down me-1"></i> Surat Masuk
                            </label>

                            <input type="radio" class="btn-check edit-jenis-radio" name="jenis_surat" id="edit_jenis_keluar" value="Keluar">
                            <label class="btn btn-outline-primary py-2" for="edit_jenis_keluar">
                                <i class="bi bi-box-arrow-up-right me-1"></i> Surat Keluar
                            </label>
                        </div>
                        <div class="alert alert-light-primary border-primary d-none mt-2 mb-0" id="info_edit_surat_keluar">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-magic fs-4 text-primary me-2"></i>
                                <div>
                                    <strong class="text-primary">Pembaruan Surat Keluar Otomatis:</strong>
                                    <p class="mb-0 small text-muted">Saat memperbarui Surat Keluar, tampilan surat resmi akan otomatis di-generate ulang dengan data terbaru dan tersimpan di arsip.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <!-- Input Nomor Surat Masuk (Edit) -->
                            <div id="wrapper_edit_nomor_masuk">
                                <div class="row g-2">
                                    <div class="col-md-4">
                                        <label for="edit_no_agenda" class="form-label">No. Agenda</label>
                                        <input type="number" class="form-control bg-light font-monospace fw-bold text-center" id="edit_no_agenda" name="no_agenda" readonly>
                                        <small class="text-muted small">No urut agenda</small>
                                    </div>
                                    <div class="col-md-8">
                                        <label for="edit_nomor_surat_input" class="form-label">Nomor Surat <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="edit_nomor_surat_input">
                                    </div>
                                </div>
                            </div>

                            <!-- Input Nomor Surat Keluar Per Bagian (Edit) -->
                            <div id="wrapper_edit_nomor_keluar" class="d-none">
                                <label class="form-label fw-bold mb-1">Nomor Surat Keluar <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control text-center font-monospace" id="edit_nomor_kode" placeholder="800" style="max-width: 90px;" title="Kode Klasifikasi">
                                    <span class="input-group-text fw-bold">/</span>
                                    <input type="text" class="form-control text-center font-monospace fw-bold bg-light" id="edit_nomor_urut" placeholder="001" style="max-width: 85px;" title="Nomor Urut">
                                    <span class="input-group-text fw-bold">-</span>
                                    <input type="text" class="form-control text-center font-monospace" id="edit_nomor_instansi" placeholder="SMK" style="max-width: 100px;" title="Kode Instansi">
                                    <span class="input-group-text fw-bold">/</span>
                                    <input type="text" class="form-control text-center font-monospace" id="edit_nomor_bulan" placeholder="XI" style="max-width: 85px;" title="Bulan Romawi">
                                </div>
                                <div class="mt-1">
                                    <small class="text-muted">Format: <strong class="text-primary font-monospace" id="edit_nomor_preview">800/001-SMK/XI</strong></small>
                                </div>
                            </div>

                            <!-- Hidden input nomor_surat yang dikirim ke backend -->
                            <input type="hidden" id="edit_nomor_surat" name="nomor_surat" required>
                            <div class="invalid-feedback d-block" id="error_edit_nomor_surat"></div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="edit_tanggal_surat" class="form-label">Tanggal Surat <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="edit_tanggal_surat" name="tanggal_surat" required>
                            <div class="invalid-feedback" id="error_edit_tanggal_surat"></div>
                        </div>
                        <div class="col-md-3 mb-3 field-edit-masuk-only">
                            <label for="edit_tanggal_terima" class="form-label">Tanggal Terima <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="edit_tanggal_terima" name="tanggal_terima">
                            <div class="invalid-feedback" id="error_edit_tanggal_terima"></div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Khusus Surat Masuk: Asal Surat / Pengirim (Edit) -->
                        <div class="col-md-6 mb-3 field-edit-masuk-only">
                            <label for="edit_asal_surat" class="form-label">Asal Surat / Pengirim <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_asal_surat" name="asal_surat">
                            <div class="invalid-feedback" id="error_edit_asal_surat"></div>
                        </div>
                        <!-- Khusus Surat Keluar: Penerima Surat (Edit) -->
                        <div class="col-md-6 mb-3 field-edit-keluar-only d-none">
                            <label for="edit_penerima_surat" class="form-label">Penerima Surat <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_penerima_surat" name="penerima_surat" placeholder="Contoh: Bapak/Ibu Dewan Guru">
                            <div class="invalid-feedback" id="error_edit_penerima_surat"></div>
                        </div>
                        <!-- Tujuan Surat / Disposisi (Edit) -->
                        <div class="col-md-6 mb-3" id="wrapper_edit_tujuan">
                            <label for="edit_tujuan_surat" class="form-label" id="label_edit_tujuan">Tujuan / Disposisi <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_tujuan_surat" name="tujuan_surat" required>
                            <div class="invalid-feedback" id="error_edit_tujuan_surat"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="edit_perihal" class="form-label">Perihal Surat <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="edit_perihal" name="perihal" rows="2" required></textarea>
                        <div class="invalid-feedback" id="error_edit_perihal"></div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3 field-edit-masuk-only">
                            <label for="edit_status_disposisi" class="form-label">Status Disposisi <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_status_disposisi" name="status_disposisi">
                                <option value="Belum Didisposisikan">Belum Didisposisikan</option>
                                <option value="Sudah Didisposisikan">Sudah Didisposisikan</option>
                                <option value="Selesai">Selesai</option>
                            </select>
                            <div class="invalid-feedback" id="error_edit_status_disposisi"></div>
                        </div>
                        <div class="col-md-6 mb-3 field-edit-masuk-only">
                            <label for="edit_file_arsip" class="form-label">Ganti File Arsip (Opsional)</label>
                            <input type="file" class="form-control" id="edit_file_arsip" name="file_arsip" accept=".pdf,.jpg,.jpeg,.png">
                            <small class="text-muted d-block mt-1">Kosongkan jika tidak ingin mengganti file. Maksimal: 10 MB</small>
                            <div id="edit_current_file_info" class="mt-2"></div>
                            <div class="invalid-feedback" id="error_edit_file_arsip"></div>
                        </div>
                    </div>

                    <!-- Bagian Khusus Surat Keluar: Isi Pengantar, Rincian Kegiatan, & Penutup (Edit) -->
                    <div class="field-edit-keluar-only d-none">
                        <div class="mb-3">
                            <label for="edit_isi_surat" class="form-label fw-bold">Isi Surat / Pengantar <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="edit_isi_surat" name="isi_surat" rows="3" placeholder="Paragraf pengantar / pembuka sebelum rincian pelaksanaan kegiatan..."></textarea>
                        </div>

                        <!-- Card Rincian Kegiatan -->
                        <div class="card border mb-3 bg-light">
                            <div class="card-body p-3">
                                <h6 class="fw-bold text-primary mb-3">
                                    <i class="bi bi-calendar-event me-2"></i>Rincian Waktu & Tempat Kegiatan
                                </h6>
                                <div class="row g-2">
                                    <div class="col-md-6 mb-2">
                                        <label for="edit_hari_tanggal" class="form-label small fw-bold">Hari/Tanggal</label>
                                        <input type="text" class="form-control" id="edit_hari_tanggal" name="hari_tanggal" placeholder="Contoh: Kamis, 12 November 2026">
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label for="edit_waktu" class="form-label small fw-bold">Waktu</label>
                                        <input type="text" class="form-control" id="edit_waktu" name="waktu" placeholder="Contoh: Pukul 09.00 WIB s.d. selesai">
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label for="edit_tempat" class="form-label small fw-bold">Tempat</label>
                                        <input type="text" class="form-control" id="edit_tempat" name="tempat" placeholder="Contoh: Ruang Guru SMK Negeri 1 Sadaniang">
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label for="edit_agenda" class="form-label small fw-bold">Agenda Kegiatan</label>
                                        <input type="text" class="form-control" id="edit_agenda" name="agenda" placeholder="Contoh: Rapat Persiapan dan Koordinasi Pelaksanaan Kegiatan.">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="edit_penutup" class="form-label fw-bold">Paragraf Penutup Surat</label>
                            <textarea class="form-control" id="edit_penutup" name="penutup" rows="3" placeholder="Paragraf penutup surat..."></textarea>
                        </div>
                    </div>

                    <!-- Bagian Khusus Surat Masuk: Keterangan (Edit) -->
                    <div class="mb-3 field-edit-masuk-only">
                        <label for="edit_keterangan" class="form-label" id="label_edit_keterangan">Keterangan / Catatan Tambahan</label>
                        <textarea class="form-control" id="edit_keterangan" name="keterangan" rows="3"></textarea>
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

<!-- Modal Detail Surat -->
<div class="modal fade" id="modalDetailSurat" tabindex="-1" aria-labelledby="modalDetailSuratLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title text-dark" id="modalDetailSuratLabel">
                    <i class="bi bi-info-circle-fill me-2"></i>Detail Surat
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered mb-0">
                        <tbody>
                            <tr>
                                <th style="width: 32%;" class="bg-light">Jenis Surat</th>
                                <td id="detail_jenis_surat" class="fw-bold">-</td>
                            </tr>
                            <tr id="row_detail_no_agenda">
                                <th class="bg-light">No. Agenda</th>
                                <td id="detail_no_agenda" class="fw-bold text-primary">-</td>
                            </tr>
                            <tr>
                                <th class="bg-light">Nomor Surat</th>
                                <td id="detail_nomor_surat" class="fw-bold text-primary">-</td>
                            </tr>
                            <tr>
                                <th class="bg-light">Tanggal Surat</th>
                                <td id="detail_tanggal_surat">-</td>
                            </tr>
                            <tr id="row_detail_tanggal_terima">
                                <th class="bg-light">Tanggal Terima</th>
                                <td id="detail_tanggal_terima">-</td>
                            </tr>
                            <tr id="row_detail_asal">
                                <th class="bg-light">Asal Surat / Pengirim</th>
                                <td id="detail_asal_surat">-</td>
                            </tr>
                            <tr id="row_detail_penerima">
                                <th class="bg-light">Penerima Surat</th>
                                <td id="detail_penerima_surat" class="fw-bold">-</td>
                            </tr>
                            <tr>
                                <th class="bg-light" id="th_detail_tujuan">Tujuan / Penerima</th>
                                <td id="detail_tujuan_surat" class="fw-bold">-</td>
                            </tr>
                            <tr>
                                <th class="bg-light">Perihal</th>
                                <td id="detail_perihal">-</td>
                            </tr>
                            <tr id="row_detail_status_disposisi">
                                <th class="bg-light">Status Disposisi</th>
                                <td id="detail_status_disposisi">-</td>
                            </tr>
                            <tr>
                                <th class="bg-light">Keterangan / Isi Surat</th>
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
            <div class="modal-footer" id="modalDetailFooter">
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
                <div id="previewContainer" style="width: 100%; height: 650px;"></div>
            </div>
            <div class="modal-footer bg-light justify-content-between">
                <div>
                    <span id="previewFileName" class="text-muted fw-bold"></span>
                </div>
                <div>
                    <a href="#" id="previewDownloadBtn" class="btn btn-primary" target="_blank">
                        <i class="bi bi-download me-1"></i> Unduh Berkas
                    </a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Pop-up Hasil Generate Surat Keluar -->
<div class="modal fade" id="modalHasilSuratKeluar" tabindex="-1" aria-labelledby="modalHasilSuratKeluarLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title text-white" id="modalHasilSuratKeluarLabel">
                    <i class="bi bi-check-circle-fill me-2"></i>Surat Keluar Berhasil Dihasilkan & Diarsipkan!
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3 bg-light">
                <div class="alert alert-success d-flex align-items-center mb-3 py-2 px-3">
                    <i class="bi bi-check-circle fs-4 me-2"></i>
                    <div>
                        <strong>Dokumen Resmi Berhasil Dibuat:</strong><br>
                        Surat resmi telah dibuat otomatis ber-kop SMK Negeri 1 Sadaniang lengkap dengan format cetak resmi Kepala Sekolah, serta langsung tersimpan ke sistem arsip surat.
                    </div>
                </div>

                <!-- Preview Gambar Hasil Render Surat -->
                <div class="text-center p-2 bg-white rounded border shadow-sm mb-3" style="max-height: 480px; overflow-y: auto;">
                    <img id="hasilSuratPreviewImg" src="" alt="Pratinjau Surat Keluar" style="max-width: 100%; height: auto; box-shadow: 0 2px 8px rgba(0,0,0,0.12); border-radius: 4px;">
                </div>

                <!-- Pilihan Unduhan -->
                <div class="d-flex flex-wrap gap-2 justify-content-center">
                    <button type="button" class="btn btn-success px-3" id="btnUnduhGambarModal">
                        <i class="bi bi-file-earmark-image-fill me-1"></i> Unduh Gambar (PNG)
                    </button>
                    <button type="button" class="btn btn-danger px-3" id="btnUnduhPDFModal">
                        <i class="bi bi-file-earmark-pdf-fill me-1"></i> Unduh PDF (A4)
                    </button>
                    <a href="#" target="_blank" class="btn btn-primary px-3" id="btnCetakSuratModal">
                        <i class="bi bi-printer-fill me-1"></i> Cetak Dokumen
                    </a>
                </div>
            </div>
            <div class="modal-footer bg-white justify-content-between">
                <small class="text-muted"><i class="bi bi-info-circle me-1"></i>File arsip otomatis tersimpan dan dapat diunduh kapan saja pada tabel.</small>
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Tutup & Selesai</button>
            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- OFFSCREEN TEMPLATE SURAT UNTUK HTML2CANVAS -->
<!-- ========================================== -->
<style>
    #suratRenderElement,
    #suratRenderElement * {
        font-family: "Times New Roman", Times, serif !important;
        color: #000000 !important;
        box-sizing: border-box !important;
    }
    #suratRenderElement h1,
    #suratRenderElement h2,
    #suratRenderElement h3,
    #suratRenderElement h4,
    #suratRenderElement h5,
    #suratRenderElement h6,
    #suratRenderElement p,
    #suratRenderElement span,
    #suratRenderElement td,
    #suratRenderElement strong,
    #suratRenderElement a {
        font-family: "Times New Roman", Times, serif !important;
        color: #000000 !important;
    }
    #suratRenderElement h4 {
        margin: 0 !important;
        font-size: 14.5px !important;
        font-weight: normal !important;
        letter-spacing: 0.5px !important;
        text-transform: uppercase !important;
        line-height: 1.1 !important;
    }
    #suratRenderElement h2 {
        margin: 1px 0 2px 0 !important;
        font-size: 19.5px !important;
        font-weight: 800 !important;
        letter-spacing: 0.8px !important;
        text-transform: uppercase !important;
        line-height: 1.1 !important;
    }
    #suratRenderElement p {
        margin: 0 !important;
        padding: 0 !important;
        font-size: 12px !important;
        line-height: 1.15 !important;
    }
</style>
<div id="suratOffscreenWrapper" style="position: absolute; left: -9999px; top: 0; width: 210mm; z-index: -9999; background: #ffffff;">
    <div id="suratRenderElement" style="width: 210mm; min-height: 297mm; padding: 16mm 22mm 20mm 22mm; background: #ffffff; font-family: 'Times New Roman', Times, serif !important; color: #000000 !important; font-size: 12pt; line-height: 1.5; box-sizing: border-box; position: relative;">
        <!-- KOP SURAT -->
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 4px;">
            <tr>
                <td style="width: 80px; text-align: left; vertical-align: middle;">
                    <img src="{{ asset('assets/surat/logo_kalbar.png') }}" style="width: 78px; height: auto; display: block;" alt="Logo Pemprov Kalbar">
                </td>
                <td style="text-align: center; padding: 0 10px; vertical-align: middle;">
                    <h4 style="margin: 0 !important; font-size: 14.5px !important; font-weight: normal !important; letter-spacing: 0.5px !important; text-transform: uppercase !important; line-height: 1.1 !important; color: #000000 !important; font-family: 'Times New Roman', Times, serif !important;">PEMERINTAH PROVINSI KALIMANTAN BARAT</h4>
                    <h2 style="margin: 1px 0 2px 0 !important; font-size: 19.5px !important; font-weight: 800 !important; letter-spacing: 0.8px !important; text-transform: uppercase !important; line-height: 1.1 !important; color: #000000 !important; font-family: 'Times New Roman', Times, serif !important;">SMK NEGERI 1 SADANIANG</h2>
                    <p style="margin: 0 !important; padding: 0 !important; font-size: 12px !important; line-height: 1.15 !important; color: #000000 !important; font-family: 'Times New Roman', Times, serif !important;">Jalan Raya Pudak, Provinsi Kalimantan Barat, Kode Pos 78361</p>
                    <p style="margin: 0 !important; padding: 0 !important; font-size: 12px !important; line-height: 1.15 !important; color: #000000 !important; font-family: 'Times New Roman', Times, serif !important;">Hp. 081348969697, Website: <span style="text-decoration: underline !important; color: #000000 !important; font-family: 'Times New Roman', Times, serif !important;">http://www.smkn1sadaniang.sch.id</span></p>
                </td>
                <td style="width: 100px; height: auto; text-align: right; vertical-align: middle;">
                    <img src="{{ asset('assets/surat/logo_smk.png') }}" style="width: 95px; height: auto; display: block; margin-left: auto;" alt="Logo SMK">
                </td>
            </tr>
        </table>
        
        <!-- GARIS GANDA KOP -->
        <div style="border-top: 3.5px solid #000000; border-bottom: 1.2px solid #000000; height: 2px; margin: 6px 0 18px 0;"></div>

        <!-- TANGGAL SURAT -->
        <div id="render_surat_tanggal" style="text-align: right; margin-bottom: 16px; font-size: 13.5px;">
            Sadaniang, 10 November 2026
        </div>

        <!-- NOMOR & PERIHAL -->
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 13.5px;">
            <tr>
                <td style="width: 85px; vertical-align: top; padding: 2px 0;">Nomor</td>
                <td style="width: 15px; vertical-align: top; padding: 2px 0;">:</td>
                <td style="vertical-align: top; padding: 2px 0;"><strong id="render_surat_nomor">800/134-SMKN1SDG/XI</strong></td>
            </tr>
            <tr>
                <td style="vertical-align: top; padding: 2px 0;">Perihal</td>
                <td style="vertical-align: top; padding: 2px 0;">:</td>
                <td style="vertical-align: top; padding: 2px 0;" id="render_surat_perihal">Undangan Rapat Dewan Guru mengenai Penilaian Sumatif Akhir Semester (PSAS) Ganjil Tahun Pelajaran 2026/2027</td>
            </tr>
        </table>

        <!-- TUJUAN SURAT -->
        <div style="margin-bottom: 20px; line-height: 1.5; font-size: 13.5px;">
            Kepada Yth.<br>
            <strong id="render_surat_penerima">Bapak/Ibu Dewan Guru</strong><br>
            <span id="render_surat_tujuan_wrapper"><span id="render_surat_tujuan">SMK Negeri 1 Sadaniang</span><br></span>
            di Tempat
        </div>

        <!-- SALAM PEMBUKA -->
        <div style="margin-bottom: 14px; font-size: 13.5px;">
            Dengan hormat,
        </div>

        <!-- ISI SURAT / PENGANTAR -->
        <div id="render_surat_isi" style="text-align: justify; text-justify: inter-word; line-height: 1.6; margin-bottom: 16px; font-size: 13.5px;">
            Sehubungan dengan akan dilaksanakannya kegiatan...
        </div>

        <!-- RINCIAN KEGIATAN -->
        <table id="render_surat_rincian" style="margin: 12px 0 16px 20px; border-collapse: collapse; font-size: 13.5px;">
            <tr id="render_row_hari_tanggal">
                <td style="width: 120px; vertical-align: top; padding: 3px 6px 3px 0;">Hari/Tanggal</td>
                <td style="width: 15px; vertical-align: top; padding: 3px 6px 3px 0;">:</td>
                <td style="vertical-align: top; padding: 3px 6px 3px 0;"><strong id="render_surat_hari_tanggal">Kamis, 12 November 2026</strong></td>
            </tr>
            <tr id="render_row_waktu">
                <td style="vertical-align: top; padding: 3px 6px 3px 0;">Waktu</td>
                <td style="vertical-align: top; padding: 3px 6px 3px 0;">:</td>
                <td style="vertical-align: top; padding: 3px 6px 3px 0;" id="render_surat_waktu">Pukul 09.00 WIB s.d. selesai</td>
            </tr>
            <tr id="render_row_tempat">
                <td style="vertical-align: top; padding: 3px 6px 3px 0;">Tempat</td>
                <td style="vertical-align: top; padding: 3px 6px 3px 0;">:</td>
                <td style="vertical-align: top; padding: 3px 6px 3px 0;" id="render_surat_tempat">Ruang Guru SMK Negeri 1 Sadaniang</td>
            </tr>
            <tr id="render_row_agenda">
                <td style="vertical-align: top; padding: 3px 6px 3px 0;">Agenda</td>
                <td style="vertical-align: top; padding: 3px 6px 3px 0;">:</td>
                <td style="vertical-align: top; padding: 3px 6px 3px 0;" id="render_surat_agenda">Rapat Persiapan dan Koordinasi Pelaksanaan Kegiatan.</td>
            </tr>
        </table>

        <!-- PARAGRAF PENEGASAN & PENUTUP -->
        <div id="render_surat_penutup" style="text-align: justify; line-height: 1.6; margin-bottom: 24px; font-size: 13.5px;">
            Mengingat pentingnya agenda tersebut, kami mengharapkan kehadiran Bapak/Ibu tepat pada waktunya agar seluruh rangkaian kegiatan dapat dikoordinasikan dan berjalan dengan baik.
            <br><br>
            Demikian surat ini kami sampaikan. Atas perhatian, kehadiran, dan kerja samanya, kami mengucapkan terima kasih.
        </div>

        <!-- TANDA TANGAN (TANPA STEMPEL) -->
        <div style="width: 100%; margin-top: 30px;">
            <div style="float: right; width: 260px; text-align: left; position: relative; font-size: 13.5px; line-height: 1.4;">
                Mengetahui,<br>
                Kepala Sekolah
                
                <span style="font-weight: bold; text-decoration: underline; margin-top: 75px; display: block; font-size: 13.5px;">Fransiska Ika, S.S, M.Pd.</span>
                <span style="display: block; margin-top: 2px; font-size: 13px;">NIP. 19850122 200904 2 002</span>
            </div>
            <div style="clear: both;"></div>
        </div>
    </div>
</div>

@endsection

@push('js')
<!-- html2canvas & jsPDF CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<script>
$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    let currentJenis = "{{ $defaultJenis }}";

    // Variabel penyimpan file hasil generate untuk modal hasil
    let lastGeneratedData = {
        nomorSurat: '',
        canvas: null,
        dataUrl: '',
        cetakUrl: ''
    };

    // Fungsi format tanggal ke format surat Indonesia (contoh: Sadaniang, 10 November 2026)
    function formatTanggalSuratIndo(dateStr) {
        if (!dateStr) return 'Sadaniang, ' + new Date().getDate() + ' November ' + new Date().getFullYear();
        const parts = dateStr.split('-');
        if (parts.length !== 3) return 'Sadaniang, ' + dateStr;
        const year = parts[0];
        const month = parseInt(parts[1], 10);
        const day = parseInt(parts[2], 10);
        const namaBulan = [
            "", "Januari", "Februari", "Maret", "April", "Mei", "Juni",
            "Juli", "Agustus", "September", "Oktober", "November", "Desember"
        ];
        return 'Sadaniang, ' + day + ' ' + (namaBulan[month] || '') + ' ' + year;
    }

    // Helper Konversi Bulan ke Angka Romawi (I - XII)
    function getBulanRomawi(dateStr) {
        const romawi = ["", "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII"];
        if (!dateStr) {
            return romawi[new Date().getMonth() + 1] || 'I';
        }
        const parts = dateStr.split('-');
        if (parts.length >= 2) {
            const m = parseInt(parts[1], 10);
            return romawi[m] || 'I';
        }
        return 'I';
    }

    // Update nilai gabungan nomor surat keluar pada Form Tambah
    function updateCreateNomorKeluar() {
        let kode = ($('#create_nomor_kode').val() || '').trim() || '800';
        let urut = ($('#create_nomor_urut').val() || '').trim() || '001';
        let instansi = ($('#create_nomor_instansi').val() || '').trim() || 'SMK';
        let bulan = ($('#create_nomor_bulan').val() || '').trim() || 'XI';

        let fullNomor = `${kode}/${urut}-${instansi}/${bulan}`;
        $('#create_nomor_preview').text(fullNomor);
        $('#create_nomor_surat').val(fullNomor);
    }

    // Ambil nomor urut otomatis berikutnya dari database
    function fetchNextNomorKeluar(callback) {
        $.ajax({
            url: "{{ route('surat.next-nomor-keluar') }}",
            type: "GET",
            success: function(res) {
                if (res.success) {
                    if (!$('#create_nomor_kode').val()) {
                        $('#create_nomor_kode').val(res.default_kode);
                    }
                    $('#create_nomor_urut').val(res.next_urut);
                    if (!$('#create_nomor_instansi').val()) {
                        $('#create_nomor_instansi').val(res.default_instansi);
                    }
                    let romawi = getBulanRomawi($('#create_tanggal_surat').val());
                    $('#create_nomor_bulan').val(romawi || res.default_bulan);
                    updateCreateNomorKeluar();
                    if (callback) callback(res);
                }
            }
        });
    }

    // Ambil nomor agenda otomatis berikutnya untuk surat masuk (mulai dari 1)
    function fetchNextNoAgenda(callback) {
        $.ajax({
            url: "{{ route('surat.next-no-agenda') }}",
            type: "GET",
            success: function(res) {
                if (res.success) {
                    $('#create_no_agenda').val(res.next_agenda);
                    if (callback) callback(res);
                }
            }
        });
    }

    // Update nilai gabungan nomor surat keluar pada Form Edit
    function updateEditNomorKeluar() {
        let kode = ($('#edit_nomor_kode').val() || '').trim() || '800';
        let urut = ($('#edit_nomor_urut').val() || '').trim() || '001';
        let instansi = ($('#edit_nomor_instansi').val() || '').trim() || 'SMK';
        let bulan = ($('#edit_nomor_bulan').val() || '').trim() || 'XI';

        let fullNomor = `${kode}/${urut}-${instansi}/${bulan}`;
        $('#edit_nomor_preview').text(fullNomor);
        $('#edit_nomor_surat').val(fullNomor);
    }

    // Parsing string nomor surat keluar ke 4 komponen per kata
    function parseNomorSuratKeluar(nomor) {
        if (!nomor) {
            return { kode: '800', urut: '001', instansi: 'SMK', bulan: 'XI' };
        }
        // Pola: {kode}/{urut}-{instansi}/{bulan}
        const regex = /^([^\/]+)\/([0-9]+)-([^\/]+)\/(.+)$/i;
        const match = nomor.match(regex);
        if (match) {
            return {
                kode: match[1],
                urut: match[2],
                instansi: match[3],
                bulan: match[4]
            };
        }
        // Fallback jika pemisah sedikit berbeda
        const slashParts = nomor.split('/');
        if (slashParts.length >= 3) {
            let midParts = slashParts[1].split('-');
            return {
                kode: slashParts[0] || '800',
                urut: midParts[0] || '001',
                instansi: midParts[1] || 'SMK',
                bulan: slashParts[2] || 'XI'
            };
        }
        return { kode: '800', urut: '001', instansi: 'SMK', bulan: 'XI' };
    }

    // Event input nomor surat masuk
    $('#create_nomor_surat_input').on('input', function() {
        $('#create_nomor_surat').val($(this).val());
    });
    $('#edit_nomor_surat_input').on('input', function() {
        $('#edit_nomor_surat').val($(this).val());
    });

    // Event input nomor surat keluar (per bagian)
    $('#create_nomor_kode, #create_nomor_urut, #create_nomor_instansi, #create_nomor_bulan').on('input', function() {
        updateCreateNomorKeluar();
    });
    $('#edit_nomor_kode, #edit_nomor_urut, #edit_nomor_instansi, #edit_nomor_bulan').on('input', function() {
        updateEditNomorKeluar();
    });

    // Tombol refresh nomor urut auto
    $('#btnRefreshNomorKeluar').on('click', function() {
        let btn = $(this);
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');
        fetchNextNomorKeluar(function() {
            btn.prop('disabled', false).html('<i class="bi bi-arrow-repeat me-1"></i> No Urut Auto');
        });
    });

    // Perubahan tanggal otomatis menyesuaikan bulan romawi untuk surat keluar
    $('#create_tanggal_surat').on('change', function() {
        let currentJenis = $('input[name="jenis_surat"]:checked', '#formTambahSurat').val();
        if (currentJenis === 'Keluar') {
            $('#create_nomor_bulan').val(getBulanRomawi($(this).val()));
            updateCreateNomorKeluar();
        }
    });
    $('#edit_tanggal_surat').on('change', function() {
        let currentJenis = $('input[name="jenis_surat"]:checked', '#formEditSurat').val();
        if (currentJenis === 'Keluar') {
            $('#edit_nomor_bulan').val(getBulanRomawi($(this).val()));
            updateEditNomorKeluar();
        }
    });

    // Fungsi toggle UI berdasarkan jenis surat pada Form Tambah
    function toggleCreateJenisForm(jenis) {
        if (jenis === 'Keluar') {
            $('.field-masuk-only').hide();
            $('.field-keluar-only').removeClass('d-none');
            $('#wrapper_create_nomor_masuk').addClass('d-none');
            $('#wrapper_create_nomor_keluar').removeClass('d-none');
            $('#info_create_surat_keluar').removeClass('d-none');
            $('#label_create_tujuan').html('Tujuan Surat <span class="text-danger">*</span>');
            $('#create_tujuan_surat').attr('placeholder', 'Contoh: SMK Negeri 1 Sadaniang');
            if (!$('#create_penerima_surat').val()) {
                $('#create_penerima_surat').val('Bapak/Ibu Dewan Guru');
            }
            if (!$('#create_tujuan_surat').val()) {
                $('#create_tujuan_surat').val('SMK Negeri 1 Sadaniang');
            }
            $('#create_penerima_surat').attr('required', true);
            $('#create_asal_surat').removeAttr('required');
            $('#create_tanggal_terima').removeAttr('required');
            $('#create_status_disposisi').removeAttr('required');
            $('#create_file_arsip').val('');
            $('#create_no_agenda').val('');

            // Nilai default isi, rincian, dan penutup jika masih kosong
            if (!$('#create_isi_surat').val()) {
                let perihal = $('#create_perihal').val() || 'Penilaian Sumatif Akhir Semester (PSAS) Ganjil Tahun Pelajaran 2026/2027';
                $('#create_isi_surat').val(`Sehubungan dengan akan dilaksanakannya kegiatan ${perihal}, yang pelaksanaannya mengacu pada edaran Dinas Pendidikan dan Kebudayaan Provinsi Kalimantan Barat,Nomor : 400.3/4715/DIKBUD-C. Maka kami mengundang Bapak/Ibu Dewan Guru SMK Negeri 1 Sadaniang untuk menghadiri rapat persiapan dan koordinasi pelaksanaan kegiatan tersebut, yang akan dilaksanakan pada:`);
            }
            if (!$('#create_hari_tanggal').val()) {
                $('#create_hari_tanggal').val('Kamis, 12 November 2026');
            }
            if (!$('#create_waktu').val()) {
                $('#create_waktu').val('Pukul 09.00 WIB s.d. selesai');
            }
            if (!$('#create_tempat').val()) {
                $('#create_tempat').val('Ruang Guru SMK Negeri 1 Sadaniang');
            }
            if (!$('#create_agenda').val()) {
                $('#create_agenda').val('Rapat Persiapan dan Koordinasi Pelaksanaan Kegiatan.');
            }
            if (!$('#create_penutup').val()) {
                $('#create_penutup').val(`Mengingat pentingnya agenda tersebut, kami mengharapkan kehadiran Bapak/Ibu tepat pada waktunya agar seluruh rangkaian kegiatan dapat dikoordinasikan dan berjalan dengan baik.\n\nDemikian surat ini kami sampaikan. Atas perhatian, kehadiran, dan kerja samanya, kami mengucapkan terima kasih.`);
            }

            // Ambil nomor urut otomatis jika belum terisi
            if (!$('#create_nomor_urut').val()) {
                fetchNextNomorKeluar();
            } else {
                updateCreateNomorKeluar();
            }
        } else {
            $('.field-masuk-only').show();
            $('.field-keluar-only').addClass('d-none');
            $('#wrapper_create_nomor_masuk').removeClass('d-none');
            $('#wrapper_create_nomor_keluar').addClass('d-none');
            $('#create_nomor_surat').val($('#create_nomor_surat_input').val());
            $('#info_create_surat_keluar').addClass('d-none');
            $('#label_create_tujuan').html('Tujuan / Disposisi Kepada <span class="text-danger">*</span>');
            $('#create_tujuan_surat').attr('placeholder', 'Contoh: Kepala Dinas / Sekretaris / Kasubag');
            $('#create_penerima_surat').removeAttr('required');
            $('#create_asal_surat').attr('required', true);
            $('#create_tanggal_terima').attr('required', true);
            $('#create_status_disposisi').attr('required', true);

            // Ambil nomor agenda otomatis untuk surat masuk (mulai dari 1)
            fetchNextNoAgenda();
        }
    }

    // Fungsi toggle UI berdasarkan jenis surat pada Form Edit
    function toggleEditJenisForm(jenis) {
        if (jenis === 'Keluar') {
            $('.field-edit-masuk-only').hide();
            $('.field-edit-keluar-only').removeClass('d-none');
            $('#wrapper_edit_nomor_masuk').addClass('d-none');
            $('#wrapper_edit_nomor_keluar').removeClass('d-none');
            $('#info_edit_surat_keluar').removeClass('d-none');
            $('#label_edit_tujuan').html('Tujuan Surat <span class="text-danger">*</span>');
            $('#edit_tujuan_surat').attr('placeholder', 'Contoh: SMK Negeri 1 Sadaniang');
            $('#edit_penerima_surat').attr('required', true);
            $('#edit_asal_surat').removeAttr('required');
            $('#edit_tanggal_terima').removeAttr('required');
            $('#edit_status_disposisi').removeAttr('required');
            $('#edit_file_arsip').val('');
            $('#edit_no_agenda').val('');
            updateEditNomorKeluar();
        } else {
            $('.field-edit-masuk-only').show();
            $('.field-edit-keluar-only').addClass('d-none');
            $('#wrapper_edit_nomor_masuk').removeClass('d-none');
            $('#wrapper_edit_nomor_keluar').addClass('d-none');
            $('#edit_nomor_surat').val($('#edit_nomor_surat_input').val());
            $('#info_edit_surat_keluar').addClass('d-none');
            $('#label_edit_tujuan').html('Tujuan / Disposisi <span class="text-danger">*</span>');
            $('#edit_tujuan_surat').attr('placeholder', '');
            $('#edit_penerima_surat').removeAttr('required');
            $('#edit_asal_surat').attr('required', true);
            $('#edit_tanggal_terima').attr('required', true);
            $('#edit_status_disposisi').attr('required', true);
        }
    }

    // Event listener switch radio jenis surat
    $('.jenis-surat-radio').on('change', function() {
        toggleCreateJenisForm($(this).val());
    });
    $('.edit-jenis-radio').on('change', function() {
        toggleEditJenisForm($(this).val());
    });

    // Sisipkan Contoh Isi Surat pada Form Tambah
    $('#btnTemplateKeterangan').on('click', function() {
        let perihal = $('#create_perihal').val() || 'Penilaian Sumatif Akhir Semester (PSAS) Ganjil';
        let templateText = `Sehubungan dengan akan dilaksanakannya kegiatan ${perihal}, yang pelaksanaannya mengacu pada edaran Dinas Pendidikan dan Kebudayaan Provinsi Kalimantan Barat,Nomor : 400.3/4715/DIKBUD-C. Maka kami mengundang Bapak/Ibu Dewan Guru SMK Negeri 1 Sadaniang untuk menghadiri rapat persiapan dan koordinasi pelaksanaan kegiatan tersebut, yang akan dilaksanakan pada:

Hari/Tanggal : Kamis, 12 November 2026
Waktu        : Pukul 09.00 WIB s.d. selesai
Tempat       : Ruang Guru SMK Negeri 1 Sadaniang
Agenda       : Rapat Persiapan dan Koordinasi Pelaksanaan Kegiatan.`;
        $('#create_keterangan').val(templateText);
    });

    // Inisialisasi DataTable Surat (AJAX POST)
    let table = $('#surat-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
            url: "{{ route('surat.data') }}",
            type: "POST",
            data: function(d) {
                d.jenis_surat = $('#filter_jenis_surat').val();
                d.nomor_surat = $('#filter_nomor_surat').val();
                d.perihal = $('#filter_perihal').val();
                d.tanggal_mulai = $('#filter_tanggal_mulai').val();
                d.tanggal_selesai = $('#filter_tanggal_selesai').val();
                d.status_disposisi = $('#filter_status_disposisi').val();
            },
            error: function(xhr, error, code) {
                console.error("DataTables AJAX Error:", xhr.responseText);
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
            { data: 'jenis_badge', name: 'surat.jenis_surat', className: 'text-center' },
            { data: 'nomor_surat', name: 'surat.nomor_surat' },
            { data: 'tanggal_surat', name: 'surat.tanggal_surat', className: 'text-center' },
            { data: 'tujuan_display', name: 'surat.tujuan_surat' },
            { data: 'perihal', name: 'surat.perihal' },
            { data: 'status_badge', name: 'surat.status_disposisi', className: 'text-center' },
            { data: 'file_badge', name: 'surat.file_arsip', orderable: false, searchable: false, className: 'text-center' },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
        ],
        language: {
            search: "Pencarian Cepat:",
            lengthMenu: "Tampilkan _MENU_ entri",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ surat",
            infoEmpty: "Menampilkan 0 sampai 0 dari 0 surat",
            infoFiltered: "(disaring dari _MAX_ total data)",
            zeroRecords: "Tidak ada data surat yang ditemukan",
            processing: "Memuat data...",
            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: "Selanjutnya",
                previous: "Sebelumnya"
            }
        }
    });

    // Update Header Text & filter wrapper based on jenis
    function updatePageContext(jenis) {
        if (jenis === 'Masuk') {
            $('#pageHeaderTitle').text('Surat Masuk');
            $('#breadcrumbCurrent').text('Surat Masuk');
            $('#tableCardTitle').text('Daftar Surat Masuk');
            $('#filter_disposisi_wrapper').show();
        } else if (jenis === 'Keluar') {
            $('#pageHeaderTitle').text('Surat Keluar');
            $('#breadcrumbCurrent').text('Surat Keluar');
            $('#tableCardTitle').text('Daftar Surat Keluar');
            $('#filter_disposisi_wrapper').hide();
        } else {
            $('#pageHeaderTitle').text('Data Surat');
            $('#breadcrumbCurrent').text('Semua Surat');
            $('#tableCardTitle').text('Daftar Surat Masuk & Keluar');
            $('#filter_disposisi_wrapper').show();
        }
    }

    // Set initial context
    updatePageContext(currentJenis);

    // Tombol Terapkan Filter
    $('#btnApplyFilter').on('click', function() {
        let jenis = $('#filter_jenis_surat').val();
        updatePageContext(jenis);
        table.ajax.reload();
    });

    // Tombol Reset Filter
    $('#btnResetFilter').on('click', function() {
        $('#formFilterSurat')[0].reset();
        updatePageContext('');
        table.ajax.reload();
    });

    // Clear error
    function clearErrors(form) {
        $(form).find('.is-invalid').removeClass('is-invalid');
        $(form).find('.invalid-feedback').text('');
    }

    // Modal Tambah: Reset Form
    $('#modalTambahSurat').on('show.bs.modal', function() {
        $('#formTambahSurat')[0].reset();
        clearErrors('#formTambahSurat');

        $('#create_nomor_urut').val('');
        $('#create_nomor_kode').val('800');
        $('#create_nomor_instansi').val('SMK');
        $('#create_tanggal_surat').val(new Date().toISOString().slice(0, 10));
        $('#create_tanggal_terima').val(new Date().toISOString().slice(0, 10));
        $('#create_nomor_bulan').val(getBulanRomawi($('#create_tanggal_surat').val()));

        let currentTabJenis = $('#filter_jenis_surat').val();
        if ($('#create_jenis_keluar').length && (currentTabJenis === 'Keluar' || !$('#create_jenis_masuk').length)) {
            $('#create_jenis_keluar').prop('checked', true);
            toggleCreateJenisForm('Keluar');
        } else if ($('#create_jenis_masuk').length) {
            $('#create_jenis_masuk').prop('checked', true);
            toggleCreateJenisForm('Masuk');
        }
    });

    // Helper untuk escape HTML
    function escapeHtml(text) {
        if (!text) return '';
        return text
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    // Fungsi Render template surat ke canvas offscreen
    async function generateSuratCanvas(data) {
        $('#render_surat_nomor').text(data.nomor);
        $('#render_surat_tanggal').text(formatTanggalSuratIndo(data.tanggalStr));
        $('#render_surat_penerima').text(data.penerima || 'Bapak/Ibu Dewan Guru');
        if (data.tujuan && data.tujuan.trim() !== '') {
            $('#render_surat_tujuan').text(data.tujuan);
            $('#render_surat_tujuan_wrapper').show();
        } else {
            $('#render_surat_tujuan_wrapper').hide();
        }
        $('#render_surat_perihal').text(data.perihal);

        // Isi Surat / Pengantar
        let isiHtml = '';
        if (data.isiSurat && data.isiSurat.trim() !== '') {
            isiHtml = escapeHtml(data.isiSurat).replace(/\n/g, '<br>');
        } else {
            isiHtml = `Sehubungan dengan akan dilaksanakannya agenda <strong>${escapeHtml(data.perihal)}</strong> pada SMK Negeri 1 Sadaniang, yang pelaksanaannya mengacu pada edaran dinas terkait. Maka kami mengundang Bapak/Ibu Dewan Guru SMK Negeri 1 Sadaniang untuk menghadiri rapat persiapan dan koordinasi pelaksanaan kegiatan tersebut, yang akan dilaksanakan pada:`;
        }
        $('#render_surat_isi').html(isiHtml);

        // Rincian Kegiatan
        let hasRincian = false;
        if (data.hariTanggal && data.hariTanggal.trim() !== '') {
            $('#render_row_hari_tanggal').show();
            $('#render_surat_hari_tanggal').text(data.hariTanggal);
            hasRincian = true;
        } else {
            $('#render_row_hari_tanggal').hide();
        }

        if (data.waktu && data.waktu.trim() !== '') {
            $('#render_row_waktu').show();
            $('#render_surat_waktu').text(data.waktu);
            hasRincian = true;
        } else {
            $('#render_row_waktu').hide();
        }

        if (data.tempat && data.tempat.trim() !== '') {
            $('#render_row_tempat').show();
            $('#render_surat_tempat').text(data.tempat);
            hasRincian = true;
        } else {
            $('#render_row_tempat').hide();
        }

        if (data.agenda && data.agenda.trim() !== '') {
            $('#render_row_agenda').show();
            $('#render_surat_agenda').text(data.agenda);
            hasRincian = true;
        } else {
            $('#render_row_agenda').hide();
        }

        if (hasRincian) {
            $('#render_surat_rincian').show();
        } else {
            $('#render_surat_rincian').hide();
        }

        // Paragraf Penutup Surat
        let penutupHtml = '';
        if (data.penutup && data.penutup.trim() !== '') {
            penutupHtml = escapeHtml(data.penutup).replace(/\n/g, '<br>');
        } else {
            penutupHtml = `Mengingat pentingnya agenda tersebut, kami mengharapkan kehadiran Bapak/Ibu tepat pada waktunya agar seluruh rangkaian kegiatan dapat dikoordinasikan dan berjalan dengan baik.<br><br>Demikian surat ini kami sampaikan. Atas perhatian, kehadiran, dan kerja samanya, kami mengucapkan terima kasih.`;
        }
        $('#render_surat_penutup').html(penutupHtml);

        // Render dengan html2canvas
        const targetElement = document.getElementById('suratRenderElement');
        const canvas = await html2canvas(targetElement, {
            scale: 2,
            useCORS: true,
            backgroundColor: '#ffffff',
            logging: false
        });

        return canvas;
    }

    // Submit Tambah Surat
    $('#formTambahSurat').on('submit', async function(e) {
        e.preventDefault();
        clearErrors(this);
        let btn = $('#btnSubmitTambah');
        let jenis = $('input[name="jenis_surat"]:checked', this).val();

        btn.prop('disabled', true);

        let formData = new FormData(this);

        try {
            // Jika jenis surat adalah KELUAR, generate dokumen arsip otomatis
            if (jenis === 'Keluar') {
                btn.html('<span class="spinner-border spinner-border-sm me-1"></span> Menghasilkan Surat Resmi...');
                let nomor = $('#create_nomor_surat').val();
                let tanggal = $('#create_tanggal_surat').val();
                let penerima = $('#create_penerima_surat').val() || 'Bapak/Ibu Dewan Guru';
                let tujuan = $('#create_tujuan_surat').val();
                let perihal = $('#create_perihal').val();
                let isiSurat = $('#create_isi_surat').val();
                let hariTanggal = $('#create_hari_tanggal').val();
                let waktu = $('#create_waktu').val();
                let tempat = $('#create_tempat').val();
                let agenda = $('#create_agenda').val();
                let penutup = $('#create_penutup').val();

                const canvas = await generateSuratCanvas({
                    nomor: nomor,
                    tanggalStr: tanggal,
                    penerima: penerima,
                    tujuan: tujuan,
                    perihal: perihal,
                    isiSurat: isiSurat,
                    hariTanggal: hariTanggal,
                    waktu: waktu,
                    tempat: tempat,
                    agenda: agenda,
                    penutup: penutup
                });
                const dataUrl = canvas.toDataURL('image/png');

                formData.append('generated_image', dataUrl);

                lastGeneratedData.nomorSurat = nomor;
                lastGeneratedData.canvas = canvas;
                lastGeneratedData.dataUrl = dataUrl;
            } else {
                btn.html('<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...');
            }

            $.ajax({
                url: "{{ route('surat.store') }}",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function(res) {
                    btn.prop('disabled', false).html('<i class="bi bi-save me-1"></i> Simpan Surat');
                    $('#modalTambahSurat').modal('hide');
                    table.ajax.reload(null, false);

                    if (jenis === 'Keluar' && lastGeneratedData.canvas) {
                        lastGeneratedData.cetakUrl = res.cetak_url;
                        $('#hasilSuratPreviewImg').attr('src', lastGeneratedData.dataUrl);
                        $('#btnCetakSuratModal').attr('href', res.cetak_url);
                        $('#modalHasilSuratKeluar').modal('show');
                    } else {
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
                    btn.prop('disabled', false).html('<i class="bi bi-save me-1"></i> Simpan Surat');
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, val) {
                            $('#create_' + key).addClass('is-invalid');
                            $('#error_create_' + key).text(val[0]);
                            if (key === 'nomor_surat') {
                                $('#create_nomor_surat_input, #create_nomor_kode, #create_nomor_urut, #create_nomor_instansi, #create_nomor_bulan').addClass('is-invalid');
                            }
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
        } catch (err) {
            console.error(err);
            btn.prop('disabled', false).html('<i class="bi bi-save me-1"></i> Simpan Surat');
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Terjadi kesalahan saat membuat dokumen surat keluar.'
            });
        }
    });

    // Buka Modal Detail Surat
    $(document).on('click', '.btn-detail', function() {
        let id = $(this).data('id');
        $.ajax({
            url: "{{ url('surat') }}/" + id,
            type: "GET",
            success: function(res) {
                if (res.success) {
                    let s = res.data;
                    let isMasuk = s.jenis_surat === 'Masuk';

                    if (isMasuk) {
                        $('#detail_jenis_surat').html('<span class="badge bg-success"><i class="bi bi-box-arrow-in-down me-1"></i>Surat Masuk</span>');
                        $('#row_detail_no_agenda').show();
                        $('#detail_no_agenda').html(s.no_agenda ? '<span class="badge bg-light text-primary border font-monospace fs-6"><i class="bi bi-bookmark-fill me-1"></i>Agenda #' + s.no_agenda + '</span>' : '<span class="text-muted">-</span>');
                        $('#row_detail_tanggal_terima').show();
                        $('#detail_tanggal_terima').text(s.tanggal_terima || '-');
                        $('#row_detail_asal').show();
                        $('#detail_asal_surat').text(s.asal_surat || '-');
                        $('#row_detail_penerima').hide();
                        $('#th_detail_tujuan').text('Tujuan / Disposisi Kepada');
                        $('#row_detail_status_disposisi').show();

                        let badgeClass = s.status_disposisi === 'Selesai' ? 'bg-success' : (s.status_disposisi === 'Sudah Didisposisikan' ? 'bg-info text-dark' : 'bg-warning text-dark');
                        $('#detail_status_disposisi').html('<span class="badge ' + badgeClass + '">' + (s.status_disposisi || 'Belum Didisposisikan') + '</span>');
                    } else {
                        $('#detail_jenis_surat').html('<span class="badge bg-primary"><i class="bi bi-box-arrow-up-right me-1"></i>Surat Keluar</span>');
                        $('#row_detail_no_agenda').hide();
                        $('#row_detail_tanggal_terima').hide();
                        $('#row_detail_asal').hide();
                        $('#row_detail_penerima').show();
                        $('#detail_penerima_surat').text(s.penerima_surat || '-');
                        $('#th_detail_tujuan').text('Tujuan Surat');
                        $('#row_detail_status_disposisi').hide();
                    }

                    $('#detail_nomor_surat').text(s.nomor_surat);
                    $('#detail_tanggal_surat').text(s.tanggal_surat);
                    $('#detail_tujuan_surat').text(s.tujuan_surat || s.tujuan_disposisi || '-');
                    $('#detail_perihal').text(s.perihal);
                    if (isMasuk) {
                        $('#detail_keterangan').html(s.keterangan ? s.keterangan.replace(/\n/g, '<br>') : '-');
                    } else {
                        let htmlContent = '';
                        if (s.isi_surat) {
                            htmlContent += '<p class="mb-2"><strong>Isi Pengantar:</strong><br>' + s.isi_surat.replace(/\n/g, '<br>') + '</p>';
                        }
                        if (s.hari_tanggal || s.waktu || s.tempat || s.agenda) {
                            htmlContent += '<div class="p-2 bg-light rounded border mb-2 small">' +
                                '<div><strong>Hari/Tanggal:</strong> ' + (s.hari_tanggal || '-') + '</div>' +
                                '<div><strong>Waktu:</strong> ' + (s.waktu || '-') + '</div>' +
                                '<div><strong>Tempat:</strong> ' + (s.tempat || '-') + '</div>' +
                                '<div><strong>Agenda:</strong> ' + (s.agenda || '-') + '</div>' +
                                '</div>';
                        }
                        if (s.penutup) {
                            htmlContent += '<p class="mb-0 text-muted small"><strong>Penutup:</strong><br>' + s.penutup.replace(/\n/g, '<br>') + '</p>';
                        }
                        if (!htmlContent) {
                            htmlContent = s.keterangan ? s.keterangan.replace(/\n/g, '<br>') : '-';
                        }
                        $('#detail_keterangan').html(htmlContent);
                    }
                    $('#detail_creator').text(s.creator_name ? s.creator_name + ' (pada ' + s.created_at + ')' : '-');

                    let footerActions = '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>';
                    if (!isMasuk) {
                        footerActions = '<a href="{{ url("surat") }}/' + s.id + '/cetak" target="_blank" class="btn btn-outline-primary"><i class="bi bi-printer me-1"></i> Cetak / Unduh Dokumen</a>' + footerActions;
                    }
                    $('#modalDetailFooter').html(footerActions);

                    if (res.file_url) {
                        $('#detail_file_arsip').html(
                            '<div class="d-flex align-items-center gap-2 flex-wrap">' +
                            '<span class="badge bg-secondary">' + res.file_ext.toUpperCase() + ' (' + res.file_size + ')</span>' +
                            '<button type="button" class="btn btn-sm btn-outline-primary btn-preview-file" data-url="' + res.file_url + '" data-ext="' + res.file_ext + '" data-nomor="' + s.nomor_surat + '"><i class="bi bi-eye me-1"></i> Pratinjau</button>' +
                            '<a href="' + res.download_url + '" class="btn btn-sm btn-outline-success"><i class="bi bi-download me-1"></i> Unduh Arsip</a>' +
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

    // Buka Modal Edit Surat
    $(document).on('click', '.btn-edit', function() {
        let id = $(this).data('id');
        clearErrors('#formEditSurat');

        $.ajax({
            url: "{{ url('surat') }}/" + id,
            type: "GET",
            success: function(res) {
                if (res.success) {
                    let s = res.data;
                    $('#edit_id').val(s.id);
                    if (s.jenis_surat === 'Keluar') {
                        $('#edit_jenis_keluar').prop('checked', true);
                        let parsed = parseNomorSuratKeluar(s.nomor_surat);
                        $('#edit_nomor_kode').val(parsed.kode);
                        $('#edit_nomor_urut').val(parsed.urut);
                        $('#edit_nomor_instansi').val(parsed.instansi);
                        $('#edit_nomor_bulan').val(parsed.bulan);
                        $('#edit_penerima_surat').val(s.penerima_surat || '');
                        $('#edit_isi_surat').val(s.isi_surat || '');
                        $('#edit_hari_tanggal').val(s.hari_tanggal || '');
                        $('#edit_waktu').val(s.waktu || '');
                        $('#edit_tempat').val(s.tempat || '');
                        $('#edit_agenda').val(s.agenda || '');
                        $('#edit_penutup').val(s.penutup || '');
                        toggleEditJenisForm('Keluar');
                    } else {
                        $('#edit_jenis_masuk').prop('checked', true);
                        $('#edit_no_agenda').val(s.no_agenda || '');
                        $('#edit_nomor_surat_input').val(s.nomor_surat);
                        $('#edit_nomor_surat').val(s.nomor_surat);
                        $('#edit_penerima_surat').val('');
                        $('#edit_keterangan').val(s.keterangan || '');
                        toggleEditJenisForm('Masuk');
                    }

                    $('#edit_nomor_surat').val(s.nomor_surat);
                    $('#edit_tanggal_surat').val(s.tanggal_surat);
                    $('#edit_tanggal_terima').val(s.tanggal_terima);
                    $('#edit_asal_surat').val(s.asal_surat);
                    $('#edit_tujuan_surat').val(s.tujuan_surat || s.tujuan_disposisi);
                    $('#edit_perihal').val(s.perihal);
                    $('#edit_status_disposisi').val(s.status_disposisi || 'Belum Didisposisikan');
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

    // Submit Edit Surat
    $('#formEditSurat').on('submit', async function(e) {
        e.preventDefault();
        clearErrors(this);
        let id = $('#edit_id').val();
        let btn = $('#btnSubmitEdit');
        let jenis = $('input[name="jenis_surat"]:checked', this).val();

        btn.prop('disabled', true);

        let formData = new FormData(this);

        try {
            if (jenis === 'Keluar') {
                btn.html('<span class="spinner-border spinner-border-sm me-1"></span> Menghasilkan Ulang Surat...');
                let nomor = $('#edit_nomor_surat').val();
                let tanggal = $('#edit_tanggal_surat').val();
                let penerima = $('#edit_penerima_surat').val() || 'Bapak/Ibu Dewan Guru';
                let tujuan = $('#edit_tujuan_surat').val();
                let perihal = $('#edit_perihal').val();
                let isiSurat = $('#edit_isi_surat').val();
                let hariTanggal = $('#edit_hari_tanggal').val();
                let waktu = $('#edit_waktu').val();
                let tempat = $('#edit_tempat').val();
                let agenda = $('#edit_agenda').val();
                let penutup = $('#edit_penutup').val();

                const canvas = await generateSuratCanvas({
                    nomor: nomor,
                    tanggalStr: tanggal,
                    penerima: penerima,
                    tujuan: tujuan,
                    perihal: perihal,
                    isiSurat: isiSurat,
                    hariTanggal: hariTanggal,
                    waktu: waktu,
                    tempat: tempat,
                    agenda: agenda,
                    penutup: penutup
                });
                const dataUrl = canvas.toDataURL('image/png');

                formData.append('generated_image', dataUrl);

                lastGeneratedData.nomorSurat = nomor;
                lastGeneratedData.canvas = canvas;
                lastGeneratedData.dataUrl = dataUrl;
            } else {
                btn.html('<span class="spinner-border spinner-border-sm me-1"></span> Memperbarui...');
            }

            $.ajax({
                url: "{{ url('surat') }}/" + id + "/update",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function(res) {
                    btn.prop('disabled', false).html('<i class="bi bi-save me-1"></i> Perbarui Surat');
                    $('#modalEditSurat').modal('hide');
                    table.ajax.reload(null, false);

                    if (jenis === 'Keluar' && lastGeneratedData.canvas) {
                        lastGeneratedData.cetakUrl = res.cetak_url;
                        $('#hasilSuratPreviewImg').attr('src', lastGeneratedData.dataUrl);
                        $('#btnCetakSuratModal').attr('href', res.cetak_url);
                        $('#modalHasilSuratKeluar').modal('show');
                    } else {
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
                    btn.prop('disabled', false).html('<i class="bi bi-save me-1"></i> Perbarui Surat');
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, val) {
                            $('#edit_' + key).addClass('is-invalid');
                            $('#error_edit_' + key).text(val[0]);
                            if (key === 'nomor_surat') {
                                $('#edit_nomor_surat_input, #edit_nomor_kode, #edit_nomor_urut, #edit_nomor_instansi, #edit_nomor_bulan').addClass('is-invalid');
                            }
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
        } catch (err) {
            console.error(err);
            btn.prop('disabled', false).html('<i class="bi bi-save me-1"></i> Perbarui Surat');
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Terjadi kesalahan saat memproses pembaruan surat keluar.'
            });
        }
    });

    // Tombol Unduh Gambar di Modal Hasil Surat Keluar
    $('#btnUnduhGambarModal').on('click', function() {
        if (!lastGeneratedData.dataUrl) return;
        const cleanNomor = (lastGeneratedData.nomorSurat || 'Surat_Keluar').replace(/[^a-zA-Z0-9_-]/g, '_');
        const link = document.createElement('a');
        link.download = 'Surat_Keluar_' + cleanNomor + '.png';
        link.href = lastGeneratedData.dataUrl;
        link.click();
    });

    // Tombol Unduh PDF di Modal Hasil Surat Keluar
    $('#btnUnduhPDFModal').on('click', function() {
        if (!lastGeneratedData.canvas) return;
        const btn = $(this);
        const originalText = btn.html();
        btn.html('<span class="spinner-border spinner-border-sm me-1"></span> Menyusun PDF...').prop('disabled', true);

        try {
            const { jsPDF } = window.jspdf;
            const pdf = new jsPDF('p', 'mm', 'a4');
            const canvas = lastGeneratedData.canvas;
            const imgData = lastGeneratedData.dataUrl;
            const pdfWidth = pdf.internal.pageSize.getWidth();
            const pdfHeight = (canvas.height * pdfWidth) / canvas.width;

            pdf.addImage(imgData, 'PNG', 0, 0, pdfWidth, pdfHeight);
            const cleanNomor = (lastGeneratedData.nomorSurat || 'Surat_Keluar').replace(/[^a-zA-Z0-9_-]/g, '_');
            pdf.save('Surat_Keluar_' + cleanNomor + '.pdf');

            btn.html(originalText).prop('disabled', false);
        } catch (e) {
            console.error(e);
            alert('Gagal mengekspor PDF.');
            btn.html(originalText).prop('disabled', false);
        }
    });

    // Hapus Surat
    $(document).on('click', '.btn-delete', function() {
        let id = $(this).data('id');
        let nomor = $(this).data('nomor');
        let jenis = $(this).data('jenis');

        Swal.fire({
            title: `Hapus Surat ${jenis}?`,
            text: `Data nomor "${nomor}" beserta file arsipnya akan dihapus permanen.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('surat') }}/" + id,
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
        let nomor = $(this).data('nomor') || 'Surat';

        $('#modalPreviewFileLabel').html('<i class="bi bi-file-earmark-text me-2"></i>Pratinjau: ' + nomor);
        $('#previewFileName').text(nomor);
        $('#previewDownloadBtn').attr('href', url.replace('/preview', '/download'));

        let container = $('#previewContainer');
        container.empty();

        if (['jpg', 'jpeg', 'png'].includes(ext)) {
            container.css('display', 'flex').css('justify-content', 'center').css('align-items', 'center').css('padding', '20px').css('overflow-y', 'auto');
            container.html('<img src="' + url + '" alt="Pratinjau Gambar Surat" style="max-width: 100%; max-height: 100%; object-fit: contain; box-shadow: 0 4px 15px rgba(0,0,0,0.3); border-radius: 4px; background: #fff;">');
        } else if (ext === 'pdf') {
            container.css('display', 'block').css('padding', '0');
            container.html('<iframe src="' + url + '" style="width: 100%; height: 100%; border: none;"></iframe>');
        } else {
            container.html('<div class="text-white text-center p-5"><i class="bi bi-file-earmark-x fs-1 d-block mb-3"></i>Format file tidak dapat dipratinjau langsung. Silakan unduh berkas untuk membukanya.</div>');
        }

        $('#modalPreviewFile').modal('show');
    });
});
</script>
@endpush
