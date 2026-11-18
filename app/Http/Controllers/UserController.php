<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    /**
     * Check if the authenticated user is an Admin.
     */
    protected function checkAdmin(): void
    {
        if (auth()->user()?->role !== 'Admin') {
            abort(403, 'Akses terbatas untuk Administrator.');
        }
    }

    /**
     * Display a listing of the users.
     */
    public function index()
    {
        $this->checkAdmin();

        $roles = Role::orderBy('name')->get();

        return view('users.index', compact('roles'));
    }

    /**
     * Fetch user data for DataTables (AJAX POST).
     */
    public function data(Request $request): JsonResponse
    {
        $this->checkAdmin();

        $users = User::query()->select(['id', 'name', 'username', 'email', 'role', 'is_active', 'created_at']);

        return DataTables::of($users)
            ->addIndexColumn()
            ->addColumn('role_badge', function ($user) {
                $badgeClass = match ($user->role) {
                    'Admin' => 'bg-primary',
                    'Kepala Sekolah' => 'bg-info text-dark',
                    'Staff' => 'bg-success',
                    default => 'bg-secondary',
                };

                return '<span class="badge ' . $badgeClass . '">' . e($user->role) . '</span>';
            })
            ->addColumn('status_badge', function ($user) {
                if ($user->is_active) {
                    return '<span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Aktif</span>';
                }

                return '<span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Nonaktif</span>';
            })
            ->addColumn('action', function ($user) {
                $isSelf = auth()->id() === $user->id;

                $btnEdit = '<button type="button" class="btn btn-sm btn-warning btn-edit text-white me-1" 
                                data-id="' . $user->id . '" 
                                data-name="' . e($user->name) . '" 
                                data-username="' . e($user->username) . '" 
                                data-email="' . e($user->email) . '" 
                                data-role="' . e($user->role) . '" 
                                data-is_active="' . ($user->is_active ? 1 : 0) . '" 
                                title="Edit Pengguna">
                                <i class="bi bi-pencil-square"></i>
                            </button>';

                $toggleIcon = $user->is_active ? 'bi-person-x' : 'bi-person-check';
                $toggleColor = $user->is_active ? 'btn-secondary' : 'btn-success';
                $toggleTitle = $user->is_active ? 'Nonaktifkan Akun' : 'Aktifkan Akun';
                $toggleAction = $user->is_active ? 'nonaktifkan' : 'aktifkan';

                $btnToggle = '';
                if (! $isSelf) {
                    $btnToggle = '<button type="button" class="btn btn-sm ' . $toggleColor . ' btn-toggle-status me-1" 
                                    data-id="' . $user->id . '" 
                                    data-name="' . e($user->name) . '" 
                                    data-action="' . $toggleAction . '" 
                                    title="' . $toggleTitle . '">
                                    <i class="bi ' . $toggleIcon . '"></i>
                                  </button>';
                }

                $btnReset = '<button type="button" class="btn btn-sm btn-info text-dark btn-reset-password me-1" 
                                data-id="' . $user->id . '" 
                                data-name="' . e($user->name) . '" 
                                title="Reset Password">
                                <i class="bi bi-key-fill"></i>
                            </button>';

                $btnDelete = '';
                if (! $isSelf) {
                    $btnDelete = '<button type="button" class="btn btn-sm btn-danger btn-delete" 
                                    data-id="' . $user->id . '" 
                                    data-name="' . e($user->name) . '" 
                                    title="Hapus Pengguna">
                                    <i class="bi bi-trash-fill"></i>
                                  </button>';
                }

                return '<div class="d-flex align-items-center">' . $btnEdit . $btnToggle . $btnReset . $btnDelete . '</div>';
            })
            ->rawColumns(['role_badge', 'status_badge', 'action'])
            ->make(true);
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $this->checkAdmin();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', 'string', 'exists:roles,name'],
            'password' => ['required', 'string', 'min:6'],
            'is_active' => ['required', 'boolean'],
        ], [
            'name.required' => 'Nama pengguna wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'role.required' => 'Role wajib dipilih.',
            'role.exists' => 'Role yang dipilih tidak valid dalam sistem.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
            'is_active' => $validated['is_active'],
        ]);

        $user->syncRoles([$validated['role']]);

        return response()->json([
            'success' => true,
            'message' => 'Pengguna berhasil ditambahkan.',
            'data' => $user,
        ]);
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user): JsonResponse
    {
        $this->checkAdmin();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($user->id)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => ['required', 'string', 'exists:roles,name'],
            'is_active' => ['required', 'boolean'],
        ], [
            'name.required' => 'Nama pengguna wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'role.required' => 'Role wajib dipilih.',
            'role.exists' => 'Role yang dipilih tidak valid dalam sistem.',
        ]);

        // Prevent self-deactivation or demoting own admin role
        if (auth()->id() === $user->id) {
            $validated['is_active'] = true;
            $validated['role'] = 'Admin';
        }

        $user->update($validated);
        $user->syncRoles([$validated['role']]);

        return response()->json([
            'success' => true,
            'message' => 'Pengguna berhasil diperbarui.',
            'data' => $user,
        ]);
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user): JsonResponse
    {
        $this->checkAdmin();

        if (auth()->id() === $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak dapat menghapus akun Anda sendiri.',
            ], 422);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pengguna berhasil dihapus.',
        ]);
    }

    /**
     * Toggle active/inactive status of the user account.
     */
    public function toggleStatus(User $user): JsonResponse
    {
        $this->checkAdmin();

        if (auth()->id() === $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak dapat mengubah status akun Anda sendiri.',
            ], 422);
        }

        $user->is_active = ! $user->is_active;
        $user->save();

        $statusText = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return response()->json([
            'success' => true,
            'message' => "Akun {$user->name} berhasil {$statusText}.",
            'is_active' => $user->is_active,
        ]);
    }

    /**
     * Reset password for the user.
     */
    public function resetPassword(Request $request, User $user): JsonResponse
    {
        $this->checkAdmin();

        $validated = $request->validate([
            'new_password' => ['required', 'string', 'min:6'],
        ], [
            'new_password.required' => 'Password baru wajib diisi.',
            'new_password.min' => 'Password baru minimal 6 karakter.',
        ]);

        $user->password = Hash::make($validated['new_password']);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => "Password pengguna {$user->name} berhasil direset.",
        ]);
    }
}

