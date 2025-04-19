<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrandsController;
use App\Http\Controllers\ProductionController;
use App\Http\Controllers\ProductsController;
use App\Models\Brand;
use App\Models\Product;
use App\Models\Production;
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

        // Brands Management APIs
        // Route::resource("brands", BrandsController::class);

        // To bind brand_id with brand record for restore method
        Route::bind('brand', function ($value) { return Brand::withTrashed()->findOrFail($value); });
        Route::prefix("brands")->controller(BrandsController::class)->group(function () {
            Route::get("/", "index");
            Route::get("/search", "searchBrands");
            Route::get("/trash", "trash");
            Route::post("/", "store");
            Route::post("/{brand}", "update");
            Route::delete("/{brand}", "destroy");
            Route::post("/restore/{brand}", "restore");
        });

        // To bind brand_id with brand record for restore method
        Route::bind('product', function ($value) { return Product::withTrashed()->findOrFail($value); });
        Route::prefix("products")->controller(ProductsController::class)->group(function () {
            Route::get("/", "index");
            Route::get("/trash", "trash");
            Route::post("/", "store");
            Route::post("/{product}", "update");
            Route::delete("/{product}", "destroy");
            Route::post("/restore/{product}", "restore");
        });

        // To bind brand_id with brand record for restore method
        Route::bind('production', function ($value) { return Production::withTrashed()->findOrFail($value); });
        Route::prefix("productions")->controller(ProductionController::class)->group(function () {
            Route::get("/", "index");
            // Route::get("/trash", "trash");
            Route::post("/", "store");
            Route::post("/{product}", "update");
            Route::delete("/{product}", "destroy");
            // Route::post("/restore/{product}", "restore");
        });
        // Route::get('/routes', function () { return response()->json(Route::getRoutes()); });
    });
});
