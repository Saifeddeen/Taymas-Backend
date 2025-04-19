<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        try {
            $products = Product::with('brand')->paginate(10);
            return mdsJsonResponse("products list", $products, "success");
        } catch (Exception $e) {
            return mdsJsonResponse("products list failed", $e->getMessage(), "failed", 200);
        }
    }

    // Get Trash
    public function trash()
    {
        //
        try {
            $products = Product::onlyTrashed()->paginate(10);
            return mdsJsonResponse("trash products list", $products, "success");
        } catch (Exception $e) {
            return mdsJsonResponse("trash products list failed", $e->getMessage(), "failed", 200);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
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
                "name" => "nullable|string|max:255",
                "brand_id" => "nullable|numeric|max:255",
                "quantity" => "nullable|numeric|max:255",
                "package_average_cost" => "nullable|numeric|max:255",
                "package_average_price" => "nullable|numeric|max:255",
                "description" => "nullable|string|max:10000",
                "image" => "nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048"
            ]);

            if ($inputs['brand_id']) {
                $brand = Brand::findOrFail($inputs['brand_id']);
                $inputs['brand_id'] = $brand->id;
            }

            // Check for image
            if ($request->hasFile("image")) {
                // Get name to store
                $originalName = $request->image->getClientOriginalName();
                $fullName = time() . "_" . $originalName;

                // Store
                $request->image->move(public_path("images/products"), $fullName);

                // Get the stored path
                $inputs["image"] = "images/products/" . $fullName;
            }

            // Create
            $product = Product::create($inputs);

            return mdsJsonResponse("create product", ["product" => $product], "success");
        } catch (Exception $e) {
            return mdsJsonResponse("create product error", $e->getMessage(), "failed", 200);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        //
        try {
            if ($product->trashed()) {
                return mdsJsonResponse("this is a trashed product", $product, "failed");
            }

            $allRules = [
                "name" => "nullable|string|max:255",
                "brand_id" => "nullable|numeric|max:255",
                "quantity" => "nullable|numeric|max:255",
                "package_average_cost" => "nullable|numeric|max:255",
                "package_average_price" => "nullable|numeric|max:255",
                "description" => "nullable|string|max:10000",
                "image" => "nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048"
            ];

            $actualRules = array_filter($allRules, function ($key) use ($request) {
                return $request->has($key);
            }, ARRAY_FILTER_USE_KEY);

            $inputs = $request->validate($actualRules);

            // Check for image
            if ($request->hasFile("image")) {
                // Get name to store
                $originalName = $request->image->getClientOriginalName();
                $fullName = time() . "_" . $originalName;

                // Store
                $request->image->move(public_path("images/products"), $fullName);

                // Get the stored path
                $inputs["image"] = "images/products/" . $fullName;
            }

            // Update
            $product->update($inputs);

            return mdsJsonResponse("product updated successfully", $product, "success");
        } catch (Exception $e) {
            return mdsJsonResponse("update product error", $e->getMessage(), "failed", 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        //
        try {
            // Delete
            $product->delete();

            return mdsJsonResponse("delete product", ["product" => $product], "success");
        } catch (Exception $e) {
            return mdsJsonResponse("delete product error", $e->getMessage(), "failed", 200);
        }
    }

    public function restore(Product $product)
    {
        //
        try {
            // Restore
            if ($product) {
                $product->restore();
                return mdsJsonResponse("restore product", ["product" => $product], "success");
            } else {
                return mdsJsonResponse("restore product", "product not found", "failed");
            }
        } catch (Exception $e) {
            return mdsJsonResponse("restore product error", $e->getMessage(), "failed", 200);
        }
    }
}
