<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Surat - {{ $periodeLabel }}</title>
    <style>
        @page {
            size: a4 landscape;
            margin: 12mm 15mm;
        }
        * {
            box-sizing: border-box;
            color: #000000;
        }
        body {
            font-family: "Times New Roman", Times, serif;
            color: #000000;
            background: #ffffff;
            margin: 0;
            padding: 0;
            font-size: 10pt;
            line-height: 1.3;
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
            width: 70px;
            height: auto;
        }
        .kop-logo1 {
            width: 85px;
            height: auto;
        }
        .kop-table td.kop-text,
        .kop-text {
            text-align: center;
            padding: 0 10px 0px 10px;
            vertical-align: bottom !important;
        }
        .kop-text h4 {
            margin: 0;
            font-size: 13.5pt;
            font-weight: normal;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            line-height: 1.1;
        }
        .kop-text h2 {
            margin: 1px 0 2px 0;
            font-size: 17pt;
            font-weight: bold;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            line-height: 1.1;
        }
        .kop-text p {
            margin: 0;
            padding: 0;
            font-size: 9.5pt;
            line-height: 1.15;
        }
        .kop-divider {
            border-top: 3px solid #000;
            border-bottom: 1px solid #000;
            height: 2px;
            margin: 2px 0 14px 0;
        }

        /* JUDUL LAPORAN */
        .judul-laporan {
            text-align: center;
            margin-bottom: 14px;
        }
        .judul-laporan h3 {
            margin: 0 0 3px 0;
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .judul-laporan p {
            margin: 0;
            font-size: 10pt;
        }

        /* TABEL DATA */
        .table-data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
            font-size: 9pt;
        }
        .table-data th, .table-data td {
            border: 1px solid #000000;
            padding: 4px 5px;
            vertical-align: top;
        }
        .table-data th {
            background-color: #eeeeee;
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8.5pt;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-mono { font-family: "Courier New", Courier, monospace; }

        /* REKAPITULASI */
        .rekap-container {
            width: 100%;
            margin-bottom: 15px;
        }
        .table-rekap {
            border-collapse: collapse;
            font-size: 8.5pt;
            width: 320px;
        }
        .table-rekap td, .table-rekap th {
            border: 1px solid #000000;
            padding: 2px 6px;
        }
        .table-rekap th {
            background-color: #f2f2f2;
            text-align: left;
        }

        /* TANDA TANGAN */
        .ttd-wrapper {
            width: 100%;
            margin-top: 10px;
        }
        .ttd-table {
            width: 100%;
            border-collapse: collapse;
        }
        .ttd-table td {
            vertical-align: top;
        }
        .nama-pejabat {
            font-weight: bold;
            text-decoration: underline;
            margin-top: 55px;
            display: block;
        }
    </style>
</head>
<body>
    @php
        $logoKalbar = '';
        $pathKalbar = public_path('assets/surat/logo_kalbar.png');
        if (file_exists($pathKalbar)) {
            $logoKalbar = 'data:image/png;base64,' . base64_encode(file_get_contents($pathKalbar));
        }

        $logoSmk = '';
        $pathSmk = public_path('assets/surat/logo_smk.png');
        if (file_exists($pathSmk)) {
            $logoSmk = 'data:image/png;base64,' . base64_encode(file_get_contents($pathSmk));
        }
    @endphp

    <!-- KOP SURAT RESMI -->
    <table class="kop-table">
        <tr>
            <td style="width: 80px; text-align: left;">
                @if($logoKalbar)
                    <img src="{{ $logoKalbar }}" class="kop-logo" alt="Logo Pemprov Kalbar">
                @endif
            </td>
            <td class="kop-text">
                <h4>PEMERINTAH PROVINSI KALIMANTAN BARAT</h4>
                <h2>SMK NEGERI 1 SADANIANG</h2>
                <p>Jalan Raya Pudak, Provinsi Kalimantan Barat, Kode Pos 78361</p>
                <p>Hp. 081348969697, Website: <span style="text-decoration: underline;">http://www.smkn1sadaniang.sch.id</span></p>
            </td>
            <td style="width: 90px; text-align: right;">
                @if($logoSmk)
                    <img src="{{ $logoSmk }}" class="kop-logo1" alt="Logo SMK">
                @endif
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
                <th style="width: 25px;">No</th>
                @if (empty($filters['jenis_surat']))
                    <th style="width: 50px;">Jenis</th>
                @endif
                <th style="width: 45px;">No. Agd</th>
                <th style="width: 140px;">Nomor Surat</th>
                <th style="width: 65px;">Tgl Surat</th>
                <th style="width: 150px;">Asal / Penerima</th>
                <th style="width: 130px;">Tujuan</th>
                <th>Perihal</th>
                @if ($filters['jenis_surat'] !== 'Keluar')
                    <th style="width: 80px;">Disposisi</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse ($suratList as $idx => $s)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    @if (empty($filters['jenis_surat']))
                        <td class="text-center">{{ $s->jenis_surat }}</td>
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
                            <div style="font-size: 8pt; color: #555;">
                                <em>Agenda: {{ $s->agenda }}</em>
                            </div>
                        @endif
                    </td>
                    @if ($filters['jenis_surat'] !== 'Keluar')
                        <td class="text-center" style="font-size: 8pt;">
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
                    <td colspan="{{ empty($filters['jenis_surat']) ? '9' : ($filters['jenis_surat'] === 'Keluar' ? '7' : '8') }}" class="text-center" style="padding: 20px 0;">
                        <em>Tidak ada data surat pada periode ini.</em>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- REKAPITULASI & TANDA TANGAN -->
    <div class="ttd-wrapper">
        <table class="ttd-table">
            <tr>
                <td style="width: 55%; vertical-align: top;">
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
                                <th>&nbsp;&nbsp;&bull; Status Belum Didisposisikan</th>
                                <td class="text-center">{{ $rekap['belum_disposisi'] }}</td>
                            </tr>
                            <tr>
                                <th>&nbsp;&nbsp;&bull; Status Sudah Didisposisikan</th>
                                <td class="text-center">{{ $rekap['sudah_disposisi'] }}</td>
                            </tr>
                            <tr>
                                <th>&nbsp;&nbsp;&bull; Status Selesai</th>
                                <td class="text-center">{{ $rekap['selesai'] }}</td>
                            </tr>
                        @endif
                    </table>
                </td>
                <td style="width: 45%; vertical-align: top; text-align: left; padding-left: 50px;">
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
                </td>
            </tr>
        </table>
    </div>
</body>
</html>

