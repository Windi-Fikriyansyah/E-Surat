<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        .num-text { mso-number-format: "\@"; }
        .text-center { text-align: center; }
        .text-bold { font-weight: bold; }
        .bg-header { background-color: #203764; color: #ffffff; font-weight: bold; }
        .bg-sub { background-color: #d9e1f2; font-weight: bold; }
        .border-all { border: 0.5pt solid #000000; }
    </style>
</head>
<body>
    @php
        $judulJenis = 'SURAT MASUK & SURAT KELUAR';
        if ($filters['jenis_surat'] === 'Masuk') {
            $judulJenis = 'SURAT MASUK';
        } elseif ($filters['jenis_surat'] === 'Keluar') {
            $judulJenis = 'SURAT KELUAR';
        }
        $colSpan = empty($filters['jenis_surat']) ? 9 : ($filters['jenis_surat'] === 'Keluar' ? 7 : 8);
    @endphp

    <table border="0" cellpadding="0" cellspacing="0">
        <!-- KOP EXCEL -->
        <tr>
            <td colspan="{{ $colSpan }}" align="center" style="font-size: 14pt; font-weight: bold;">
                PEMERINTAH PROVINSI KALIMANTAN BARAT
            </td>
        </tr>
        <tr>
            <td colspan="{{ $colSpan }}" align="center" style="font-size: 16pt; font-weight: bold;">
                SMK NEGERI 1 SADANIANG
            </td>
        </tr>
        <tr>
            <td colspan="{{ $colSpan }}" align="center" style="font-size: 10pt;">
                Jalan Raya Pudak, Provinsi Kalimantan Barat, Kode Pos 78361 | Hp. 081348969697 | Website: http://www.smkn1sadaniang.sch.id
            </td>
        </tr>
        <tr>
            <td colspan="{{ $colSpan }}" align="center" style="border-bottom: 2pt solid #000000;"></td>
        </tr>
        <tr><td></td></tr>

        <!-- JUDUL LAPORAN -->
        <tr>
            <td colspan="{{ $colSpan }}" align="center" style="font-size: 13pt; font-weight: bold;">
                LAPORAN REKAPITULASI {{ $judulJenis }}
            </td>
        </tr>
        <tr>
            <td colspan="{{ $colSpan }}" align="center" style="font-size: 11pt;">
                Periode: {{ $periodeLabel }}
            </td>
        </tr>
        <tr><td></td></tr>

        <!-- TABEL HEADER -->
        <tr style="height: 25pt;">
            <th style="border: 0.5pt solid #000000; background-color: #203764; color: #ffffff;" align="center">No</th>
            @if (empty($filters['jenis_surat']))
                <th style="border: 0.5pt solid #000000; background-color: #203764; color: #ffffff;" align="center">Jenis</th>
            @endif
            <th style="border: 0.5pt solid #000000; background-color: #203764; color: #ffffff;" align="center">No. Agenda</th>
            <th style="border: 0.5pt solid #000000; background-color: #203764; color: #ffffff;" align="center">Nomor Surat</th>
            <th style="border: 0.5pt solid #000000; background-color: #203764; color: #ffffff;" align="center">Tanggal Surat</th>
            <th style="border: 0.5pt solid #000000; background-color: #203764; color: #ffffff;" align="center">Asal / Penerima</th>
            <th style="border: 0.5pt solid #000000; background-color: #203764; color: #ffffff;" align="center">Tujuan</th>
            <th style="border: 0.5pt solid #000000; background-color: #203764; color: #ffffff;" align="center">Perihal</th>
            @if ($filters['jenis_surat'] !== 'Keluar')
                <th style="border: 0.5pt solid #000000; background-color: #203764; color: #ffffff;" align="center">Status Disposisi</th>
            @endif
        </tr>

        <!-- DATA ROWS -->
        @forelse ($suratList as $idx => $s)
            <tr>
                <td style="border: 0.5pt solid #000000;" align="center">{{ $idx + 1 }}</td>
                @if (empty($filters['jenis_surat']))
                    <td style="border: 0.5pt solid #000000;" align="center">{{ $s->jenis_surat }}</td>
                @endif
                <td style="border: 0.5pt solid #000000;" align="center" class="num-text">
                    {{ $s->no_agenda ? '#' . $s->no_agenda : '-' }}
                </td>
                <td style="border: 0.5pt solid #000000;" class="num-text">
                    {{ $s->nomor_surat }}
                </td>
                <td style="border: 0.5pt solid #000000;" align="center" class="num-text">
                    {{ \Carbon\Carbon::parse($s->tanggal_surat)->format('d/m/Y') }}
                </td>
                <td style="border: 0.5pt solid #000000;">
                    @if ($s->jenis_surat === 'Masuk')
                        {{ $s->asal_surat ?: '-' }}
                    @else
                        {{ $s->penerima_surat ?: $s->tujuan_surat ?: '-' }}
                    @endif
                </td>
                <td style="border: 0.5pt solid #000000;">
                    {{ $s->tujuan_surat ?: $s->tujuan_disposisi ?: '-' }}
                </td>
                <td style="border: 0.5pt solid #000000;">
                    {{ $s->perihal }}
                    @if (!empty($s->agenda))
                        ({{ $s->agenda }})
                    @endif
                </td>
                @if ($filters['jenis_surat'] !== 'Keluar')
                    <td style="border: 0.5pt solid #000000;" align="center">
                        @if ($s->jenis_surat === 'Masuk')
                            {{ $s->status_disposisi ?: 'Belum Didisposisikan' }}
                        @else
                            -
                        @endif
                    </td>
                @endif
            </tr>
        @empty
            <tr>
                <td colspan="{{ $colSpan }}" align="center" style="border: 0.5pt solid #000000; padding: 15pt;">
                    Tidak ada data surat pada periode ini.
                </td>
            </tr>
        @endforelse

        <tr><td></td></tr>

        <!-- REKAPITULASI JUMLAH SURAT -->
        <tr>
            <td colspan="4" style="border: 0.5pt solid #000000; background-color: #d9e1f2; font-weight: bold;">
                REKAPITULASI JUMLAH SURAT
            </td>
            <td colspan="{{ $colSpan - 4 }}"></td>
        </tr>
        <tr>
            <td colspan="3" style="border: 0.5pt solid #000000;">Total Surat pada Periode Ini</td>
            <td style="border: 0.5pt solid #000000; font-weight: bold;" align="center">{{ $rekap['total'] }}</td>
            <td colspan="{{ $colSpan - 4 }}"></td>
        </tr>
        <tr>
            <td colspan="3" style="border: 0.5pt solid #000000;">- Surat Masuk</td>
            <td style="border: 0.5pt solid #000000;" align="center">{{ $rekap['masuk'] }}</td>
            <td colspan="{{ $colSpan - 4 }}"></td>
        </tr>
        <tr>
            <td colspan="3" style="border: 0.5pt solid #000000;">- Surat Keluar</td>
            <td style="border: 0.5pt solid #000000;" align="center">{{ $rekap['keluar'] }}</td>
            <td colspan="{{ $colSpan - 4 }}"></td>
        </tr>
        @if ($rekap['masuk'] > 0)
            <tr>
                <td colspan="3" style="border: 0.5pt solid #000000;">&nbsp;&nbsp;&bull; Status Belum Didisposisikan</td>
                <td style="border: 0.5pt solid #000000;" align="center">{{ $rekap['belum_disposisi'] }}</td>
                <td colspan="{{ $colSpan - 4 }}"></td>
            </tr>
            <tr>
                <td colspan="3" style="border: 0.5pt solid #000000;">&nbsp;&nbsp;&bull; Status Sudah Didisposisikan</td>
                <td style="border: 0.5pt solid #000000;" align="center">{{ $rekap['sudah_disposisi'] }}</td>
                <td colspan="{{ $colSpan - 4 }}"></td>
            </tr>
            <tr>
                <td colspan="3" style="border: 0.5pt solid #000000;">&nbsp;&nbsp;&bull; Status Selesai</td>
                <td style="border: 0.5pt solid #000000;" align="center">{{ $rekap['selesai'] }}</td>
                <td colspan="{{ $colSpan - 4 }}"></td>
            </tr>
        @endif

        <tr><td></td></tr>
        <tr><td></td></tr>

        <!-- TANDA TANGAN -->
        @php
            $bulanIndo = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
            ];
            $now = \Carbon\Carbon::now();
            $tglCetak = 'Sadaniang, ' . $now->day . ' ' . ($bulanIndo[$now->month] ?? '') . ' ' . $now->year;
        @endphp
        <tr>
            <td colspan="{{ $colSpan - 3 }}"></td>
            <td colspan="3" align="left">{{ $tglCetak }}</td>
        </tr>
        <tr>
            <td colspan="{{ $colSpan - 3 }}"></td>
            <td colspan="3" align="left">Mengetahui,</td>
        </tr>
        <tr>
            <td colspan="{{ $colSpan - 3 }}"></td>
            <td colspan="3" align="left">Kepala Sekolah</td>
        </tr>
        <tr><td></td></tr>
        <tr><td></td></tr>
        <tr><td></td></tr>
        <tr>
            <td colspan="{{ $colSpan - 3 }}"></td>
            <td colspan="3" align="left" style="font-weight: bold; text-decoration: underline;">
                Fransiska Ika, S.S, M.Pd.
            </td>
        </tr>
        <tr>
            <td colspan="{{ $colSpan - 3 }}"></td>
            <td colspan="3" align="left">
                NIP. 19850122 200904 2 002
            </td>
        </tr>
    </table>
</body>
</html>
