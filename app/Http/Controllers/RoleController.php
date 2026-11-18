<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Permission;
use App\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class RoleController extends Controller
{
    /**
     * Daftar grup permission agar rapi dan mudah diatur
     */
    private function getPermissionGroups()
    {
        return [
            'Surat Masuk' => [
                'surat-masuk.view' => 'Melihat Data & Detail Surat Masuk',
                'surat-masuk.create' => 'Menambah / Input Surat Masuk',
                'surat-masuk.edit' => 'Mengubah / Edit Surat Masuk',
                'surat-masuk.delete' => 'Menghapus Surat Masuk',
                'surat-masuk.disposisi' => 'Mengisi & Mengubah Disposisi Surat',
            ],
            'Surat Keluar' => [
                'surat-keluar.view' => 'Melihat Data & Detail Surat Keluar',
                'surat-keluar.create' => 'Menambah & Menghasilkan Surat Keluar',
                'surat-keluar.edit' => 'Mengubah Surat Keluar',
                'surat-keluar.delete' => 'Menghapus Surat Keluar',
                'surat-keluar.cetak' => 'Mencetak & Mengunduh Dokumen Surat Keluar',
            ],
            'Laporan' => [
                'laporan.view' => 'Melihat Menu & Rekapitulasi Laporan',
                'laporan.cetak' => 'Mencetak Laporan Surat',
                'laporan.export' => 'Ekspor Laporan ke PDF & Excel',
            ],
            'Manajemen Pengguna' => [
                'users.view' => 'Melihat Daftar Pengguna',
                'users.manage' => 'Menambah, Mengedit & Mengelola Pengguna',
            ],
            'Manajemen Role & Hak Akses' => [
                'roles.view' => 'Melihat Daftar Role',
                'roles.manage' => 'Menambah, Mengedit & Mengatur Hak Akses Role',
            ],
        ];
    }

    /**
     * Halaman index Manajemen Role
     */
    public function index()
    {
        $permissionGroups = $this->getPermissionGroups();
        $allPermissions = Permission::orderBy('name')->get();
        $totalRoles = Role::count();
        $totalPermissions = Permission::count();

        return view('roles.index', compact('permissionGroups', 'allPermissions', 'totalRoles', 'totalPermissions'));
    }

    /**
     * Data AJAX DataTables untuk tabel roles
     */
    public function data(Request $request)
    {
        $roles = Role::with('permissions')->withCount('users')->select('roles.*');

        return DataTables::of($roles)
            ->addIndexColumn()
            ->addColumn('permissions_badge', function ($role) {
                $count = $role->permissions->count();
                if ($count === 0) {
                    return '<span class="badge bg-secondary">Tidak ada hak akses</span>';
                }
                
                $html = '<div class="d-flex flex-wrap gap-1 align-items-center">';
                $html .= '<span class="badge bg-primary me-1">' . $count . ' Hak Akses</span>';
                
                // Tampilkan beberapa preview permission
                $preview = $role->permissions->take(3);
                foreach ($preview as $p) {
                    $html .= '<span class="badge bg-light text-dark border small">' . e($p->name) . '</span>';
                }
                if ($count > 3) {
                    $html .= '<span class="badge bg-light text-muted border small">+' . ($count - 3) . ' lainnya</span>';
                }
                $html .= '</div>';
                return $html;
            })
            ->addColumn('users_count_badge', function ($role) {
                return '<span class="badge bg-info text-dark font-monospace"><i class="bi bi-people-fill me-1"></i>' . $role->users_count . ' Pengguna</span>';
            })
            ->addColumn('action', function ($role) {
                $isSystemRole = in_array(strtolower($role->name), ['admin']);
                
                $btn = '<div class="btn-group" role="group">';
                $btn .= '<button type="button" class="btn btn-sm btn-warning btn-edit-role text-white" data-id="' . $role->id . '" title="Edit Role & Permission">
                            <i class="bi bi-pencil-square"></i>
                        </button>';

                if (!$isSystemRole) {
                    $btn .= '<button type="button" class="btn btn-sm btn-danger btn-delete-role" data-id="' . $role->id . '" data-name="' . e($role->name) . '" title="Hapus Role">
                                <i class="bi bi-trash-fill"></i>
                            </button>';
                } else {
                    $btn .= '<button type="button" class="btn btn-sm btn-secondary" disabled title="Role Utama Sistem Tidak Boleh Dihapus">
                                <i class="bi bi-lock-fill"></i>
                            </button>';
                }
                $btn .= '</div>';

                return $btn;
            })
            ->rawColumns(['permissions_badge', 'users_count_badge', 'action'])
            ->make(true);
    }

    /**
     * Simpan Role baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name',
        ], [
            'name.required' => 'Nama Role wajib diisi.',
            'name.unique' => 'Nama Role sudah digunakan, silakan gunakan nama lain.',
        ]);

        DB::beginTransaction();
        try {
            $role = Role::create([
                'name' => trim($request->name),
                'guard_name' => 'web',
            ]);

            if ($request->has('permissions') && is_array($request->permissions)) {
                $role->syncPermissions($request->permissions);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Role "' . $role->name . '" beserta hak aksesnya berhasil dibuat.',
                'data' => $role
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat role: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ambil data role spesifik untuk modal Edit
     */
    public function show($id)
    {
        $role = Role::with('permissions')->find($id);

        if (!$role) {
            return response()->json([
                'success' => false,
                'message' => 'Role tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $role->id,
                'name' => $role->name,
                'is_system' => in_array(strtolower($role->name), ['admin']),
                'permissions' => $role->permissions->pluck('name')->toArray(),
            ]
        ]);
    }

    /**
     * Perbarui data Role dan sinkronisasi Permissions
     */
    public function update(Request $request, $id)
    {
        $role = Role::find($id);

        if (!$role) {
            return response()->json([
                'success' => false,
                'message' => 'Role tidak ditemukan.'
            ], 404);
        }

        $request->validate([
            'name' => 'required|string|max:100|unique:roles,name,' . $role->id,
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name',
        ], [
            'name.required' => 'Nama Role wajib diisi.',
            'name.unique' => 'Nama Role sudah digunakan oleh role lain.',
        ]);

        DB::beginTransaction();
        try {
            $isSystemRole = in_array(strtolower($role->name), ['admin']);

            // Jika bukan role sistem inti, boleh ubah nama
            if (!$isSystemRole) {
                $role->name = trim($request->name);
                $role->save();
            }

            // Update hak akses (sync permissions)
            $permissions = $request->permissions ?? [];
            
            // Khusus Admin selalu pertahankan semua permission
            if ($isSystemRole) {
                $role->syncPermissions(Permission::all());
            } else {
                $role->syncPermissions($permissions);
            }

            // Reset cache permissions
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Role "' . $role->name . '" dan hak akses berhasil diperbarui.',
                'data' => $role
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui role: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Hapus Role
     */
    public function destroy($id)
    {
        $role = Role::withCount('users')->find($id);

        if (!$role) {
            return response()->json([
                'success' => false,
                'message' => 'Role tidak ditemukan.'
            ], 404);
        }

        if (in_array(strtolower($role->name), ['admin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Role Admin adalah role utama sistem dan tidak boleh dihapus.'
            ], 403);
        }

        if ($role->users_count > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Role "' . $role->name . '" tidak dapat dihapus karena masih digunakan oleh ' . $role->users_count . ' pengguna. Silakan alihkan role pengguna terlebih dahulu.'
            ], 422);
        }

        DB::beginTransaction();
        try {
            $roleName = $role->name;
            $role->syncPermissions([]);
            $role->delete();

            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Role "' . $roleName . '" berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus role: ' . $e->getMessage()
            ], 500);
        }
    }
}

