<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $permissions = $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => ['string', 'max:124'],
        ]);
        $inputPermissions = $permissions['permissions'];
        $existingPermissions = Permission::whereIn('name', $inputPermissions)->pluck('name')->toArray();
        $newPermissions = array_diff($inputPermissions, $existingPermissions);

        foreach ($newPermissions as $name) {
            Permission::create(['name' => $name, 'guard_name' => 'api']);
        }

        return response()->json([
            'success' => true,
            'created' => $newPermissions,
            'existing' => $existingPermissions,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
    public function getUserPermissions(string $email)
    {
        $user = User::where('email', $email)->first();
        if (!$user) {
            throw new NotFoundHttpException("User not found", null, 404);
        }

        $allPermissions = $user->getAllPermissions()->pluck('name');
        return response()->json([
            'permissions' => $allPermissions,
        ]);
    }

    public function assignPermissionToRole(Request $request, string $id)
    {
        $role = is_numeric($id)
            ? Role::find($id)
            : Role::where('name', $id)->first();

        if (!$role) {
            throw new NotFoundHttpException("Role not found");
        }

        $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->syncPermissions($request->permissions);

        return response()->json(['message' => 'Permissions assigned successfully']);
    }

    public function assignPermissionsToUser(Request $request, string $email)
    {
        $user = User::where('email', $email)->first();
        if (!$user) {
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
            // dd($missingPermissions);
            $missingList = implode(', ', $missingPermissions);
            // dd($missingList);
            throw new BadRequestHttpException("Permissions not found: {$missingList}", null, 400);
        }

        $user->givePermissionTo($sanitizedPermissions);

        return response()->json([
            'message' => 'Permissions added',
            'permissions' => $sanitizedPermissions,
        ]);
    }
}
