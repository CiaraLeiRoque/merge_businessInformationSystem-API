<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductPackageName;
use App\Models\ProductPackage;
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

    public function getAllPackageNamesWithProducts()
    {
        try {
            $packageNames = ProductPackageName::with('packages')->get();
            
            if ($packageNames->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'message' => 'No package names found.',
                    'data' => [],
                ]);
            }

            \Log::info('SHOWING ALL PACKAGE NAMES WITH PRODUCTS called', [
                'packages' => $packageNames->toArray()
            ]);
            
            return response()->json($packageNames);
            
        } catch (\Exception $e) {
            \Log::error('Error fetching product package names with products: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching product package names with products.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function updatePackageName(Request $request, $id)
    {
        try {
            // Find the product package by ID
            $productPackage = ProductPackageName::findOrFail($id);            
            Log::info('Incoming UPDATE PACKAGE ID:', $request->all());

            // Validate the request data
            $validated = $request->validate([
                'image' => 'nullable|image|mimes:jpg,png,jpeg|max:5120',
                'product_package_name' => 'nullable|string|max:255',
                'product_package_description' => ''
            ]);

            // Update the product package name

            $productPackage->update($validated);
    
            if ($request->hasFile('image')) {
                // Handle image upload
                $imagePath = $request->file('image')->store('products', 'public');
                $productPackage->image = $imagePath;
                $productPackage->save();
            }

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
            $request->validate([                
                'image' => 'nullable|image|mimes:jpg,png,jpeg|max:5120',
                'product_package_name' => 'nullable|string|max:255',
                'product_package_description' => ''
            ]);

            
            // // Create the product package (product_package_id will be auto-incremented by the database)
            // $product_package = ProductPackageName::create([
            //     'product_package_name' => $request->product_package_name,
            //     'product_package_description' => $request->product_package_description,
            // ]);

            $productPackageName = new ProductPackageName($request->except('image'));

            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('products', 'public');
                $productPackageName->image = $path;
            }

            $productPackageName->save();

            // Log the created product package
            Log::info('New product package created:', $productPackageName->toArray());

            // Return the created product package, including the auto-incremented product_package_id
            return response()->json($productPackageName, 201);
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
