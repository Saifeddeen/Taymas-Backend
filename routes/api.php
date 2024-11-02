<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Login

Route::middleware(["api"])->group(function () {
    // Login
    Route::post('/login', [AuthController::class, 'login']);

    // Auth Routes
    Route::middleware(["auth:sanctum"])->group(function () {
        // Auth user
        Route::get('/user', function (Request $request) {
            if($request->user()->id) {
                return mdsJsonResponse('token available', $request->user(), "success", 200);
            } else {
                return mdsJsonResponse('token not available', [], "success", 200);
            }
        });

        // Logout
        Route::get('/logout', [AuthController::class, "logout"]);

        // Register
        Route::post("/register", [AuthController::class, "register"]);
    });
});
