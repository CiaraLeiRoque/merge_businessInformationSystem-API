<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductPackageName;
use Exception;
use Illuminate\Support\Facades\Log;

class ProductPackageNameController extends Controller
{
    //
    public function show()
    {
        \Log::info('ProductPackageNameController@show called');
        return ProductPackageName::all();
    }

    public function updatePackageName(Request $request, $id)
    {
        try {
            // Find the product package by ID
            $productPackage = ProductPackageName::findOrFail($id);            
            Log::info('Incoming UPDATE PACKAGE ID:', $request->all());

            // Validate the request data
            $request->validate([
                'product_package_name' => 'nullable|string|max:255'
            ]);

            // Update the product package name
            $productPackage->update([
                'product_package_name' => $request->input('product_package_name'),
            ]);

            return response()->json([
                'success' => true,
                'data' => $productPackage,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating product package name.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function showPackageName(Request $request, $package_id)
    {
        

        // Retrieve the package using `first()` to get a single record
        $package = ProductPackageName::where('id', $package_id)->first();
        
        if ($package) {
            // Log the package details
            \Log::info('Returning package ID and package name: ', $package->toArray());
            return response()->json($package);
        } else {
            // Log a warning if the package is not found
            \Log::warning('Package not found for ID: ' . $package_id);
            return response()->json(['error' => 'Package not found'], 404);
        }
    }
    
    public function savePackageName(Request $request)
    {
        try {
            Log::info('Incoming request data:', $request->all());

            // Validate the request (no need to validate product_package_id since it's auto-incremented)
            $validatedData = $request->validate([
                'product_package_name' => 'nullable|string|max:255',
            ]);

            // Create the product package (product_package_id will be auto-incremented by the database)
            $product_package = ProductPackageName::create([
                'product_package_name' => $request->product_package_name,
            ]);

            // Log the created product package
            Log::info('New product package created:', $product_package->toArray());

            // Return the created product package, including the auto-incremented product_package_id
            return response()->json($product_package, 201);
        } catch (Exception $e) {
            Log::error('Error saving product package name:', ['message' => $e->getMessage()]);
            return response()->json([
                'error' => 'Error in generating product package name: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deletePackage($package_id)
    {
        try {
            $package_id = ProductPackageName::where('id', $package_id)->first();
            
            if (!$package_id) {
                return response()->json(['error' => 'package id not found'], 404);
            }

            $package_id->delete();

            return response()->json(['message' => 'package id deleted successfully']);
        } catch (Exception $e) {
            Log::error('Error deleting package id: ' . $e->getMessage());
            return response()->json(['error' => 'Unable to delete package id. Please try again later.'], 500);
        }
    }

}
