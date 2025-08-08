<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->query('search', '');
        $limit = $request->query('limit', 15);
        $page = $request->query('page', 1);
        $query = Role::query();
        if ($search) {
            $query->whereLike('name', $search);
        }
        return $query->paginate($limit, ['*'], 'page', $page);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
        ]);

        $role = Role::create(['name' => $request->name, 'guard_name' => 'api']);

        return response()->json($role, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $role = is_numeric($id)
            ? Role::findOrFail($id)
            : Role::where('name', $id)->firstOrFail();

        return response()->json($role);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    public function assignRoleToUser(Request $request, string $id)
    {
        $user = $request->validate([
            'email' => 'required|string|max:255',
        ]);
        $user = User::where('email', $user['email'])->first();

        if (!$user) {
            throw new NotFoundHttpException("User not found", null, 404);
        }

        $role = is_numeric($id)
            ? Role::find($id)
            : Role::where('name', $id)->first();

        if (!$role) {
            throw new NotFoundHttpException("Role not found", null, 404);
        }

        if (!$user->hasRole($role)) {
            $user->assignRole($role);
        }

        return response()->json(['message' => 'Role assigned successfully']);
    }

    public function getRoleWithPermissions(string $roleId)
    {
        $role = Role::with('permissions:id,name')->select('id', 'name')->find($roleId);

        if (!$role) {
            throw new NotFoundHttpException("Role not found. ID: {$roleId}");
        }

        $role->permissions->makeHidden(['id', 'pivot']); // Hide the pivot attribute under the permissions object being returned
        $role->setRelation('permissions', $role->permissions->pluck('name'));

        return response()->json([
            'role' => $role,
        ]);
    }

    public function assignPermissionsToRole(Request $request, string $roleId)
    {
        $role = Role::find($roleId);
        if (!$role) {
            throw new NotFoundHttpException("User not found", null, 404);
        }

        $payload = $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => ['string'],
        ]);

        $sanitizedPermissions = array_map('trim', $payload['permissions']);
        $existingPermissions = Permission::whereIn('name', $sanitizedPermissions)->pluck('name')->toArray();
        $missingPermissions = array_diff($sanitizedPermissions, $existingPermissions);

        if (!empty($missingPermissions)) {
            $missingList = implode(', ', $missingPermissions);
            throw new BadRequestHttpException("Permissions not found: {$missingList}", null, 400);
        }

        $role->givePermissionTo($sanitizedPermissions);

        return response()->json([
            'message' => 'Permissions added',
            'permissions' => $sanitizedPermissions,
        ]);
    }
}
