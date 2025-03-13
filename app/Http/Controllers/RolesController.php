<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateRoleRequest;
use GuzzleHttp\Promise\Create;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use PhpParser\Node\Stmt\Return_;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesController extends Controller
{
    // Get all roles
    public function index()
    {
        $roles = Role::with('permissions', 'users')->get();
        return Inertia::render('RolesManagement/Roles', [
            'roles' => $roles
        ]);
    }

    public function create()
    {   
        // get all permissions
        $permissions = Permission::all();

        $groupedPermissions = $permissions->groupBy(function($perm) {
            return explode('.', $perm->name)[0]; // Bagian sebelum dot
        });

        return Inertia::render('RolesManagement/CreateRole', [
            'permissions' => $permissions,
            'groupedPermissions' => $groupedPermissions
        ]);
    }

    public function store (CreateRoleRequest $request)
    {
        $role = Role::create($request->validated());

        $role->syncPermissions($request->permissions);
        
        if ($role) {
            return redirect()->route('roles.index')->with('status', 'Role created successfully!');
        } else {
            return redirect()->route('roles.index')->with('error', 'Failed to create role.');
        }
    }

    public function edit(Role $role)
    {
        $permissions = Permission::all();

        $groupedPermissions = $permissions->groupBy(function($perm) {
            return explode('.', $perm->name)[0]; // Bagian sebelum dot
        });

        return Inertia::render('RolesManagement/EditRole', [
            'role' => $role,
            'permissions' => $permissions,
            'groupedPermissions' => $groupedPermissions
        ]);
    }

    public function update(CreateRoleRequest $request, Role $role)
    {
        $role->update($request->validated());

        $role->syncPermissions($request->permissions);

        return Redirect::route('roles.index')->with('status', 'Role updated successfully!');
    }

    public function destroy(Role $role)
    {   
        // cek jika ada user yang memiliki role ini
        if ($role->users()->exists()) {
            return Redirect::route('roles.index')->with('error', 'Role cannot be deleted because it is still being used by users.');
        }
        
        $role->delete();

        return Redirect::route('roles.index')->with('status', 'Role deleted successfully!');
    }

}
