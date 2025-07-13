<?php

namespace App\Http\Controllers;

use App\Models\Discipline;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Controller;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rules;
use Illuminate\Http\RedirectResponse;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        // breadcrumb
        $breadcrumbs = [
            ['name' => 'Users', 'url' => route('users.index')],
        ];
        
        $roles = Cache::remember('roles_list', 3600, function () {
            return Role::select('id', 'name')->get();
        });
        
        $maxPerPage = 50; // Set a maximum limit for perPage
        // Get per page value with default fallback
        $perPage = min((int) request('perPage', 10), $maxPerPage);
        
        // Get sort parameters with defaults
        $sortField = $request->input('sort', 'name');
        $sortDirection = $request->input('direction', 'asc');
        
        // Build the query with filters, sorting and pagination
        $users = User::with('roles', 'discipline')
            ->filter($request->only(['search', 'role']))
            ->when($sortField && $sortDirection, function($query) use ($sortField, $sortDirection) {
                return $query->orderBy($sortField, $sortDirection);
            }, function($query) {
                return $query->orderBy('created_at', 'desc');
            })
            ->paginate($perPage)
            ->withQueryString();

        return Inertia::render('UsersManagement/UserIndex', [
            'users' => $users,
            'breadcrumbs' => $breadcrumbs,
            'roles' => $roles,
            'filters' => $request->only(['search', 'role', 'perPage', 'sort', 'direction']),
        ]);
    }

    public function create()
    {
        // breadcrumb
        $breadcrumbs = [
            ['name' => 'Users', 'url' => route('users.index')],
            ['name' => 'Create User', 'url' => route('users.create')],
        ];
        $roles = Cache::remember('roles_list', 3600, function () {
            return Role::select('name')->get();
        });
        $disciplines = Discipline::select('id', 'name')->get();
        // Only fetch necessary fields to optimize performance
        return Inertia::render('UsersManagement/CreateUser', [
            'roles' => $roles,
            'disciplines' => $disciplines,
            'breadcrumbs' => $breadcrumbs,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'discipline_id'=> $request->discipline_id,
        ]);

        $user->assignRole($request->role);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        // breadcrumb
        $breadcrumbs = [
            ['name' => 'Users', 'url' => route('users.index')],
            ['name' => 'Edit User', 'url' => route('users.edit', $user->id)],
        ];

        $roles = Role::all();
        $disciplines = Discipline::select('id', 'name')->get();
        return Inertia::render('UsersManagement/EditUser', [
            'user' => $user,
            'disciplines' => $disciplines,
            'roles' => $roles,
            'breadcrumbs' => $breadcrumbs,
        ]);
    }
    public function update(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'discipline_id' => $request->discipline_id,
        ]);

        $user->syncRoles($request->role);
        return redirect()->route('users.index');
    }

    public function destroy(User $user): RedirectResponse
    {
        $user->delete();
        return redirect()->route('users.index');
    }

    // UserController.php
    public function search(Request $request)
    {
        $search = $request->get('search');
        $limit = $request->get('limit', 10);
        
        $users = User::with('roles')
            ->where(function($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            })
            ->whereHas('roles', function($query) {
                $query->where('name', 'lead');
            })
            ->limit($limit)
            ->get(['id', 'name', 'email']);
        
        return response()->json([
            'data' => $users
        ]);
    }

    // ambil user dengan discipline terpilih
    public function getUsersByDiscipline(Discipline $discipline)
    {
        $users = User::where('discipline_id', $discipline->id)
            ->select('id', 'name', 'email')
            ->get();
        
        return response()->json([
            'data' => $users
        ]);
    }

    // ambil semua user yang memiliki role Lead dan memiliki discipline tertentu
    public function getUsersForDiscipline(Request $request)
    {
        $disciplineId = $request->input('discipline_id');
        $users = User::whereHas('roles', function($query) {
            $query->where('name', 'lead');
        })
        ->where('discipline_id', $disciplineId)
        ->select('id', 'name', 'email')
        ->get();

        return response()->json([
            'data' => $users
        ]);
    }
}