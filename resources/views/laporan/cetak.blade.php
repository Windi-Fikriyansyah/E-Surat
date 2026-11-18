<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan Surat - {{ $periodeLabel }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        @page {
            size: A4 landscape;
            margin: 12mm 15mm;
        }
        * {
            box-sizing: border-box;
            color: #000;
        }
        body {
            font-family: "Times New Roman", Times, serif;
            color: #000;
            background: #e9ecef;
            margin: 0;
            padding: 0;
            font-size: 11pt;
            line-height: 1.35;
        }
        .no-print {
            text-align: center;
            padding: 12px 20px;
            background: #ffffff;
            border-bottom: 2px solid #dee2e6;
            position: sticky;
            top: 0;
            z-index: 999;
            box-shadow: 0 2px 6px rgba(0,0,0,0.08);
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }
        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            font-size: 13.5px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
        }
        .btn-print { background: #0d6efd; color: #fff !important; }
        .btn-pdf { background: #dc3545; color: #fff !important; }
        .btn-excel { background: #198754; color: #fff !important; }
        .btn-close-page { background: #6c757d; color: #fff !important; }

        .paper-container {
            padding: 20px 0;
            display: flex;
            justify-content: center;
        }
        .page-laporan {
            width: 297mm;
            min-height: 210mm;
            background: #ffffff;
            padding: 14mm 18mm 16mm 18mm;
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
            box-sizing: border-box;
        }

        /* KOP SURAT */
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4px;
        }
        .kop-table td {
            vertical-align: middle;
        }
        .kop-logo {
            width: 78px;
            height: auto;
            display: block;
        }
        .kop-logo1 {
            width: 95px;
            height: auto;
            display: block;
        }
        .kop-text {
            text-align: center;
            padding: 0 10px;
            vertical-align: middle;
        }
        .kop-text h4 {
            margin: 0;
            font-size: 15px;
            font-weight: normal;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            line-height: 1.1;
        }
        .kop-text h2 {
            margin: 1px 0 2px 0;
            font-size: 20px;
            font-weight: 800;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            line-height: 1.1;
        }
        .kop-text p {
            margin: 0;
            padding: 0;
            font-size: 12px;
            line-height: 1.15;
        }
        .kop-divider {
            border-top: 3.5px solid #000;
            border-bottom: 1.2px solid #000;
            height: 2px;
            margin: 6px 0 16px 0;
        }

        /* JUDUL LAPORAN */
        .judul-laporan {
            text-align: center;
            margin-bottom: 16px;
        }
        .judul-laporan h3 {
            margin: 0 0 4px 0;
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .judul-laporan p {
            margin: 0;
            font-size: 13px;
        }

        /* TABEL DATA */
        .table-data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
            font-size: 10.5pt;
        }
        .table-data th, .table-data td {
            border: 1px solid #000;
            padding: 5px 6px;
            vertical-align: top;
        }
        .table-data th {
            background-color: #f0f0f0;
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10pt;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-mono { font-family: "Courier New", Courier, monospace; }

        /* REKAP KECIL */
        .rekap-box {
            margin-bottom: 20px;
            font-size: 10.5pt;
        }
        .table-rekap {
            border-collapse: collapse;
            font-size: 10pt;
            width: auto;
        }
        .table-rekap td, .table-rekap th {
            border: 1px solid #000;
            padding: 3px 8px;
        }
        .table-rekap th {
            background-color: #f7f7f7;
            text-align: left;
        }

        /* TANDA TANGAN */
        .ttd-container {
            width: 100%;
            margin-top: 20px;
            page-break-inside: avoid;
        }
        .ttd-box {
            float: right;
            width: 280px;
            text-align: left;
            font-size: 11pt;
            line-height: 1.4;
        }
        .nama-pejabat {
            font-weight: bold;
            text-decoration: underline;
            margin-top: 65px;
            display: block;
        }

        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: #fff;
            }
            .paper-container {
                padding: 0;
            }
            .page-laporan {
                width: 100%;
                margin: 0;
                padding: 0;
                box-shadow: none;
                min-height: auto;
            }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button type="button" class="btn-action btn-print" onclick="window.print()">
            <i class="bi bi-printer-fill"></i> Cetak Dokumen
        </button>
        <a href="{{ route('laporan.pdf', request()->all()) }}" class="btn-action btn-pdf">
            <i class="bi bi-file-earmark-pdf-fill"></i> Unduh PDF
        </a>
        <a href="{{ route('laporan.excel', request()->all()) }}" class="btn-action btn-excel">
            <i class="bi bi-file-earmark-excel-fill"></i> Unduh Excel
        </a>
        <button type="button" class="btn-action btn-close-page" onclick="window.close()">
            <i class="bi bi-x-circle"></i> Tutup
        </button>
    </div>

    <div class="paper-container">
        <div class="page-laporan">
            <!-- KOP SURAT RESMI -->
            <table class="kop-table">
                <tr>
                    <td style="width: 80px; text-align: left;">
                        <img src="{{ asset('assets/surat/logo_kalbar.png') }}" class="kop-logo" alt="Logo Pemprov Kalbar">
                    </td>
                    <td class="kop-text">
                        <h4>PEMERINTAH PROVINSI KALIMANTAN BARAT</h4>
                        <h2>SMK NEGERI 1 SADANIANG</h2>
                        <p>Jalan Raya Pudak, Provinsi Kalimantan Barat, Kode Pos 78361</p>
                        <p>Hp. 081348969697, Website: <span style="text-decoration: underline;">http://www.smkn1sadaniang.sch.id</span></p>
                    </td>
                    <td style="width: 100px; height: auto; text-align: right;">
                        <img src="{{ asset('assets/surat/logo_smk.png') }}" class="kop-logo1" alt="Logo SMK">
                    </td>
                </tr>
            </table>
            <div class="kop-divider"></div>

            <!-- JUDUL LAPORAN -->
            <div class="judul-laporan">
                @php
                    $judulJenis = 'SURAT MASUK & SURAT KELUAR';
                    if ($filters['jenis_surat'] === 'Masuk') {
                        $judulJenis = 'SURAT MASUK';
                    } elseif ($filters['jenis_surat'] === 'Keluar') {
                        $judulJenis = 'SURAT KELUAR';
                    }
                @endphp
                <h3>LAPORAN REKAPITULASI {{ $judulJenis }}</h3>
                <p>Periode: <strong>{{ $periodeLabel }}</strong></p>
            </div>

            <!-- TABEL DATA LAPORAN -->
            <table class="table-data">
                <thead>
                    <tr>
                        <th style="width: 30px;">No</th>
                        @if (empty($filters['jenis_surat']))
                            <th style="width: 55px;">Jenis</th>
                        @endif
                        <th style="width: 50px;">No. Agd</th>
                        <th style="width: 150px;">Nomor Surat</th>
                        <th style="width: 75px;">Tgl Surat</th>
                        <th style="width: 160px;">Asal / Penerima</th>
                        <th style="width: 140px;">Tujuan</th>
                        <th>Perihal</th>
                        @if ($filters['jenis_surat'] !== 'Keluar')
                            <th style="width: 90px;">Disposisi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse ($suratList as $idx => $s)
                        <tr>
                            <td class="text-center">{{ $idx + 1 }}</td>
                            @if (empty($filters['jenis_surat']))
                                <td class="text-center fw-bold">{{ $s->jenis_surat }}</td>
                            @endif
                            <td class="text-center font-mono">
                                {{ $s->no_agenda ? '#' . $s->no_agenda : '-' }}
                            </td>
                            <td class="font-mono">
                                <strong>{{ $s->nomor_surat }}</strong>
                            </td>
                            <td class="text-center">
                                {{ \Carbon\Carbon::parse($s->tanggal_surat)->format('d/m/Y') }}
                            </td>
                            <td>
                                @if ($s->jenis_surat === 'Masuk')
                                    {{ $s->asal_surat ?: '-' }}
                                @else
                                    {{ $s->penerima_surat ?: $s->tujuan_surat ?: '-' }}
                                @endif
                            </td>
                            <td>
                                {{ $s->tujuan_surat ?: $s->tujuan_disposisi ?: '-' }}
                            </td>
                            <td>
                                {{ $s->perihal }}
                                @if(!empty($s->agenda))
                                    <div style="font-size: 9.5pt; color: #444; margin-top: 2px;">
                                        <em>Agenda: {{ $s->agenda }}</em>
                                    </div>
                                @endif
                            </td>
                            @if ($filters['jenis_surat'] !== 'Keluar')
                                <td class="text-center" style="font-size: 9.5pt;">
                                    @if ($s->jenis_surat === 'Masuk')
                                        {{ $s->status_disposisi ?: 'Belum' }}
                                    @else
                                        -
                                    @endif
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ empty($filters['jenis_surat']) ? '9' : ($filters['jenis_surat'] === 'Keluar' ? '7' : '8') }}" class="text-center" style="padding: 25px 0;">
                                <em>Tidak ada data surat pada periode ini.</em>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- REKAPITULASI JUMLAH SURAT -->
            <div class="rekap-box">
                <table class="table-rekap">
                    <tr>
                        <th colspan="2" style="text-align: center; background: #e0e0e0;"><strong>REKAPITULASI JUMLAH SURAT</strong></th>
                    </tr>
                    <tr>
                        <th>Total Surat pada Periode Ini</th>
                        <td class="text-center"><strong>{{ $rekap['total'] }}</strong> surat</td>
                    </tr>
                    <tr>
                        <th>- Surat Masuk</th>
                        <td class="text-center">{{ $rekap['masuk'] }} surat</td>
                    </tr>
                    <tr>
                        <th>- Surat Keluar</th>
                        <td class="text-center">{{ $rekap['keluar'] }} surat</td>
                    </tr>
                    @if ($rekap['masuk'] > 0)
                        <tr>
                            <th>&nbsp;&nbsp;&nbsp;&bull; Status Belum Didisposisikan</th>
                            <td class="text-center">{{ $rekap['belum_disposisi'] }}</td>
                        </tr>
                        <tr>
                            <th>&nbsp;&nbsp;&nbsp;&bull; Status Sudah Didisposisikan</th>
                            <td class="text-center">{{ $rekap['sudah_disposisi'] }}</td>
                        </tr>
                        <tr>
                            <th>&nbsp;&nbsp;&nbsp;&bull; Status Selesai</th>
                            <td class="text-center">{{ $rekap['selesai'] }}</td>
                        </tr>
                    @endif
                </table>
            </div>

            <!-- TANDA TANGAN KEPALA SEKOLAH -->
            <div class="ttd-container">
                <div class="ttd-box">
                    @php
                        $bulanIndo = [
                            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                        ];
                        $now = \Carbon\Carbon::now();
                        $tglCetak = 'Sadaniang, ' . $now->day . ' ' . ($bulanIndo[$now->month] ?? '') . ' ' . $now->year;
                    @endphp
                    {{ $tglCetak }}<br>
                    Mengetahui,<br>
                    Kepala Sekolah
                    
                    <span class="nama-pejabat">Fransiska Ika, S.S, M.Pd.</span>
                    <span>NIP. 19850122 200904 2 002</span>
                </div>
                <div style="clear: both;"></div>
            </div>
        </div>
    </div>
</body>
</html>

