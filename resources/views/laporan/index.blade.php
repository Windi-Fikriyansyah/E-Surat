@extends('template.app')
@section('title', 'Laporan Surat')

@section('content')
<div class="page-heading">
    <div class="page-title mb-3">
        <div class="row align-items-center">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3><i class="bi bi-file-earmark-bar-graph-fill me-2 text-primary"></i>Laporan Surat</h3>
                <p class="text-subtitle text-muted">Rekapitulasi, penyaringan periode, pencetakan, dan ekspor data Surat Masuk & Keluar.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Laporan Surat</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center py-3">
            <h5 class="card-title text-white mb-0">
                <i class="bi bi-funnel-fill me-2"></i>Filter & Pengaturan Periode Laporan
            </h5>
            <span class="badge bg-light text-dark fw-bold">{{ $periodeLabel }}</span>
        </div>
        <div class="card-body pt-3">
            <form method="GET" action="{{ route('laporan.index') }}" id="formFilterLaporan">
                <div class="row g-3">
                    <!-- Jenis Surat -->
                    <div class="col-md-3">
                        <label for="filter_jenis_surat" class="form-label fw-bold">Jenis Surat</label>
                        <select class="form-select" id="filter_jenis_surat" name="jenis_surat">
                            <option value="" {{ empty($filters['jenis_surat']) ? 'selected' : '' }}>Semua Jenis (Masuk & Keluar)</option>
                            <option value="Masuk" {{ $filters['jenis_surat'] === 'Masuk' ? 'selected' : '' }}>Surat Masuk Saja</option>
                            <option value="Keluar" {{ $filters['jenis_surat'] === 'Keluar' ? 'selected' : '' }}>Surat Keluar Saja</option>
                        </select>
                    </div>

                    <!-- Tipe Periode -->
                    <div class="col-md-3">
                        <label for="filter_periode_type" class="form-label fw-bold">Tipe Periode</label>
                        <select class="form-select" id="filter_periode_type" name="periode_type">
                            <option value="harian" {{ $filters['periode_type'] === 'harian' ? 'selected' : '' }}>Harian</option>
                            <option value="bulanan" {{ $filters['periode_type'] === 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                            <option value="tahunan" {{ $filters['periode_type'] === 'tahunan' ? 'selected' : '' }}>Tahunan</option>
                            <option value="rentang" {{ $filters['periode_type'] === 'rentang' ? 'selected' : '' }}>Rentang Tanggal</option>
                        </select>
                    </div>

                    <!-- Input Periode Harian -->
                    <div class="col-md-3 periode-field field-harian {{ $filters['periode_type'] === 'harian' ? '' : 'd-none' }}">
                        <label for="filter_tanggal" class="form-label fw-bold">Tanggal</label>
                        <input type="date" class="form-control" id="filter_tanggal" name="tanggal" value="{{ $filters['tanggal'] }}">
                    </div>

                    <!-- Input Periode Bulanan (Bulan) -->
                    <div class="col-md-2 periode-field field-bulanan {{ $filters['periode_type'] === 'bulanan' ? '' : 'd-none' }}">
                        <label for="filter_bulan" class="form-label fw-bold">Bulan</label>
                        <select class="form-select" id="filter_bulan" name="bulan">
                            @php
                                $daftarBulan = [
                                    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                                ];
                            @endphp
                            @foreach ($daftarBulan as $mNum => $mName)
                                <option value="{{ $mNum }}" {{ (int)$filters['bulan'] === $mNum ? 'selected' : '' }}>{{ $mName }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Input Periode Bulanan & Tahunan (Tahun) -->
                    <div class="col-md-2 periode-field field-tahun {{ in_array($filters['periode_type'], ['bulanan', 'tahunan']) ? '' : 'd-none' }}">
                        <label for="filter_tahun" class="form-label fw-bold">Tahun</label>
                        <select class="form-select" id="filter_tahun" name="tahun">
                            @php
                                $curYear = (int)date('Y');
                            @endphp
                            @for ($y = $curYear + 1; $y >= 2020; $y--)
                                <option value="{{ $y }}" {{ (int)$filters['tahun'] === $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>

                    <!-- Input Periode Rentang (Tanggal Mulai) -->
                    <div class="col-md-3 periode-field field-rentang {{ $filters['periode_type'] === 'rentang' ? '' : 'd-none' }}">
                        <label for="filter_tanggal_mulai" class="form-label fw-bold">Dari Tanggal</label>
                        <input type="date" class="form-control" id="filter_tanggal_mulai" name="tanggal_mulai" value="{{ $filters['tanggal_mulai'] }}">
                    </div>

                    <!-- Input Periode Rentang (Tanggal Selesai) -->
                    <div class="col-md-3 periode-field field-rentang {{ $filters['periode_type'] === 'rentang' ? '' : 'd-none' }}">
                        <label for="filter_tanggal_selesai" class="form-label fw-bold">Sampai Tanggal</label>
                        <input type="date" class="form-control" id="filter_tanggal_selesai" name="tanggal_selesai" value="{{ $filters['tanggal_selesai'] }}">
                    </div>

                    <!-- Filter Status Disposisi (Opsional, untuk Surat Masuk) -->
                    <div class="col-md-3 field-status-disposisi {{ $filters['jenis_surat'] === 'Keluar' ? 'd-none' : '' }}">
                        <label for="filter_status_disposisi" class="form-label fw-bold">Status Disposisi (Surat Masuk)</label>
                        <select class="form-select" id="filter_status_disposisi" name="status_disposisi">
                            <option value="">Semua Status</option>
                            <option value="Belum Didisposisikan" {{ $filters['status_disposisi'] === 'Belum Didisposisikan' ? 'selected' : '' }}>Belum Didisposisikan</option>
                            <option value="Sudah Didisposisikan" {{ $filters['status_disposisi'] === 'Sudah Didisposisikan' ? 'selected' : '' }}>Sudah Didisposisikan</option>
                            <option value="Selesai" {{ $filters['status_disposisi'] === 'Selesai' ? 'selected' : '' }}>Selesai</option>
                        </select>
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-2 mt-4 pt-2 border-top justify-content-between align-items-center">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-3">
                            <i class="bi bi-funnel me-1"></i> Terapkan Filter
                        </button>
                        <a href="{{ route('laporan.index') }}" class="btn btn-outline-secondary px-3">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                        </a>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('laporan.cetak', request()->all()) }}" target="_blank" class="btn btn-outline-primary px-3">
                            <i class="bi bi-printer-fill me-1"></i> Cetak Laporan
                        </a>
                        <a href="{{ route('laporan.pdf', request()->all()) }}" class="btn btn-danger px-3">
                            <i class="bi bi-file-earmark-pdf-fill me-1"></i> Ekspor PDF
                        </a>
                        <a href="{{ route('laporan.excel', request()->all()) }}" class="btn btn-success px-3">
                            <i class="bi bi-file-earmark-excel-fill me-1"></i> Ekspor Excel
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Rekapitulasi Jumlah Surat (Cards) -->
    <div class="row mb-4">
        <div class="col-6 col-lg-3 col-md-6 mb-3">
            <div class="card h-100 border-0 shadow-sm border-start border-primary border-4">
                <div class="card-body px-3 py-3">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon bg-primary text-white me-3 d-flex align-items-center justify-content-center rounded" style="width: 48px; height: 48px;">
                            <i class="bi bi-envelope-paper-fill fs-4"></i>
                        </div>
                        <div>
                            <h6 class="text-muted font-semibold mb-1">Total Surat</h6>
                            <h4 class="font-extrabold mb-0 text-primary">{{ $rekap['total'] }}</h4>
                            <small class="text-muted">{{ $periodeLabel }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3 col-md-6 mb-3">
            <div class="card h-100 border-0 shadow-sm border-start border-success border-4">
                <div class="card-body px-3 py-3">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon bg-success text-white me-3 d-flex align-items-center justify-content-center rounded" style="width: 48px; height: 48px;">
                            <i class="bi bi-box-arrow-in-down-left fs-4"></i>
                        </div>
                        <div>
                            <h6 class="text-muted font-semibold mb-1">Surat Masuk</h6>
                            <h4 class="font-extrabold mb-0 text-success">{{ $rekap['masuk'] }}</h4>
                            <small class="text-muted">{{ $rekap['total'] > 0 ? round(($rekap['masuk'] / $rekap['total']) * 100) : 0 }}% dari total</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3 col-md-6 mb-3">
            <div class="card h-100 border-0 shadow-sm border-start border-info border-4">
                <div class="card-body px-3 py-3">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon bg-info text-white me-3 d-flex align-items-center justify-content-center rounded" style="width: 48px; height: 48px;">
                            <i class="bi bi-box-arrow-up-right fs-4"></i>
                        </div>
                        <div>
                            <h6 class="text-muted font-semibold mb-1">Surat Keluar</h6>
                            <h4 class="font-extrabold mb-0 text-info">{{ $rekap['keluar'] }}</h4>
                            <small class="text-muted">{{ $rekap['total'] > 0 ? round(($rekap['keluar'] / $rekap['total']) * 100) : 0 }}% dari total</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3 col-md-6 mb-3">
            <div class="card h-100 border-0 shadow-sm border-start border-warning border-4">
                <div class="card-body px-3 py-3">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon bg-warning text-white me-3 d-flex align-items-center justify-content-center rounded" style="width: 48px; height: 48px;">
                            <i class="bi bi-clock-history fs-4"></i>
                        </div>
                        <div>
                            <h6 class="text-muted font-semibold mb-1">Disposisi Masuk</h6>
                            <div class="d-flex gap-1 flex-wrap mt-1">
                                <span class="badge bg-warning text-dark" title="Belum Didisposisikan">
                                    <i class="bi bi-hourglass-split"></i> {{ $rekap['belum_disposisi'] }}
                                </span>
                                <span class="badge bg-info text-dark" title="Sudah Didisposisikan">
                                    <i class="bi bi-send"></i> {{ $rekap['sudah_disposisi'] }}
                                </span>
                                <span class="badge bg-success" title="Selesai">
                                    <i class="bi bi-check2"></i> {{ $rekap['selesai'] }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Data Laporan Card -->
    <div class="card border-0 shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center py-3 bg-white border-bottom">
            <div>
                <h5 class="card-title mb-0">
                    <i class="bi bi-table me-2 text-primary"></i>Daftar Arsip Surat
                </h5>
                <small class="text-muted">Menampilkan {{ count($suratList) }} surat pada periode <strong>{{ $periodeLabel }}</strong></small>
            </div>
            <div>
                <span class="badge bg-secondary">
                    Total: {{ count($suratList) }} Baris Data
                </span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 50px;">No</th>
                            <th class="text-center" style="width: 100px;">Jenis</th>
                            <th class="text-center" style="width: 90px;">No. Agenda</th>
                            <th style="min-width: 160px;">Nomor Surat</th>
                            <th class="text-center" style="width: 120px;">Tanggal</th>
                            <th style="min-width: 160px;">Asal / Penerima</th>
                            <th style="min-width: 150px;">Tujuan</th>
                            <th style="min-width: 220px;">Perihal</th>
                            <th class="text-center" style="width: 140px;">Status Disposisi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($suratList as $idx => $s)
                            <tr>
                                <td class="text-center fw-bold">{{ $idx + 1 }}</td>
                                <td class="text-center">
                                    @if ($s->jenis_surat === 'Masuk')
                                        <span class="badge bg-success"><i class="bi bi-box-arrow-in-down-left me-1"></i>Masuk</span>
                                    @else
                                        <span class="badge bg-primary"><i class="bi bi-box-arrow-up-right me-1"></i>Keluar</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($s->no_agenda)
                                        <span class="badge bg-light text-primary border font-monospace">#{{ $s->no_agenda }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <strong class="text-primary font-monospace">{{ $s->nomor_surat }}</strong>
                                </td>
                                <td class="text-center font-monospace small">
                                    {{ \Carbon\Carbon::parse($s->tanggal_surat)->format('d/m/Y') }}
                                    @if ($s->tanggal_terima)
                                        <br><span class="text-muted small">Terima: {{ \Carbon\Carbon::parse($s->tanggal_terima)->format('d/m/Y') }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($s->jenis_surat === 'Masuk')
                                        <strong>{{ $s->asal_surat ?: '-' }}</strong>
                                    @else
                                        <strong>{{ $s->penerima_surat ?: $s->tujuan_surat ?: '-' }}</strong>
                                    @endif
                                </td>
                                <td>
                                    {{ $s->tujuan_surat ?: $s->tujuan_disposisi ?: '-' }}
                                </td>
                                <td>
                                    <span class="text-dark">{{ $s->perihal }}</span>
                                    @if (!empty($s->agenda))
                                        <br><small class="text-muted"><i class="bi bi-calendar-event me-1"></i>{{ $s->agenda }}</small>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($s->jenis_surat === 'Masuk')
                                        @if ($s->status_disposisi === 'Selesai')
                                            <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Selesai</span>
                                        @elseif ($s->status_disposisi === 'Sudah Didisposisikan')
                                            <span class="badge bg-info text-dark"><i class="bi bi-send me-1"></i>Didisposisikan</span>
                                        @else
                                            <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i>Belum</span>
                                        @endif
                                    @else
                                        <span class="text-muted"><i class="bi bi-dash"></i></span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                                        <h6>Tidak Ada Data Surat yang Ditemukan</h6>
                                        <p class="small mb-0">Silakan ubah filter periode atau jenis surat di atas untuk menampilkan data.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if (count($suratList) > 0)
            <div class="card-footer bg-light d-flex justify-content-between align-items-center py-2 px-3 small text-muted">
                <span>Dihasilkan oleh Sistem Administrasi Surat SMK Negeri 1 Sadaniang</span>
                <span>Total Surat: <strong>{{ count($suratList) }}</strong></span>
            </div>
        @endif
    </div>
</div>
@endsection

@push('js')
<script>
$(document).ready(function() {
    // Dynamic toggle input periode
    $('#filter_periode_type').on('change', function() {
        let type = $(this).val();
        $('.periode-field').addClass('d-none');
        if (type === 'harian') {
            $('.field-harian').removeClass('d-none');
        } else if (type === 'bulanan') {
            $('.field-bulanan').removeClass('d-none');
            $('.field-tahun').removeClass('d-none');
        } else if (type === 'tahunan') {
            $('.field-tahun').removeClass('d-none');
        } else if (type === 'rentang') {
            $('.field-rentang').removeClass('d-none');
        }
    });

    // Toggle filter disposisi berdasarkan jenis surat
    $('#filter_jenis_surat').on('change', function() {
        if ($(this).val() === 'Keluar') {
            $('.field-status-disposisi').addClass('d-none');
            $('#filter_status_disposisi').val('');
        } else {
            $('.field-status-disposisi').removeClass('d-none');
        }
    });
});
</script>
@endpush

