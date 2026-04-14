<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

use App\Services\UserService;

class KelolaUserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    private function checkAccess()
    {
        $user = auth()->user();
        if ($user && $user->role && $user->role->name === 'pemilik_kos') {
            return redirect()->route('pemilik.dashboard');
        }
        return null;
    }

    public function index(Request $request)
    {
        if ($redirect = $this->checkAccess()) return $redirect;

        $users = $this->userService->getUsersList($request->all())->withQueryString();
        $roles = $this->userService->getAllRoles();

        return Inertia::render('admin/user/index', [
            'users' => $users,
            'roles' => $roles,
            'filters' => $request->only('search', 'sort_by', 'sort_direction', 'per_page', 'page')
        ]);
    }

    public function store(Request $request)
    {
        if ($redirect = $this->checkAccess()) return $redirect;

        $request->validate([
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'no_wa' => 'nullable|string|max:20',
            'password' => 'required|string|min:8',
            'role_id' => 'required|exists:roles,id'
        ]);

        $this->userService->createUser($request->only('username', 'email', 'no_wa', 'password', 'role_id'));

        return redirect()->back()->with('success', 'User berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        if ($redirect = $this->checkAccess()) return $redirect;

        $user = User::findOrFail($id);

        $request->validate([
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'no_wa' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8',
            'role_id' => 'required|exists:roles,id'
        ]);

        $this->userService->updateUser($user, $request->only('username', 'email', 'no_wa', 'password', 'role_id'));

        return redirect()->back()->with('success', 'User berhasil diubah.');
    }

    public function destroy($id)
    {
        if ($redirect = $this->checkAccess()) return $redirect;

        $user = User::findOrFail($id);
        
        try {
            $this->userService->deleteUser($user);
            return redirect()->back()->with('success', 'User berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}

