<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Production;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        try {
            $productions = Production::with('product')->paginate(10);
            return mdsJsonResponse("productions list", $productions, "success");
        } catch (Exception $e) {
            return mdsJsonResponse("productions list failed", $e->getMessage(), "failed", 200);
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
            DB::transaction(function () use ($request, &$product, &$production) {
                $inputs = $request->validate([
                    "product_id" => "required|numeric|max:255",
                    "quantity" => "required|numeric|max:255|min:1",
                    "cost" => "nullable|numeric|max:255",
                    "price" => "nullable|numeric|max:255",
                    "production_date" => "nullable|string|max:255",
                    "expiry_date" => "nullable|string|max:255",
                    "comments" => "nullable|string|max:10000"
                ]);

                if ($inputs['product_id']) {
                    $product = Product::findOrFail($inputs['product_id']);
                    $inputs['product_id'] = $product->id;
                }

                // Create
                $production = Production::create($inputs);

                $oldQuantity = $product->quantity;
                $product->quantity = $oldQuantity + $production->quantity;
                $product->package_average_cost = (($oldQuantity * $product->package_average_cost) + ($production->quantity * $production->cost)) / ($oldQuantity + $production->quantity);
                $product->package_average_price = (($oldQuantity * $product->package_average_price) + ($production->quantity * $production->price)) / ($oldQuantity + $production->quantity);

                $product->update();
            });
            return mdsJsonResponse("production record stored successfully", ["production" => $production], "success");
        } catch (Exception $e) {
            return mdsJsonResponse("create product error", $e->getMessage(), "failed", 200);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Production $production)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Production $production)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Production $production)
    {
        //
        try {

            DB::transaction(function () use ($request, &$production) {

                $allRules = [
                    "product_id" => "required|numeric|max:255",
                    "quantity" => "required|numeric|max:255|min:1",
                    "cost" => "nullable|numeric|max:255",
                    "price" => "nullable|numeric|max:255",
                    "production_date" => "nullable|string|max:255",
                    "expiry_date" => "nullable|string|max:255",
                    "comments" => "nullable|string|max:10000"
                ];

                $actualRules = array_filter($allRules, function ($key) use ($request) {
                    return $request->has($key);
                }, ARRAY_FILTER_USE_KEY);

                $inputs = $request->validate($actualRules);

                if ($inputs['product_id']) {
                    $product = Product::findOrFail($inputs['product_id']);
                    $inputs['product_id'] = $product->id;
                }

                // Update
                $production->update($inputs);
                
                $oldQuantity = $product->quantity;
                $product->quantity = $oldQuantity + $production->quantity;
                $product->package_average_cost = (($oldQuantity * $product->package_average_cost) + ($production->quantity * $production->cost)) / ($oldQuantity + $production->quantity);
                $product->package_average_price = (($oldQuantity * $product->package_average_price) + ($production->quantity * $production->price)) / ($oldQuantity + $production->quantity);

                $product->update();
                
            });

            return mdsJsonResponse("productiion updated successfully", ["production" => $production], "success");

        } catch (Exception $e) {
            return mdsJsonResponse("update productiion error", $e->getMessage(), "failed", 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Production $production)
    {
        //
        try {
            // Delete
            $production->delete();
            return mdsJsonResponse("production deleted successfully", ["production" => $production], "success");
        } catch (Exception $e) {
            return mdsJsonResponse("delete production error", $e->getMessage(), "failed", 200);
        }
    }
}
