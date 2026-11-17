<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class SuratController extends Controller
{
    /**
     * Tampilkan halaman utama data surat (Surat Masuk & Surat Keluar).
     */
    public function index(Request $request)
    {
        $defaultJenis = $request->query('jenis', '');

        return view('surat.index', compact('defaultJenis'));
    }

    /**
     * Load data surat untuk DataTables (AJAX POST).
     */
    public function data(Request $request): JsonResponse
    {
        $query = DB::table('surat')
            ->leftJoin('users', 'surat.user_id', '=', 'users.id')
            ->select([
                'surat.id',
                'surat.jenis_surat',
                'surat.nomor_surat',
                'surat.tanggal_surat',
                'surat.tanggal_terima',
                'surat.asal_surat',
                'surat.tujuan_surat',
                'surat.tujuan_disposisi',
                'surat.perihal',
                'surat.status_disposisi',
                'surat.keterangan',
                'surat.file_arsip',
                'surat.created_at',
                'users.name as creator_name',
            ]);

        return DataTables::of($query)
            ->filter(function ($query) use ($request) {
                // Global search dari DataTables
                if ($request->filled('search.value')) {
                    $search = $request->input('search.value');
                    $query->where(function ($q) use ($search) {
                        $q->where('surat.nomor_surat', 'like', "%{$search}%")
                            ->orWhere('surat.perihal', 'like', "%{$search}%")
                            ->orWhere('surat.asal_surat', 'like', "%{$search}%")
                            ->orWhere('surat.tujuan_surat', 'like', "%{$search}%");
                    });
                }

                // 1. Filter Jenis Surat (Masuk / Keluar)
                if ($request->filled('jenis_surat')) {
                    $query->where('surat.jenis_surat', $request->jenis_surat);
                }

                // 2. Filter Nomor Surat
                if ($request->filled('nomor_surat')) {
                    $query->where('surat.nomor_surat', 'like', '%' . trim($request->nomor_surat) . '%');
                }

                // 3. Filter Perihal
                if ($request->filled('perihal')) {
                    $query->where('surat.perihal', 'like', '%' . trim($request->perihal) . '%');
                }

                // 4. Filter Rentang Tanggal Surat
                if ($request->filled('tanggal_mulai')) {
                    $query->whereDate('surat.tanggal_surat', '>=', $request->tanggal_mulai);
                }
                if ($request->filled('tanggal_selesai')) {
                    $query->whereDate('surat.tanggal_surat', '<=', $request->tanggal_selesai);
                }

                // 5. Filter Status Disposisi
                if ($request->filled('status_disposisi')) {
                    $query->where('surat.status_disposisi', $request->status_disposisi);
                }
            })
            ->addIndexColumn()
            ->addColumn('jenis_badge', function ($row) {
                if ($row->jenis_surat === 'Masuk') {
                    return '<span class="badge bg-success"><i class="bi bi-box-arrow-in-down me-1"></i>Surat Masuk</span>';
                }

                return '<span class="badge bg-primary"><i class="bi bi-box-arrow-up-right me-1"></i>Surat Keluar</span>';
            })
            ->editColumn('tanggal_surat', function ($row) {
                return date('d/m/Y', strtotime($row->tanggal_surat));
            })
            ->addColumn('tujuan_display', function ($row) {
                $tujuan = $row->tujuan_surat ?: $row->tujuan_disposisi ?: '-';
                if ($row->jenis_surat === 'Masuk' && $row->asal_surat) {
                    return '<div><strong>Dari:</strong> ' . e($row->asal_surat) . '</div>' .
                           '<div class="text-muted small"><strong>Disposisi:</strong> ' . e($tujuan) . '</div>';
                }

                return '<div><strong>Kepada:</strong> ' . e($tujuan) . '</div>';
            })
            ->addColumn('status_badge', function ($row) {
                if ($row->jenis_surat === 'Keluar') {
                    return '<span class="badge bg-light-primary text-primary"><i class="bi bi-send-check me-1"></i>Terkirim</span>';
                }

                $badgeClass = match ($row->status_disposisi) {
                    'Selesai' => 'bg-success',
                    'Sudah Didisposisikan' => 'bg-info text-dark',
                    default => 'bg-warning text-dark',
                };
                $status = $row->status_disposisi ?: 'Belum Didisposisikan';

                return '<span class="badge ' . $badgeClass . '">' . e($status) . '</span>';
            })
            ->addColumn('file_badge', function ($row) {
                if ($row->file_arsip && Storage::disk('public')->exists($row->file_arsip)) {
                    $ext = strtolower(pathinfo($row->file_arsip, PATHINFO_EXTENSION));
                    $icon = in_array($ext, ['jpg', 'jpeg', 'png']) ? 'bi-file-earmark-image' : 'bi-file-earmark-pdf';
                    $btnColor = in_array($ext, ['jpg', 'jpeg', 'png']) ? 'btn-outline-primary' : 'btn-outline-danger';

                    return '<div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn ' . $btnColor . ' btn-preview-file" 
                                    data-url="' . route('surat.preview', $row->id) . '" 
                                    data-ext="' . $ext . '" 
                                    data-nomor="' . e($row->nomor_surat) . '" 
                                    title="Pratinjau Berkas">
                                    <i class="bi ' . $icon . ' me-1"></i> ' . strtoupper($ext) . '
                                </button>
                                <a href="' . route('surat.download', $row->id) . '" class="btn btn-outline-secondary" title="Unduh Berkas">
                                    <i class="bi bi-download"></i>
                                </a>
                            </div>';
                }

                return '<span class="text-muted small"><i class="bi bi-dash-circle me-1"></i>Tidak ada</span>';
            })
            ->addColumn('action', function ($row) {
                $btnDetail = '<button type="button" class="btn btn-sm btn-info text-dark btn-detail me-1" 
                                data-id="' . $row->id . '" title="Lihat Detail">
                                <i class="bi bi-eye-fill"></i>
                              </button>';

                $btnEdit = '<button type="button" class="btn btn-sm btn-warning text-white btn-edit me-1" 
                                data-id="' . $row->id . '" title="Edit Surat">
                                <i class="bi bi-pencil-square"></i>
                            </button>';

                $btnDelete = '<button type="button" class="btn btn-sm btn-danger btn-delete" 
                                data-id="' . $row->id . '" 
                                data-nomor="' . e($row->nomor_surat) . '" 
                                data-jenis="' . e($row->jenis_surat) . '" 
                                title="Hapus Surat">
                                <i class="bi bi-trash-fill"></i>
                              </button>';

                return '<div class="d-flex align-items-center">' . $btnDetail . $btnEdit . $btnDelete . '</div>';
            })
            ->rawColumns(['jenis_badge', 'tujuan_display', 'status_badge', 'file_badge', 'action'])
            ->make(true);
    }

    /**
     * Ambil detail data 1 surat untuk modal detail / edit.
     */
    public function show($id): JsonResponse
    {
        $surat = DB::table('surat')
            ->leftJoin('users', 'surat.user_id', '=', 'users.id')
            ->where('surat.id', $id)
            ->select([
                'surat.*',
                'users.name as creator_name',
            ])
            ->first();

        if (! $surat) {
            return response()->json([
                'success' => false,
                'message' => 'Data surat tidak ditemukan.',
            ], 404);
        }

        $fileUrl = null;
        $fileSize = null;
        $fileExt = null;
        if ($surat->file_arsip && Storage::disk('public')->exists($surat->file_arsip)) {
            $fileUrl = route('surat.preview', $surat->id);
            $bytes = Storage::disk('public')->size($surat->file_arsip);
            $fileSize = $this->formatBytes($bytes);
            $fileExt = strtolower(pathinfo($surat->file_arsip, PATHINFO_EXTENSION));
        }

        return response()->json([
            'success' => true,
            'data' => $surat,
            'file_url' => $fileUrl,
            'file_size' => $fileSize,
            'file_ext' => $fileExt,
            'download_url' => $surat->file_arsip ? route('surat.download', $surat->id) : null,
        ]);
    }

    /**
     * Simpan data surat baru (Masuk atau Keluar) ke database.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'jenis_surat' => ['required', Rule::in(['Masuk', 'Keluar'])],
            'nomor_surat' => ['required', 'string', 'max:255', 'unique:surat,nomor_surat'],
            'tanggal_surat' => ['required', 'date'],
            'tujuan_surat' => ['required', 'string', 'max:255'],
            'perihal' => ['required', 'string'],
            'keterangan' => ['nullable', 'string'],
            'file_arsip' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],

            // Khusus Surat Masuk
            'asal_surat' => [Rule::requiredIf($request->jenis_surat === 'Masuk'), 'nullable', 'string', 'max:255'],
            'tanggal_terima' => [Rule::requiredIf($request->jenis_surat === 'Masuk'), 'nullable', 'date'],
            'status_disposisi' => [Rule::requiredIf($request->jenis_surat === 'Masuk'), 'nullable', Rule::in(['Belum Didisposisikan', 'Sudah Didisposisikan', 'Selesai'])],
        ], [
            'jenis_surat.required' => 'Jenis surat wajib dipilih.',
            'jenis_surat.in' => 'Jenis surat harus Masuk atau Keluar.',
            'nomor_surat.required' => 'Nomor surat wajib diisi.',
            'nomor_surat.unique' => 'Nomor surat sudah terdaftar sebelumnya.',
            'tanggal_surat.required' => 'Tanggal surat wajib diisi.',
            'tujuan_surat.required' => $request->jenis_surat === 'Keluar' ? 'Tujuan / penerima surat wajib diisi.' : 'Tujuan / disposisi surat wajib diisi.',
            'asal_surat.required' => 'Asal surat / pengirim wajib diisi untuk surat masuk.',
            'tanggal_terima.required' => 'Tanggal terima surat wajib diisi untuk surat masuk.',
            'status_disposisi.required' => 'Status disposisi wajib dipilih untuk surat masuk.',
            'perihal.required' => 'Perihal surat wajib diisi.',
            'file_arsip.file' => 'Arsip harus berupa file.',
            'file_arsip.mimes' => 'Format file arsip harus berupa PDF, JPG, JPEG, atau PNG.',
            'file_arsip.max' => 'Ukuran file arsip maksimal 10 MB.',
        ]);

        $filePath = null;
        if ($request->hasFile('file_arsip')) {
            $file = $request->file('file_arsip');
            $folder = $validated['jenis_surat'] === 'Keluar' ? 'surat_keluar' : 'surat_masuk';
            $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
            $filePath = $file->storeAs($folder, $fileName, 'public');
        }

        $id = DB::table('surat')->insertGetId([
            'jenis_surat' => $validated['jenis_surat'],
            'nomor_surat' => $validated['nomor_surat'],
            'tanggal_surat' => $validated['tanggal_surat'],
            'tanggal_terima' => $validated['jenis_surat'] === 'Masuk' ? ($validated['tanggal_terima'] ?? now()->toDateString()) : null,
            'asal_surat' => $validated['jenis_surat'] === 'Masuk' ? ($validated['asal_surat'] ?? null) : null,
            'tujuan_surat' => $validated['tujuan_surat'],
            'tujuan_disposisi' => $validated['tujuan_surat'],
            'perihal' => $validated['perihal'],
            'status_disposisi' => $validated['jenis_surat'] === 'Masuk' ? ($validated['status_disposisi'] ?? 'Belum Didisposisikan') : null,
            'keterangan' => $validated['keterangan'] ?? null,
            'file_arsip' => $filePath,
            'user_id' => auth()->id(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $label = $validated['jenis_surat'] === 'Keluar' ? 'Surat Keluar' : 'Surat Masuk';

        return response()->json([
            'success' => true,
            'message' => "{$label} berhasil ditambahkan.",
            'id' => $id,
        ]);
    }

    /**
     * Update data surat di database.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $surat = DB::table('surat')->where('id', $id)->first();
        if (! $surat) {
            return response()->json([
                'success' => false,
                'message' => 'Data surat tidak ditemukan.',
            ], 404);
        }

        $validated = $request->validate([
            'jenis_surat' => ['required', Rule::in(['Masuk', 'Keluar'])],
            'nomor_surat' => ['required', 'string', 'max:255', Rule::unique('surat', 'nomor_surat')->ignore($id)],
            'tanggal_surat' => ['required', 'date'],
            'tujuan_surat' => ['required', 'string', 'max:255'],
            'perihal' => ['required', 'string'],
            'keterangan' => ['nullable', 'string'],
            'file_arsip' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],

            // Khusus Surat Masuk
            'asal_surat' => [Rule::requiredIf($request->jenis_surat === 'Masuk'), 'nullable', 'string', 'max:255'],
            'tanggal_terima' => [Rule::requiredIf($request->jenis_surat === 'Masuk'), 'nullable', 'date'],
            'status_disposisi' => [Rule::requiredIf($request->jenis_surat === 'Masuk'), 'nullable', Rule::in(['Belum Didisposisikan', 'Sudah Didisposisikan', 'Selesai'])],
        ], [
            'jenis_surat.required' => 'Jenis surat wajib dipilih.',
            'jenis_surat.in' => 'Jenis surat harus Masuk atau Keluar.',
            'nomor_surat.required' => 'Nomor surat wajib diisi.',
            'nomor_surat.unique' => 'Nomor surat sudah terdaftar sebelumnya.',
            'tanggal_surat.required' => 'Tanggal surat wajib diisi.',
            'tujuan_surat.required' => $request->jenis_surat === 'Keluar' ? 'Tujuan / penerima surat wajib diisi.' : 'Tujuan / disposisi surat wajib diisi.',
            'asal_surat.required' => 'Asal surat / pengirim wajib diisi untuk surat masuk.',
            'tanggal_terima.required' => 'Tanggal terima surat wajib diisi untuk surat masuk.',
            'status_disposisi.required' => 'Status disposisi wajib dipilih untuk surat masuk.',
            'perihal.required' => 'Perihal surat wajib diisi.',
            'file_arsip.file' => 'Arsip harus berupa file.',
            'file_arsip.mimes' => 'Format file arsip harus berupa PDF, JPG, JPEG, atau PNG.',
            'file_arsip.max' => 'Ukuran file arsip maksimal 10 MB.',
        ]);

        $filePath = $surat->file_arsip;
        if ($request->hasFile('file_arsip')) {
            // Hapus file lama jika ada
            if ($surat->file_arsip && Storage::disk('public')->exists($surat->file_arsip)) {
                Storage::disk('public')->delete($surat->file_arsip);
            }

            $file = $request->file('file_arsip');
            $folder = $validated['jenis_surat'] === 'Keluar' ? 'surat_keluar' : 'surat_masuk';
            $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
            $filePath = $file->storeAs($folder, $fileName, 'public');
        }

        DB::table('surat')->where('id', $id)->update([
            'jenis_surat' => $validated['jenis_surat'],
            'nomor_surat' => $validated['nomor_surat'],
            'tanggal_surat' => $validated['tanggal_surat'],
            'tanggal_terima' => $validated['jenis_surat'] === 'Masuk' ? ($validated['tanggal_terima'] ?? null) : null,
            'asal_surat' => $validated['jenis_surat'] === 'Masuk' ? ($validated['asal_surat'] ?? null) : null,
            'tujuan_surat' => $validated['tujuan_surat'],
            'tujuan_disposisi' => $validated['tujuan_surat'],
            'perihal' => $validated['perihal'],
            'status_disposisi' => $validated['jenis_surat'] === 'Masuk' ? ($validated['status_disposisi'] ?? null) : null,
            'keterangan' => $validated['keterangan'] ?? null,
            'file_arsip' => $filePath,
            'updated_at' => now(),
        ]);

        $label = $validated['jenis_surat'] === 'Keluar' ? 'Surat Keluar' : 'Surat Masuk';

        return response()->json([
            'success' => true,
            'message' => "Data {$label} berhasil diperbarui.",
        ]);
    }

    /**
     * Hapus data surat beserta file arsipnya.
     */
    public function destroy($id): JsonResponse
    {
        $surat = DB::table('surat')->where('id', $id)->first();
        if (! $surat) {
            return response()->json([
                'success' => false,
                'message' => 'Data surat tidak ditemukan.',
            ], 404);
        }

        // Hapus file arsip fisik jika ada
        if ($surat->file_arsip && Storage::disk('public')->exists($surat->file_arsip)) {
            Storage::disk('public')->delete($surat->file_arsip);
        }

        DB::table('surat')->where('id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data surat beserta arsipnya berhasil dihapus.',
        ]);
    }

    /**
     * Pratinjau (preview) file arsip secara inline di browser.
     */
    public function preview($id)
    {
        $surat = DB::table('surat')->where('id', $id)->first();
        if (! $surat || ! $surat->file_arsip || ! Storage::disk('public')->exists($surat->file_arsip)) {
            abort(404, 'File arsip surat tidak ditemukan.');
        }

        return Storage::disk('public')->response($surat->file_arsip);
    }

    /**
     * Unduh (download) file arsip surat.
     */
    public function download($id)
    {
        $surat = DB::table('surat')->where('id', $id)->first();
        if (! $surat || ! $surat->file_arsip || ! Storage::disk('public')->exists($surat->file_arsip)) {
            abort(404, 'File arsip surat tidak ditemukan.');
        }

        $ext = pathinfo($surat->file_arsip, PATHINFO_EXTENSION);
        $prefix = $surat->jenis_surat === 'Keluar' ? 'Surat_Keluar_' : 'Surat_Masuk_';
        $cleanNomor = preg_replace('/[^a-zA-Z0-9-_]/', '_', $surat->nomor_surat);
        $downloadName = $prefix . $cleanNomor . '.' . $ext;

        return Storage::disk('public')->download($surat->file_arsip, $downloadName);
    }

    /**
     * Helper format ukuran bytes ke string human-readable.
     */
    protected function formatBytes($bytes, $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
