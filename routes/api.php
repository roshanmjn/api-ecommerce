<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;

Route::get('/', function () {
    return response()->json([
        'message' => 'Hello World'
    ]);
});

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::prefix('users')->group(function () {
    Route::post('/', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});
// Protected routes
Route::middleware('jwt')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/logout-all', [AuthController::class, 'logoutFromAllDevices']);
});

Route::prefix('roles')->group(function () {
    Route::get('/', [RoleController::class, 'index']);
    Route::post('/', [RoleController::class, 'store']);
    Route::get('/{id}', [RoleController::class, 'show']);
    Route::put('/{id}', [RoleController::class, 'update']);
    Route::delete('/{id}', [RoleController::class, 'destroy']);
    Route::post('/{id}/permissions', [RoleController::class, 'assignPermissionsToRole']);
    Route::get('/{id}/permissions', [RoleController::class, 'getRoleWithPermissions']);
    Route::post('/{id}/user', [RoleController::class, 'assignRoleToUser']);
});

Route::prefix('permissions')->group(function () {
    Route::get('/', [PermissionController::class, 'index']);
    Route::post('/', [PermissionController::class, 'store']);
    Route::get('/{id}', [PermissionController::class, 'show']);
    Route::put('/{id}', [PermissionController::class, 'update']);
    Route::delete('/{id}', [PermissionController::class, 'destroy']);
    Route::post('users/{email}', [PermissionController::class, 'assignPermissionsToUser']);
    Route::get('users/{email}', [PermissionController::class, 'getUserPermissions']);
});
