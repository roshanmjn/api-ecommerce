<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RefreshTokenController;

Route::get('/', function () {
    return response()->json([
        'message' => 'Commerce Haven'
    ]);
});

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


// Refresh endpoint (public: validates cookie internally)
Route::post('/refresh', [RefreshTokenController::class, 'refresh']);

// Protected routes
Route::middleware('jwt')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
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
    Route::get('/{id}', [PermissionController::class, 'show']);
    Route::post('/', [PermissionController::class, 'store']);
    Route::put('/{id}', [PermissionController::class, 'update']);
    Route::delete('/{id}', [PermissionController::class, 'destroy']);
    Route::post('users/{email}', [PermissionController::class, 'assignPermissionsToUser']);
    Route::get('users/{email}', [PermissionController::class, 'getUserPermissions']);
});

Route::prefix('products')
    ->group(function () {
        Route::get('/', [ProductController::class, 'allProducts']);
        Route::middleware('jwt')->group(function () {
            Route::get('{id}', [ProductController::class, 'productById']);
            Route::post('/', [ProductController::class, 'store']);
            Route::put('{id}', [ProductController::class, 'update']);
            Route::delete('{id}', [ProductController::class, 'delete']);
        });
    });

Route::prefix('categories')
    ->group(function () {
        Route::get('/', [CategoryController::class, 'index']);
        Route::middleware('jwt')->group(function () {
            Route::get('/{id}', [CategoryController::class, 'show']);
            Route::post('/', [CategoryController::class, 'store']);
            Route::put('/{id}', [CategoryController::class, 'update']);
            Route::delete('/{id}', [CategoryController::class, 'destroy']);
        });
    });
