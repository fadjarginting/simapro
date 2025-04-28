<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateRoleRequest;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesController extends Controller
{
    // Get roles with server-side pagination and search
    public function index(Request $request)
    {
        // Get request parameters
        $search = $request->input('search', '');
        $maxPerPage = 50; // Set a maximum limit for perPage
        $perPage = min((int) $request->input('perPage', 10), $maxPerPage);
        $sortField = $request->input('sortField', 'name');
        $sortDirection = $request->input('sortDirection', 'asc');
        
        // Start with a base query
        $query = Role::query();
        
        // Add search condition if provided - with proper parameter binding
        if (!empty($search)) {
            $query->where('name', 'LIKE', '%' . $search . '%');
        }
        
        // Add sorting
        $query->orderBy($sortField, $sortDirection);
        
        // Get paginated results with relationships
        $roles = $query->with(['permissions', 'users'])
                       ->paginate($perPage)->appends($request->query());  // Append query parameters manually
        
        // breadcrumb
        $breadcrumbs = [
            ['name' => 'Roles', 'url' => route('roles.index')],
        ];

        return Inertia::render('RolesManagement/Roles', [
            'roles' => $roles,
            'breadcrumbs' => $breadcrumbs,
            'filters' => [
                'search' => $search,
                'perPage' => $perPage,
                'sortField' => $sortField,
                'sortDirection' => $sortDirection
            ]
        ]);
    }

    public function create()
    {   
        // breadcrumb
        $breadcrumbs = [
            ['name' => 'Roles', 'url' => route('roles.index')],
            ['name' => 'Create Role', 'url' => route('roles.create')],
        ];
        // get all permissions
        $permissions = Permission::all();

        $groupedPermissions = $permissions->groupBy(function($perm) {
            return explode('.', $perm->name)[0]; // Group by the part before the dot
        });

        return Inertia::render('RolesManagement/CreateRole', [
            'permissions' => $permissions,
            'groupedPermissions' => $groupedPermissions,
            'breadcrumbs' => $breadcrumbs,
        ]);
    }

    public function store(CreateRoleRequest $request)
    {
        try {
            $role = Role::create($request->validated());
            $role->syncPermissions($request->permissions);
            
            return redirect()->route('roles.index')->with('status', 'Role created successfully!');
        } catch (\Exception $e) {
            return redirect()->route('roles.index')->with('error', 'Failed to create role: ' . $e->getMessage());
        }
    }

    public function edit(Role $role)
    {
        // Eager-load permissions relation
        $role->load('permissions');

        $breadcrumbs = [
            ['name' => 'Roles',      'url' => route('roles.index')],
            ['name' => 'Edit Role',  'url' => route('roles.edit', $role)],
        ];

        $permissions = Permission::all();
        $groupedPermissions = $permissions->groupBy(fn($perm) => explode('.', $perm->name)[0]);

        return Inertia::render('RolesManagement/EditRole', [
            // Send role as array with only the permissions names
            'role' => [
                'id'          => $role->id,
                'name'        => $role->name,
                'permissions' => $role->permissions->pluck('name'), 
            ],
            'groupedPermissions' => $groupedPermissions,
            'breadcrumbs'        => $breadcrumbs,
        ]);
    }

    public function update(CreateRoleRequest $request, Role $role)
    {
        try {
            $role->update($request->validated());
            $role->syncPermissions($request->permissions);
            
            return redirect()->route('roles.index')->with('status', 'Role updated successfully!');
        } catch (\Exception $e) {
            return redirect()->route('roles.index')->with('error', 'Failed to update role: ' . $e->getMessage());
        }
        
    }

    public function destroy(Role $role)
    {   
        // Check if any users have this role
        if ($role->users()->exists()) {
            return Redirect::route('roles.index')->with('error', 'Role cannot be deleted because it is still being used by users.');
        }
        
        try {
            $roleName = $role->name;
            $role->delete();
            return Redirect::route('roles.index')->with('status', 'Role "' . $roleName . '" deleted successfully!');
        } catch (\Exception $e) {
            return Redirect::route('roles.index')->with('error', 'Role cannot be deleted because it is still being used by users.');    
        }
    }
}