<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    /**
     * Helper untuk memproses filter dan query laporan
     */
    private function buildLaporanQuery(Request $request)
    {
        $jenisSurat = $request->get('jenis_surat', '');
        $periodeType = $request->get('periode_type', 'bulanan');
        $statusDisposisi = $request->get('status_disposisi', '');

        // Default tanggal harian
        $tanggal = $request->get('tanggal', date('Y-m-d'));

        // Default bulan dan tahun
        $bulan = (int) $request->get('bulan', date('n'));
        $tahun = (int) $request->get('tahun', date('Y'));

        // Default rentang tanggal (30 hari terakhir jika kosong)
        $tanggalMulai = $request->get('tanggal_mulai', date('Y-m-01'));
        $tanggalSelesai = $request->get('tanggal_selesai', date('Y-m-d'));

        $query = DB::table('surat');

        // Filter berdasarkan jenis surat
        if (!empty($jenisSurat) && in_array($jenisSurat, ['Masuk', 'Keluar'])) {
            $query->where('jenis_surat', $jenisSurat);
        }

        // Filter status disposisi (hanya untuk surat masuk)
        if (!empty($statusDisposisi)) {
            $query->where('status_disposisi', $statusDisposisi);
        }

        // Label deskripsi periode untuk judul laporan
        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $periodeLabel = '';

        // Filter berdasarkan mode periode
        if ($periodeType === 'harian') {
            $query->whereDate('tanggal_surat', $tanggal);
            $tglObj = Carbon::parse($tanggal);
            $periodeLabel = 'Harian: ' . $tglObj->day . ' ' . ($namaBulan[$tglObj->month] ?? '') . ' ' . $tglObj->year;
        } elseif ($periodeType === 'tahunan') {
            $query->whereYear('tanggal_surat', $tahun);
            $periodeLabel = 'Tahun ' . $tahun;
        } elseif ($periodeType === 'rentang') {
            $query->whereBetween('tanggal_surat', [$tanggalMulai, $tanggalSelesai]);
            $mulaiObj = Carbon::parse($tanggalMulai);
            $selesaiObj = Carbon::parse($tanggalSelesai);
            $periodeLabel = $mulaiObj->day . ' ' . ($namaBulan[$mulaiObj->month] ?? '') . ' ' . $mulaiObj->year .
                ' s.d. ' . $selesaiObj->day . ' ' . ($namaBulan[$selesaiObj->month] ?? '') . ' ' . $selesaiObj->year;
        } else {
            // Default: bulanan
            $periodeType = 'bulanan';
            $query->whereMonth('tanggal_surat', $bulan)->whereYear('tanggal_surat', $tahun);
            $periodeLabel = 'Bulan ' . ($namaBulan[$bulan] ?? '') . ' ' . $tahun;
        }

        // Hitung Rekapitulasi Statistik pada periode yang sama (tanpa filter jenis surat agar rekap utuh)
        $rekapQuery = DB::table('surat');
        if ($periodeType === 'harian') {
            $rekapQuery->whereDate('tanggal_surat', $tanggal);
        } elseif ($periodeType === 'tahunan') {
            $rekapQuery->whereYear('tanggal_surat', $tahun);
        } elseif ($periodeType === 'rentang') {
            $rekapQuery->whereBetween('tanggal_surat', [$tanggalMulai, $tanggalSelesai]);
        } else {
            $rekapQuery->whereMonth('tanggal_surat', $bulan)->whereYear('tanggal_surat', $tahun);
        }

        $allDataPeriode = $rekapQuery->get();
        $rekap = [
            'total' => $allDataPeriode->count(),
            'masuk' => $allDataPeriode->where('jenis_surat', 'Masuk')->count(),
            'keluar' => $allDataPeriode->where('jenis_surat', 'Keluar')->count(),
            'belum_disposisi' => $allDataPeriode->where('jenis_surat', 'Masuk')->where('status_disposisi', 'Belum Didisposisikan')->count(),
            'sudah_disposisi' => $allDataPeriode->where('jenis_surat', 'Masuk')->where('status_disposisi', 'Sudah Didisposisikan')->count(),
            'selesai' => $allDataPeriode->where('jenis_surat', 'Masuk')->where('status_disposisi', 'Selesai')->count(),
        ];

        // Ambil data surat terurut
        $suratList = $query->orderBy('tanggal_surat', 'desc')->orderBy('id', 'desc')->get();

        return [
            'suratList' => $suratList,
            'rekap' => $rekap,
            'periodeLabel' => $periodeLabel,
            'filters' => [
                'jenis_surat' => $jenisSurat,
                'periode_type' => $periodeType,
                'tanggal' => $tanggal,
                'bulan' => $bulan,
                'tahun' => $tahun,
                'tanggal_mulai' => $tanggalMulai,
                'tanggal_selesai' => $tanggalSelesai,
                'status_disposisi' => $statusDisposisi,
            ]
        ];
    }

    /**
     * Tampilan utama Menu Laporan
     */
    public function index(Request $request)
    {
        $data = $this->buildLaporanQuery($request);

        return view('laporan.index', [
            'suratList' => $data['suratList'],
            'rekap' => $data['rekap'],
            'periodeLabel' => $data['periodeLabel'],
            'filters' => $data['filters']
        ]);
    }

    /**
     * Tampilan Cetak Laporan (Print Preview)
     */
    public function cetak(Request $request)
    {
        $data = $this->buildLaporanQuery($request);

        return view('laporan.cetak', [
            'suratList' => $data['suratList'],
            'rekap' => $data['rekap'],
            'periodeLabel' => $data['periodeLabel'],
            'filters' => $data['filters']
        ]);
    }

    /**
     * Ekspor Laporan ke PDF (Landscape A4)
     */
    public function exportPdf(Request $request)
    {
        $data = $this->buildLaporanQuery($request);

        $pdf = Pdf::loadView('laporan.pdf', [
            'suratList' => $data['suratList'],
            'rekap' => $data['rekap'],
            'periodeLabel' => $data['periodeLabel'],
            'filters' => $data['filters']
        ])->setPaper('a4', 'landscape');

        $jenisText = $data['filters']['jenis_surat'] ?: 'Semua';
        $safePeriode = preg_replace('/[^a-zA-Z0-9_-]/', '_', $data['periodeLabel']);
        $filename = 'Laporan_Surat_' . $jenisText . '_' . $safePeriode . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Ekspor Laporan ke Excel (.xls Spreadsheet)
     */
    public function exportExcel(Request $request)
    {
        $data = $this->buildLaporanQuery($request);

        $jenisText = $data['filters']['jenis_surat'] ?: 'Semua';
        $safePeriode = preg_replace('/[^a-zA-Z0-9_-]/', '_', $data['periodeLabel']);
        $filename = 'Laporan_Surat_' . $jenisText . '_' . $safePeriode . '.xls';

        $html = view('laporan.excel', [
            'suratList' => $data['suratList'],
            'rekap' => $data['rekap'],
            'periodeLabel' => $data['periodeLabel'],
            'filters' => $data['filters']
        ])->render();

        return response($html)
            ->header('Content-Type', 'application/vnd.ms-excel; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }
}

