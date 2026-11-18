<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Tampilkan halaman dashboard beserta ringkasan statistik surat.
     */
    public function index()
    {
        $currentYear = intval(date('Y'));
        $currentMonth = intval(date('n'));

        // 1. Total surat masuk dan surat keluar (keseluruhan)
        $totalMasuk = DB::table('surat')
            ->where('jenis_surat', 'Masuk')
            ->count();

        $totalKeluar = DB::table('surat')
            ->where('jenis_surat', 'Keluar')
            ->count();

        $totalSurat = $totalMasuk + $totalKeluar;

        // 2. Jumlah surat bulan ini
        $masukBulanIni = DB::table('surat')
            ->where('jenis_surat', 'Masuk')
            ->whereYear('tanggal_surat', $currentYear)
            ->whereMonth('tanggal_surat', $currentMonth)
            ->count();

        $keluarBulanIni = DB::table('surat')
            ->where('jenis_surat', 'Keluar')
            ->whereYear('tanggal_surat', $currentYear)
            ->whereMonth('tanggal_surat', $currentMonth)
            ->count();

        $totalBulanIni = $masukBulanIni + $keluarBulanIni;

        // 3. Surat masuk yang belum didisposisi (khusus untuk Kepala Sekolah / Pimpinan)
        $belumDisposisiCount = DB::table('surat')
            ->where('jenis_surat', 'Masuk')
            ->where(function ($q) {
                $q->where('status_disposisi', 'Belum Didisposisikan')
                  ->orWhereNull('status_disposisi');
            })
            ->count();

        $suratBelumDisposisi = DB::table('surat')
            ->where('jenis_surat', 'Masuk')
            ->where(function ($q) {
                $q->where('status_disposisi', 'Belum Didisposisikan')
                  ->orWhereNull('status_disposisi');
            })
            ->orderBy('tanggal_surat', 'desc')
            ->orderBy('id', 'desc')
            ->limit(5)
            ->get();

        // Status disposisi lainnya untuk pie/progress ringkasan
        $sudahDisposisiCount = DB::table('surat')
            ->where('jenis_surat', 'Masuk')
            ->where('status_disposisi', 'Sudah Didisposisikan')
            ->count();

        $selesaiDisposisiCount = DB::table('surat')
            ->where('jenis_surat', 'Masuk')
            ->where('status_disposisi', 'Selesai')
            ->count();

        // 4. Daftar surat masuk terbaru
        $suratMasukTerbaru = DB::table('surat')
            ->where('jenis_surat', 'Masuk')
            ->orderBy('tanggal_surat', 'desc')
            ->orderBy('id', 'desc')
            ->limit(7)
            ->get();

        // 5. Grafik jumlah surat per bulan tahun berjalan (Januari - Desember)
        $masukPerBulan = DB::table('surat')
            ->selectRaw('MONTH(tanggal_surat) as bulan, COUNT(*) as total')
            ->where('jenis_surat', 'Masuk')
            ->whereYear('tanggal_surat', $currentYear)
            ->groupBy('bulan')
            ->pluck('total', 'bulan')
            ->toArray();

        $keluarPerBulan = DB::table('surat')
            ->selectRaw('MONTH(tanggal_surat) as bulan, COUNT(*) as total')
            ->where('jenis_surat', 'Keluar')
            ->whereYear('tanggal_surat', $currentYear)
            ->groupBy('bulan')
            ->pluck('total', 'bulan')
            ->toArray();

        $chartCategories = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $chartMasuk = [];
        $chartKeluar = [];

        for ($m = 1; $m <= 12; $m++) {
            $chartMasuk[] = (int) ($masukPerBulan[$m] ?? 0);
            $chartKeluar[] = (int) ($keluarPerBulan[$m] ?? 0);
        }

        $namaBulanIndo = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        $currentMonthName = $namaBulanIndo[$currentMonth] ?? '';

        return view('dashboard', compact(
            'totalMasuk',
            'totalKeluar',
            'totalSurat',
            'masukBulanIni',
            'keluarBulanIni',
            'totalBulanIni',
            'belumDisposisiCount',
            'suratBelumDisposisi',
            'sudahDisposisiCount',
            'selesaiDisposisiCount',
            'suratMasukTerbaru',
            'chartCategories',
            'chartMasuk',
            'chartKeluar',
            'currentYear',
            'currentMonthName'
        ));
    }
}

