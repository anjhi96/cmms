<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index()
    {
        $users = User::latest()->paginate(20);

        return view('users.index', compact('users'));
    }

    public function create()
    {
        $roles = [
            User::ROLE_GUEST,
            User::ROLE_ADMIN,
            User::ROLE_KOORDINATOR_WWD,
            User::ROLE_KOORDINATOR_BUL,
            User::ROLE_PIC_WWD,
            User::ROLE_PIC_BUL,
        ];

        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => [
                'required',
                'in:' . implode(',', [
                    User::ROLE_GUEST,
                    User::ROLE_ADMIN,
                    User::ROLE_KOORDINATOR_WWD,
                    User::ROLE_KOORDINATOR_BUL,
                    User::ROLE_PIC_WWD,
                    User::ROLE_PIC_BUL,
                ]),
            ],
        ]);

        User::create([
            'name' => Str::title(strtolower(trim($validated['name']))),
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        return redirect()
            ->route('users.index')
            ->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        $roles = [
            User::ROLE_GUEST,
            User::ROLE_ADMIN,
            User::ROLE_KOORDINATOR_WWD,
            User::ROLE_KOORDINATOR_BUL,
            User::ROLE_PIC_WWD,
            User::ROLE_PIC_BUL,
        ];

        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email,' . $user->id,
            ],
            'password' => ['nullable', 'string', 'min:8'],
            'role' => [
                'required',
                'in:' . implode(',', [
                    User::ROLE_GUEST,
                    User::ROLE_ADMIN,
                    User::ROLE_KOORDINATOR_WWD,
                    User::ROLE_KOORDINATOR_BUL,
                    User::ROLE_PIC_WWD,
                    User::ROLE_PIC_BUL,
                ]),
            ],
        ]);

        $data = [
            'name' => Str::title(strtolower(trim($validated['name']))),
            'email' => $validated['email'],
            'role' => $validated['role'],
        ];

        // Password hanya diubah kalau Admin mengisinya
        if (! empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        return redirect()
            ->route('users.index')
            ->with('success', 'User updated successfully.');
    }
}