<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    use \App\Traits\ApiResponse;

    public function index()
    {
        $users = User::latest()->paginate(20);
        return $this->success($users);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'required|in:user,admin',
        ]);

        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);

        return $this->success($user, 'Usuario creado exitosamente', 201);
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        return $this->success($user);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $id,
            'password' => 'sometimes|min:6',
            'role' => 'sometimes|in:user,admin',
        ]);

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);
        return $this->success($user, 'Usuario actualizado exitosamente');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return $this->error('No puedes eliminar tu propia cuenta', 400);
        }

        $user->delete();
        return $this->success(null, 'Usuario eliminado exitosamente');
    }
}
