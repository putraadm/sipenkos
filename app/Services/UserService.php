<?php

namespace App\Services;

use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Pagination\LengthAwarePaginator;

class UserService
{
    /**
     * Get list of users with filtering and sorting.
     */
    public function getUsersList(array $filters): LengthAwarePaginator
    {
        $search = $filters['search'] ?? null;
        $sortBy = $filters['sort_by'] ?? 'id';
        $sortDirection = $filters['sort_direction'] ?? 'asc';
        $perPage = $filters['per_page'] ?? 10;

        $query = User::with('role')->where('id', '!=', auth()->id());

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('username', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('no_wa', 'like', '%' . $search . '%');
            });
        }
        
        $query->orderBy($sortBy, $sortDirection);

        return $query->paginate($perPage);
    }

    /**
     * Get all roles.
     */
    public function getAllRoles()
    {
        return Role::all();
    }

    /**
     * Create a new user.
     */
    public function createUser(array $data): User
    {
        return User::create([
            'username' => $data['username'],
            'email' => $data['email'],
            'no_wa' => $data['no_wa'],
            'password' => Hash::make($data['password']),
            'role_id' => $data['role_id'],
        ]);
    }

    /**
     * Update an existing user.
     */
    public function updateUser(User $user, array $data): bool
    {
        $updateData = [
            'username' => $data['username'],
            'email' => $data['email'],
            'no_wa' => $data['no_wa'],
            'role_id' => $data['role_id'],
        ];

        if (isset($data['password']) && !empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        return $user->update($updateData);
    }

    /**
     * Delete a user.
     */
    public function deleteUser(User $user): bool
    {
        if ($user->id === auth()->id()) {
            throw new \Exception('Tidak dapat menghapus diri sendiri.');
        }

        return $user->delete();
    }
}
