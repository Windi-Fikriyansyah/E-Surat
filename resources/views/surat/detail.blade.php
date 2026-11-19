@extends('template.app')
@section('title', 'Detail Surat: ' . $surat->nomor_surat)

@section('content')
<div class="page-heading">
    <div class="page-title mb-3">
        <div class="row align-items-center">
            <div class="col-12 col-md-7 order-md-1 order-last">
                <h3>
                    @if ($surat->jenis_surat === 'Masuk')
                        <i class="bi bi-box-arrow-in-down text-success me-2"></i>Detail Surat Masuk
                    @else
                        <i class="bi bi-box-arrow-up-right text-primary me-2"></i>Detail Surat Keluar
                    @endif
                </h3>
                <p class="text-subtitle text-muted mb-0">Informasi lengkap, disposisi, dan dokumen arsip surat.</p>
            </div>
            <div class="col-12 col-md-5 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('surat.index', ['jenis' => $surat->jenis_surat]) }}">Data Surat</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Detail</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <!-- Alert Notifikasi Session -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-3" role="alert">
            <i class="bi bi-check-circle-fill fs-4 me-2"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center mb-3" role="alert">
            <i class="bi bi-exclamation-triangle-fill fs-4 me-2"></i>
            <div>{{ session('error') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Banner Khusus Penerima Disposisi -->
    @if ($isRecipient)
        <div class="alert alert-light-primary border-primary d-flex align-items-center mb-4 p-3 shadow-sm">
            <div class="avatar avatar-lg bg-primary text-white me-3 d-flex align-items-center justify-content-center">
                <i class="bi bi-person-check-fill fs-3"></i>
            </div>
            <div class="flex-grow-1">
                <h6 class="text-primary fw-bold mb-1">Pemberitahuan Disposisi: Surat Ini Ditujukan Kepada Anda</h6>
                <p class="mb-0 text-muted small">
                    Anda tercatat sebagai pihak yang ditugaskan / penerima disposisi surat ini. Silakan periksa isi surat, lampiran berkas, dan perbarui status disposisi apabila telah ditindaklanjuti.
                </p>
            </div>
        </div>
    @endif

    <div class="row">
        <!-- Kolom Kiri: Detail Informasi & Disposisi -->
        <div class="col-lg-6 col-12 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header {{ $surat->jenis_surat === 'Masuk' ? 'bg-success' : 'bg-primary' }} text-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="card-title text-white mb-0">
                        <i class="bi bi-file-text-fill me-2"></i>Informasi Utama Surat
                    </h5>
                    <div class="d-flex align-items-center gap-2">
                        @if ($surat->jenis_surat === 'Masuk')
                            <span class="badge bg-white text-success fw-bold">
                                <i class="bi bi-box-arrow-in-down me-1"></i>Surat Masuk
                            </span>
                            @if ($surat->no_agenda)
                                <span class="badge bg-warning text-dark font-monospace fw-bold">
                                    Agenda #{{ $surat->no_agenda }}
                                </span>
                            @endif
                        @else
                            <span class="badge bg-white text-primary fw-bold">
                                <i class="bi bi-box-arrow-up-right me-1"></i>Surat Keluar
                            </span>
                        @endif
                    </div>
                </div>

                <div class="card-body pt-3">
                    <table class="table table-hover align-middle mb-0">
                        <tbody>
                            <tr>
                                <th style="width: 35%;" class="text-muted small fw-bold">Nomor Surat</th>
                                <td>
                                    <span class="font-monospace fw-bold fs-6 text-primary">{{ $surat->nomor_surat }}</span>
                                </td>
                            </tr>
                            <tr>
                                <th class="text-muted small fw-bold">Tanggal Surat</th>
                                <td class="fw-semibold">
                                    <i class="bi bi-calendar3 me-1 text-muted"></i>
                                    {{ \Carbon\Carbon::parse($surat->tanggal_surat)->translatedFormat('d F Y') }}
                                </td>
                            </tr>

                            @if ($surat->jenis_surat === 'Masuk')
                                <tr>
                                    <th class="text-muted small fw-bold">Tanggal Terima</th>
                                    <td>
                                        <i class="bi bi-calendar-check me-1 text-muted"></i>
                                        {{ $surat->tanggal_terima ? \Carbon\Carbon::parse($surat->tanggal_terima)->translatedFormat('d F Y') : '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th class="text-muted small fw-bold">Asal / Pengirim</th>
                                    <td class="fw-bold text-dark">
                                        <i class="bi bi-building me-1 text-muted"></i>
                                        {{ $surat->asal_surat ?: '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th class="text-muted small fw-bold">Tujuan / Disposisi Kepada</th>
                                    <td>
                                        @if (!empty($surat->recipient_name))
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm bg-primary text-white me-2 fw-bold d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; font-size: 11px;">
                                                    {{ strtoupper(substr($surat->recipient_name, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <strong class="text-dark">{{ $surat->recipient_name }}</strong>
                                                    @if (!empty($surat->recipient_role))
                                                        <span class="badge bg-light text-primary border ms-1 small">{{ $surat->recipient_role }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @else
                                            <span class="fw-bold text-dark">{{ $surat->tujuan_surat ?: ($surat->tujuan_disposisi ?: '-') }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @else
                                <tr>
                                    <th class="text-muted small fw-bold">Penerima Surat</th>
                                    <td class="fw-bold text-dark">
                                        {{ $surat->penerima_surat ?: '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th class="text-muted small fw-bold">Tujuan Surat</th>
                                    <td>
                                        {{ $surat->tujuan_surat ?: '-' }}
                                    </td>
                                </tr>
                            @endif

                            <tr>
                                <th class="text-muted small fw-bold">Perihal Surat</th>
                                <td>
                                    <div class="p-2 bg-light rounded border text-dark fw-bold">
                                        {{ $surat->perihal }}
                                    </div>
                                </td>
                            </tr>

                            @if ($surat->jenis_surat === 'Masuk')
                                <tr>
                                    <th class="text-muted small fw-bold">Status Disposisi</th>
                                    <td>
                                        @php
                                            $badgeClass = match ($surat->status_disposisi) {
                                                'Selesai' => 'bg-success',
                                                'Sudah Didisposisikan' => 'bg-info text-dark',
                                                default => 'bg-warning text-dark',
                                            };
                                        @endphp
                                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                            <span class="badge {{ $badgeClass }} fs-6 px-3 py-2">
                                                {{ $surat->status_disposisi ?: 'Belum Didisposisikan' }}
                                            </span>

                                            @if ($canUpdateStatus)
                                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#formUpdateStatusWrapper" aria-expanded="false">
                                                    <i class="bi bi-pencil me-1"></i> Ubah Status
                                                </button>
                                            @endif
                                        </div>

                                        <!-- Form Ubah Status Cepat (Collapsible) -->
                                        @if ($canUpdateStatus)
                                            <div class="collapse mt-3" id="formUpdateStatusWrapper">
                                                <div class="p-3 bg-light rounded border">
                                                    <form action="{{ route('surat.update-status', $surat->id) }}" method="POST">
                                                        @csrf
                                                        <label class="form-label small fw-bold mb-1">Perbarui Status Disposisi:</label>
                                                        <div class="input-group">
                                                            <select class="form-select form-select-sm" name="status_disposisi" required>
                                                                <option value="Belum Didisposisikan" {{ $surat->status_disposisi === 'Belum Didisposisikan' ? 'selected' : '' }}>Belum Didisposisikan</option>
                                                                <option value="Sudah Didisposisikan" {{ $surat->status_disposisi === 'Sudah Didisposisikan' ? 'selected' : '' }}>Sudah Didisposisikan</option>
                                                                <option value="Selesai" {{ $surat->status_disposisi === 'Selesai' ? 'selected' : '' }}>Selesai</option>
                                                            </select>
                                                            <button type="submit" class="btn btn-sm btn-primary">
                                                                <i class="bi bi-check-lg me-1"></i> Simpan
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th class="text-muted small fw-bold">Catatan / Keterangan</th>
                                    <td>
                                        <div class="text-muted small" style="white-space: pre-line;">{{ $surat->keterangan ?: 'Tidak ada keterangan tambahan.' }}</div>
                                    </td>
                                </tr>
                            @endif

                            <tr>
                                <th class="text-muted small fw-bold">Diinput Oleh</th>
                                <td class="small text-muted">
                                    <i class="bi bi-person me-1"></i>{{ !empty($surat->creator_name) ? $surat->creator_name : 'Sistem' }}
                                    <span class="d-block text-secondary font-monospace" style="font-size: 11px;">
                                        pada {{ \Carbon\Carbon::parse($surat->created_at)->translatedFormat('d F Y, H:i') }} WIB
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    @if ($surat->jenis_surat === 'Keluar')
                        <!-- Rincian Khusus Surat Keluar -->
                        <div class="mt-4">
                            <h6 class="fw-bold text-primary mb-2">
                                <i class="bi bi-calendar-event me-2"></i>Rincian Kegiatan
                            </h6>
                            <div class="p-3 bg-light rounded border small">
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <strong>Hari/Tanggal:</strong><br>
                                        {{ $surat->hari_tanggal ?: '-' }}
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Waktu:</strong><br>
                                        {{ $surat->waktu ?: '-' }}
                                    </div>
                                    <div class="col-md-6 mt-2">
                                        <strong>Tempat:</strong><br>
                                        {{ $surat->tempat ?: '-' }}
                                    </div>
                                    <div class="col-md-6 mt-2">
                                        <strong>Agenda:</strong><br>
                                        {{ $surat->agenda ?: '-' }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3">
                            <h6 class="fw-bold text-primary mb-2">
                                <i class="bi bi-chat-left-quote me-2"></i>Isi Pengantar
                            </h6>
                            <div class="p-3 bg-light rounded border small" style="white-space: pre-line;">
                                {{ $surat->isi_surat ?: '-' }}
                            </div>
                        </div>

                        @if ($surat->penutup)
                            <div class="mt-3">
                                <h6 class="fw-bold text-primary mb-2">
                                    <i class="bi bi-chat-right-text me-2"></i>Penutup
                                </h6>
                                <div class="p-3 bg-light rounded border small text-muted" style="white-space: pre-line;">
                                    {{ $surat->penutup }}
                                </div>
                            </div>
                        @endif
                    @endif
                </div>

                <div class="card-footer bg-light border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <a href="{{ route('surat.index', ['jenis' => $surat->jenis_surat]) }}" class="btn btn-secondary btn-sm px-3">
                        <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Surat
                    </a>

                    <div class="d-flex gap-2">
                        @if ($surat->jenis_surat === 'Keluar' && auth()->user()?->can('surat-keluar.cetak'))
                            <a href="{{ route('surat.cetak', $surat->id) }}" target="_blank" class="btn btn-primary btn-sm px-3">
                                <i class="bi bi-printer-fill me-1"></i> Cetak Dokumen Resmi
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Pratinjau Dokumen / Berkas Arsip -->
        <div class="col-lg-6 col-12 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="card-title text-white mb-0">
                        <i class="bi bi-file-earmark-text-fill me-2"></i>Dokumen / Berkas Arsip
                    </h5>
                    @if ($fileUrl)
                        <div class="d-flex align-items-center gap-1">
                            <span class="badge bg-secondary font-monospace me-1">
                                {{ strtoupper($fileExt) }} ({{ $fileSize }})
                            </span>
                            <a href="{{ route('surat.download', $surat->id) }}" class="btn btn-sm btn-outline-light" title="Unduh Berkas">
                                <i class="bi bi-download me-1"></i> Unduh
                            </a>
                            <a href="{{ $fileUrl }}" target="_blank" class="btn btn-sm btn-outline-light" title="Buka di Tab Baru">
                                <i class="bi bi-box-arrow-up-right"></i>
                            </a>
                        </div>
                    @endif
                </div>

                <div class="card-body p-0 d-flex flex-column" style="min-height: 520px; background: #525659;">
                    @if ($fileUrl)
                        @if ($fileExt === 'pdf')
                            <iframe src="{{ $fileUrl }}" style="width: 100%; height: 600px; border: none; border-radius: 0 0 6px 6px;" title="Pratinjau PDF"></iframe>
                        @elseif (in_array($fileExt, ['jpg', 'jpeg', 'png']))
                            <div class="p-3 d-flex align-items-center justify-content-center flex-grow-1" style="overflow-y: auto;">
                                <img src="{{ $fileUrl }}" alt="Pratinjau Arsip" class="img-fluid rounded shadow" style="max-height: 560px; object-fit: contain; background: #ffffff;">
                            </div>
                        @else
                            <div class="text-white text-center p-5 m-auto">
                                <i class="bi bi-file-earmark-lock fs-1 text-secondary opacity-75 d-block mb-3"></i>
                                <h6 class="text-white">Format Berkas ({{ strtoupper($fileExt) }})</h6>
                                <p class="small text-muted mb-3">Format file ini tidak mendukung pratinjau langsung di dalam browser.</p>
                                <a href="{{ route('surat.download', $surat->id) }}" class="btn btn-primary btn-sm">
                                    <i class="bi bi-download me-1"></i> Unduh Berkas untuk Membukanya
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="text-white text-center p-5 m-auto">
                            <i class="bi bi-file-earmark-x fs-1 text-secondary opacity-50 d-block mb-3"></i>
                            <h6 class="text-white">Tidak Ada File Arsip</h6>
                            <p class="small text-muted mb-0">Surat ini disimpan tanpa lampiran file arsip dokumen.</p>
                        </div>
                    @endif
                </div>

                @if ($fileUrl)
                    <div class="card-footer bg-white border-top py-2 d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            <i class="bi bi-info-circle me-1"></i>Gunakan kontrol zoom & cetak pada viewer untuk memeriksa dokumen.
                        </small>
                        <a href="{{ route('surat.download', $surat->id) }}" class="btn btn-sm btn-success">
                            <i class="bi bi-download me-1"></i> Unduh Berkas Asli
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
