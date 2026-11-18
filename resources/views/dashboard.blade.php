@extends('template.app')
@section('title', 'Dashboard')

@section('content')
<div class="page-heading">
    <h3>Dashboard E-Surat</h3>
</div>
<div class="page-content">
    <section class="row">
        <!-- Kolom Kiri: Statistik Utama, Grafik & Surat Masuk Terbaru -->
        <div class="col-12 col-lg-9">
            <!-- Baris 4 Card Statistik Bawaan Template Mazer -->
            <div class="row">
                <!-- 1. Total Surat Masuk -->
                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                    <div class="stats-icon green mb-2">
                                        <i class="iconly-boldPaper-Download"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Surat Masuk</h6>
                                    <h6 class="font-extrabold mb-0">{{ number_format($totalMasuk) }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Total Surat Keluar -->
                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                    <div class="stats-icon blue mb-2">
                                        <i class="iconly-boldPaper-Upload"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Surat Keluar</h6>
                                    <h6 class="font-extrabold mb-0">{{ number_format($totalKeluar) }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Surat Bulan Ini -->
                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                    <div class="stats-icon purple mb-2">
                                        <i class="iconly-boldCalendar"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Bulan Ini</h6>
                                    <h6 class="font-extrabold mb-0">{{ number_format($totalBulanIni) }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4. Surat Belum Didisposisi (Khusus Kepala Sekolah / Pimpinan) -->
                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                    <div class="stats-icon red mb-2">
                                        <i class="iconly-boldBookmark"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Perlu Disposisi</h6>
                                    <h6 class="font-extrabold mb-0">{{ number_format($belumDisposisiCount) }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Baris Grafik Surat Per Bulan -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Grafik Jumlah Surat Per Bulan ({{ $currentYear }})</h4>
                        </div>
                        <div class="card-body">
                            <div id="chart-profile-visit"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Baris Daftar Surat Masuk Terbaru -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4>Daftar Surat Masuk Terbaru</h4>
                            <a href="{{ route('surat.index', ['jenis' => 'Masuk']) }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-lg">
                                    <thead>
                                        <tr>
                                            <th>No. Agenda</th>
                                            <th>Nomor Surat</th>
                                            <th>Tanggal</th>
                                            <th>Asal Surat</th>
                                            <th>Perihal</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($suratMasukTerbaru as $surat)
                                        <tr>
                                            <td class="col-2">
                                                <span class="badge bg-light-primary text-primary font-monospace">#{{ $surat->no_agenda ?? '-' }}</span>
                                            </td>
                                            <td class="col-3">
                                                <p class="font-bold mb-0 text-truncate" style="max-width: 200px;">{{ $surat->nomor_surat }}</p>
                                            </td>
                                            <td class="col-2">
                                                <p class="text-muted mb-0">{{ date('d/m/Y', strtotime($surat->tanggal_surat)) }}</p>
                                            </td>
                                            <td class="col-2">
                                                <p class="mb-0 text-truncate" style="max-width: 150px;">{{ $surat->asal_surat ?: '-' }}</p>
                                            </td>
                                            <td class="col-auto">
                                                <p class="mb-0 text-truncate" style="max-width: 220px;" title="{{ $surat->perihal }}">{{ $surat->perihal }}</p>
                                            </td>
                                            <td class="col-2">
                                                @php
                                                    $badgeClass = match ($surat->status_disposisi) {
                                                        'Selesai' => 'bg-success',
                                                        'Sudah Didisposisikan' => 'bg-info text-dark',
                                                        default => 'bg-warning text-dark',
                                                    };
                                                    $status = $surat->status_disposisi ?: 'Belum Didisposisikan';
                                                @endphp
                                                <span class="badge {{ $badgeClass }}">{{ $status }}</span>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted">Belum ada surat masuk tersimpan.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Surat Menunggu Disposisi & Donut Status -->
        <div class="col-12 col-lg-3">
            <!-- Card Surat Masuk Belum Didisposisikan (Khusus Kepala Sekolah / Pimpinan) -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Perlu Disposisi</h4>
                    <span class="badge bg-danger">{{ $belumDisposisiCount }}</span>
                </div>
                <div class="card-content pb-4">
                    @forelse($suratBelumDisposisi as $item)
                    <div class="recent-message d-flex px-4 py-3 border-bottom">
                        <div class="avatar avatar-lg bg-light-danger text-danger d-flex align-items-center justify-content-center">
                            <i class="bi bi-envelope-exclamation-fill fs-5"></i>
                        </div>
                        <div class="name ms-3 flex-grow-1 overflow-hidden">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge bg-light-secondary text-dark border font-monospace" style="font-size: 11px;">#{{ $item->no_agenda ?? '-' }}</span>
                                <small class="text-muted" style="font-size: 11px;">{{ date('d/m/Y', strtotime($item->tanggal_surat)) }}</small>
                            </div>
                            <h6 class="mb-1 text-truncate font-bold mt-1 text-primary" style="font-size: 13px;" title="{{ $item->nomor_surat }}">{{ $item->nomor_surat }}</h6>
                            <p class="text-muted mb-0 text-truncate small" title="{{ $item->perihal }}">{{ $item->perihal }}</p>
                        </div>
                    </div>
                    @empty
                    <div class="px-4 py-3 text-center text-muted">
                        <i class="bi bi-check2-circle fs-2 text-success d-block mb-1"></i>
                        <small>Semua surat sudah didisposisikan.</small>
                    </div>
                    @endforelse
                    <div class="px-4">
                        <a href="{{ route('surat.index', ['jenis' => 'Masuk']) }}" class='btn btn-block btn-xl btn-outline-danger font-bold mt-3 w-100'>Buka Surat Masuk</a>
                    </div>
                </div>
            </div>

            <!-- Card Status Disposisi (Donut Chart seperti Visitors Profile) -->
            <div class="card">
                <div class="card-header">
                    <h4>Status Disposisi</h4>
                </div>
                <div class="card-body">
                    <div id="chart-visitors-profile"></div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('js')
<!-- Need: Apexcharts -->
<script src="{{ asset('template/assets/extensions/apexcharts/apexcharts.min.js') }}"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const categories = @json($chartCategories);
    const dataMasuk = @json($chartMasuk);
    const dataKeluar = @json($chartKeluar);

    // 1. Chart Bar Jumlah Surat Per Bulan (Template Mazer)
    var optionsProfileVisit = {
        annotations: {
            position: "back",
        },
        dataLabels: {
            enabled: false,
        },
        chart: {
            type: "bar",
            height: 300,
            toolbar: {
                show: false
            }
        },
        fill: {
            opacity: 1,
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '55%',
                borderRadius: 4
            }
        },
        series: [
            {
                name: "Surat Masuk",
                data: dataMasuk,
            },
            {
                name: "Surat Keluar",
                data: dataKeluar,
            }
        ],
        colors: ["#5ddab4", "#435ebe"],
        xaxis: {
            categories: categories,
        },
        yaxis: {
            title: {
                text: "Jumlah Surat"
            },
            labels: {
                formatter: function (val) {
                    return Math.floor(val);
                }
            }
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return val + " Surat";
                }
            }
        }
    };
    var chartProfileVisit = new ApexCharts(
        document.querySelector("#chart-profile-visit"),
        optionsProfileVisit
    );
    chartProfileVisit.render();

    // 2. Chart Donut Status Disposisi (Template Mazer)
    let belumDisposisi = {{ $belumDisposisiCount }};
    let sudahDisposisi = {{ $sudahDisposisiCount }};
    let selesaiDisposisi = {{ $selesaiDisposisiCount }};
    let totalStatus = belumDisposisi + sudahDisposisi + selesaiDisposisi;

    let seriesDonut = totalStatus > 0 ? [belumDisposisi, sudahDisposisi, selesaiDisposisi] : [0, 0, 0];

    let optionsVisitorsProfile = {
        series: seriesDonut,
        labels: ["Belum Didisposisi", "Sudah Didisposisi", "Selesai"],
        colors: ["#ff7976", "#57caeb", "#5ddab4"],
        chart: {
            type: "donut",
            width: "100%",
            height: "350px",
        },
        legend: {
            position: "bottom",
        },
        plotOptions: {
            pie: {
                donut: {
                    size: "30%",
                },
            },
        },
        noData: {
            text: "Belum ada data disposisi",
            align: 'center',
            verticalAlign: 'middle',
            style: {
                color: '#6c757d',
                fontSize: '14px'
            }
        }
    };
    var chartVisitorsProfile = new ApexCharts(
        document.getElementById("chart-visitors-profile"),
        optionsVisitorsProfile
    );
    chartVisitorsProfile.render();
});
</script>
@endpush
