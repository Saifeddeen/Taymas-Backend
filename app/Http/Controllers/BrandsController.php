<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Exception;
use Illuminate\Http\Request;

class BrandsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        try {
            $brands = Brand::paginate(10);
            return mdsJsonResponse("brands list", $brands, "success");
        } catch (Exception $e) {
            return mdsJsonResponse("brands list failed", $e->getMessage(), "failed", 200);
        }
    }

    public function searchBrands(Request $request)
    {
        //
        try {

            if (isset($request['name_like'])) {
                $inputs = $request->validate([
                    'name_like' => 'nullable|string|max:255'
                ]);

                if ($inputs['name_like']) {
                    $brands = Brand::whereRaw('LOWER(name) LIKE ?', '%' . strtolower($inputs['name_like']) . '%')->get();
                } else {
                    $brands = Brand::all();
                }
            } else {
                $brands = Brand::all();
            }


            return mdsJsonResponse("brands list", $brands, "success");
        } catch (Exception $e) {
            return mdsJsonResponse("brands list failed", $e->getMessage(), "failed", 200);
        }
    }

    // Get Trash
    public function trash()
    {
        //
        try {
            $brands = Brand::onlyTrashed()->paginate(10);
            return mdsJsonResponse("trash brands list", $brands, "success");
        } catch (Exception $e) {
            return mdsJsonResponse("trash brands list failed", $e->getMessage(), "failed", 200);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($request)
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        try {
            $inputs = $request->validate([
                "name" => "required|string|max:255",
                "description" => "nullable|string|max:10000",
                "image" => "nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048"
            ]);

            // Check for image
            if ($request->hasFile("image")) {
                // Get name to store
                $originalName = $request->image->getClientOriginalName();
                $fullName = time() . "_" . $originalName;

                // Store
                $request->image->move(public_path("images/brands"), $fullName);

                // Get the stored path
                $inputs["image"] = "images/brands/" . $fullName;
            }

            // Create
            $brand = Brand::create($inputs);

            return mdsJsonResponse("create brand", ["brand" => $brand], "success");
        } catch (Exception $e) {
            return mdsJsonResponse("create brand error", $e->getMessage(), "failed", 200);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Brand $brand)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Brand $brand)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Brand $brand)
    {
        //
        try {
            if ($brand->trashed()) {
                return mdsJsonResponse("this is a trashed brand", $brand, "failed");
            }

            $inputs = $request->validate([
                "name" => "required|string|max:255",
                "description" => "nullable|string|max:10000",
                "image" => "nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048"
            ]);

            // Check for image
            if ($request->hasFile("image")) {
                // Get name to store
                $originalName = $request->image->getClientOriginalName();
                $fullName = time() . "_" . $originalName;

                // Store
                $request->image->move(public_path("images/brands"), $fullName);

                // Get the stored path
                $inputs["image"] = "images/brands/" . $fullName;
            }

            // Update
            $brand->update($inputs);

            return mdsJsonResponse("brand updated successfully", $brand, "success");
        } catch (Exception $e) {
            return mdsJsonResponse("update brand error", $e->getMessage(), "failed", 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Brand $brand)
    {
        //
        try {
            // Delete
            $brand->delete();

            return mdsJsonResponse("delete brand", ["brand" => $brand], "success");
        } catch (Exception $e) {
            return mdsJsonResponse("delete brand error", $e->getMessage(), "failed", 200);
        }
    }

    public function restore(Brand $brand)
    {
        //
        try {
            // Restore
            if ($brand) {
                $brand->restore();
                return mdsJsonResponse("restore brand", ["brand" => $brand], "success");
            } else {
                return mdsJsonResponse("restore brand", "brand not found", "failed");
            }
        } catch (Exception $e) {
            return mdsJsonResponse("restore brand error", $e->getMessage(), "failed", 200);
        }
    }
}
