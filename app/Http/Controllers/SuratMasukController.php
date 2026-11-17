<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class SuratMasukController extends Controller
{
    /**
     * Tampilkan halaman utama surat masuk.
     */
    public function index()
    {
        return view('surat_masuk.index');
    }

    /**
     * Load data surat masuk untuk DataTables (AJAX POST).
     */
    public function data(Request $request): JsonResponse
    {
        $query = DB::table('surat_masuk')
            ->leftJoin('users', 'surat_masuk.user_id', '=', 'users.id')
            ->select([
                'surat_masuk.id',
                'surat_masuk.nomor_surat',
                'surat_masuk.tanggal_surat',
                'surat_masuk.tanggal_terima',
                'surat_masuk.asal_surat',
                'surat_masuk.perihal',
                'surat_masuk.tujuan_disposisi',
                'surat_masuk.status_disposisi',
                'surat_masuk.keterangan',
                'surat_masuk.file_arsip',
                'surat_masuk.created_at',
                'users.name as creator_name',
            ]);

        // Filter status disposisi
        if ($request->filled('status_disposisi')) {
            $query->where('surat_masuk.status_disposisi', $request->status_disposisi);
        }

        // Filter tanggal surat (mulai & selesai)
        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('surat_masuk.tanggal_surat', '>=', $request->tanggal_mulai);
        }
        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('surat_masuk.tanggal_surat', '<=', $request->tanggal_selesai);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('tanggal_surat', function ($row) {
                return date('d/m/Y', strtotime($row->tanggal_surat));
            })
            ->editColumn('tanggal_terima', function ($row) {
                return date('d/m/Y', strtotime($row->tanggal_terima));
            })
            ->addColumn('status_badge', function ($row) {
                $badgeClass = match ($row->status_disposisi) {
                    'Selesai' => 'bg-success',
                    'Sudah Didisposisikan' => 'bg-info text-dark',
                    default => 'bg-warning text-dark',
                };

                return '<span class="badge ' . $badgeClass . '">' . e($row->status_disposisi) . '</span>';
            })
            ->addColumn('file_badge', function ($row) {
                if ($row->file_arsip && Storage::disk('public')->exists($row->file_arsip)) {
                    $ext = strtolower(pathinfo($row->file_arsip, PATHINFO_EXTENSION));
                    $icon = in_array($ext, ['jpg', 'jpeg', 'png']) ? 'bi-file-earmark-image' : 'bi-file-earmark-pdf';
                    $btnColor = in_array($ext, ['jpg', 'jpeg', 'png']) ? 'btn-outline-primary' : 'btn-outline-danger';

                    return '<div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn ' . $btnColor . ' btn-preview-file" 
                                    data-url="' . route('surat-masuk.preview', $row->id) . '" 
                                    data-ext="' . $ext . '" 
                                    data-nomor="' . e($row->nomor_surat) . '" 
                                    title="Pratinjau File">
                                    <i class="bi ' . $icon . ' me-1"></i> ' . strtoupper($ext) . '
                                </button>
                                <a href="' . route('surat-masuk.download', $row->id) . '" class="btn btn-outline-secondary" title="Unduh File">
                                    <i class="bi bi-download"></i>
                                </a>
                            </div>';
                }

                return '<span class="text-muted"><i class="bi bi-dash-circle me-1"></i>Tidak ada</span>';
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
                                data-nomor="' . e($row->nomor_surat) . '" title="Hapus Surat">
                                <i class="bi bi-trash-fill"></i>
                              </button>';

                return '<div class="d-flex align-items-center">' . $btnDetail . $btnEdit . $btnDelete . '</div>';
            })
            ->rawColumns(['status_badge', 'file_badge', 'action'])
            ->make(true);
    }

    /**
     * Ambil detail data 1 surat masuk untuk modal detail / edit.
     */
    public function show($id): JsonResponse
    {
        $surat = DB::table('surat_masuk')
            ->leftJoin('users', 'surat_masuk.user_id', '=', 'users.id')
            ->where('surat_masuk.id', $id)
            ->select([
                'surat_masuk.*',
                'users.name as creator_name',
            ])
            ->first();

        if (! $surat) {
            return response()->json([
                'success' => false,
                'message' => 'Data surat masuk tidak ditemukan.',
            ], 404);
        }

        $fileUrl = null;
        $fileSize = null;
        $fileExt = null;
        if ($surat->file_arsip && Storage::disk('public')->exists($surat->file_arsip)) {
            $fileUrl = route('surat-masuk.preview', $surat->id);
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
            'download_url' => $surat->file_arsip ? route('surat-masuk.download', $surat->id) : null,
        ]);
    }

    /**
     * Simpan data surat masuk baru ke database.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nomor_surat' => ['required', 'string', 'max:255', 'unique:surat_masuk,nomor_surat'],
            'tanggal_surat' => ['required', 'date'],
            'tanggal_terima' => ['required', 'date'],
            'asal_surat' => ['required', 'string', 'max:255'],
            'perihal' => ['required', 'string'],
            'tujuan_disposisi' => ['required', 'string', 'max:255'],
            'status_disposisi' => ['required', Rule::in(['Belum Didisposisikan', 'Sudah Didisposisikan', 'Selesai'])],
            'keterangan' => ['nullable', 'string'],
            'file_arsip' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ], [
            'nomor_surat.required' => 'Nomor surat wajib diisi.',
            'nomor_surat.unique' => 'Nomor surat sudah terdaftar sebelumnya.',
            'tanggal_surat.required' => 'Tanggal surat wajib diisi.',
            'tanggal_terima.required' => 'Tanggal terima surat wajib diisi.',
            'asal_surat.required' => 'Asal surat / pengirim wajib diisi.',
            'perihal.required' => 'Perihal surat wajib diisi.',
            'tujuan_disposisi.required' => 'Tujuan / disposisi surat wajib diisi.',
            'status_disposisi.required' => 'Status disposisi wajib dipilih.',
            'status_disposisi.in' => 'Status disposisi tidak valid.',
            'file_arsip.file' => 'Arsip harus berupa file.',
            'file_arsip.mimes' => 'Format file arsip harus berupa PDF, JPG, JPEG, atau PNG.',
            'file_arsip.max' => 'Ukuran file arsip maksimal 10 MB.',
        ]);

        $filePath = null;
        if ($request->hasFile('file_arsip')) {
            $file = $request->file('file_arsip');
            $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
            $filePath = $file->storeAs('surat_masuk', $fileName, 'public');
        }

        $id = DB::table('surat_masuk')->insertGetId([
            'nomor_surat' => $validated['nomor_surat'],
            'tanggal_surat' => $validated['tanggal_surat'],
            'tanggal_terima' => $validated['tanggal_terima'],
            'asal_surat' => $validated['asal_surat'],
            'perihal' => $validated['perihal'],
            'tujuan_disposisi' => $validated['tujuan_disposisi'],
            'status_disposisi' => $validated['status_disposisi'],
            'keterangan' => $validated['keterangan'] ?? null,
            'file_arsip' => $filePath,
            'user_id' => auth()->id(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Surat masuk berhasil ditambahkan.',
            'id' => $id,
        ]);
    }

    /**
     * Update data surat masuk di database.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $surat = DB::table('surat_masuk')->where('id', $id)->first();
        if (! $surat) {
            return response()->json([
                'success' => false,
                'message' => 'Data surat masuk tidak ditemukan.',
            ], 404);
        }

        $validated = $request->validate([
            'nomor_surat' => ['required', 'string', 'max:255', Rule::unique('surat_masuk', 'nomor_surat')->ignore($id)],
            'tanggal_surat' => ['required', 'date'],
            'tanggal_terima' => ['required', 'date'],
            'asal_surat' => ['required', 'string', 'max:255'],
            'perihal' => ['required', 'string'],
            'tujuan_disposisi' => ['required', 'string', 'max:255'],
            'status_disposisi' => ['required', Rule::in(['Belum Didisposisikan', 'Sudah Didisposisikan', 'Selesai'])],
            'keterangan' => ['nullable', 'string'],
            'file_arsip' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ], [
            'nomor_surat.required' => 'Nomor surat wajib diisi.',
            'nomor_surat.unique' => 'Nomor surat sudah terdaftar sebelumnya.',
            'tanggal_surat.required' => 'Tanggal surat wajib diisi.',
            'tanggal_terima.required' => 'Tanggal terima surat wajib diisi.',
            'asal_surat.required' => 'Asal surat / pengirim wajib diisi.',
            'perihal.required' => 'Perihal surat wajib diisi.',
            'tujuan_disposisi.required' => 'Tujuan / disposisi surat wajib diisi.',
            'status_disposisi.required' => 'Status disposisi wajib dipilih.',
            'status_disposisi.in' => 'Status disposisi tidak valid.',
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
            $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
            $filePath = $file->storeAs('surat_masuk', $fileName, 'public');
        }

        DB::table('surat_masuk')->where('id', $id)->update([
            'nomor_surat' => $validated['nomor_surat'],
            'tanggal_surat' => $validated['tanggal_surat'],
            'tanggal_terima' => $validated['tanggal_terima'],
            'asal_surat' => $validated['asal_surat'],
            'perihal' => $validated['perihal'],
            'tujuan_disposisi' => $validated['tujuan_disposisi'],
            'status_disposisi' => $validated['status_disposisi'],
            'keterangan' => $validated['keterangan'] ?? null,
            'file_arsip' => $filePath,
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data surat masuk berhasil diperbarui.',
        ]);
    }

    /**
     * Hapus data surat masuk beserta file arsipnya.
     */
    public function destroy($id): JsonResponse
    {
        $surat = DB::table('surat_masuk')->where('id', $id)->first();
        if (! $surat) {
            return response()->json([
                'success' => false,
                'message' => 'Data surat masuk tidak ditemukan.',
            ], 404);
        }

        // Hapus file arsip fisik jika ada
        if ($surat->file_arsip && Storage::disk('public')->exists($surat->file_arsip)) {
            Storage::disk('public')->delete($surat->file_arsip);
        }

        DB::table('surat_masuk')->where('id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Surat masuk beserta arsipnya berhasil dihapus.',
        ]);
    }

    /**
     * Pratinjau (preview) file arsip secara inline di browser.
     */
    public function preview($id)
    {
        $surat = DB::table('surat_masuk')->where('id', $id)->first();
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
        $surat = DB::table('surat_masuk')->where('id', $id)->first();
        if (! $surat || ! $surat->file_arsip || ! Storage::disk('public')->exists($surat->file_arsip)) {
            abort(404, 'File arsip surat tidak ditemukan.');
        }

        $ext = pathinfo($surat->file_arsip, PATHINFO_EXTENSION);
        $cleanNomor = preg_replace('/[^a-zA-Z0-9-_]/', '_', $surat->nomor_surat);
        $downloadName = 'Surat_Masuk_' . $cleanNomor . '.' . $ext;

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

