<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductPackage;
use Exception;

class ProductPackageController extends Controller
{       
    public function show()
    {
        \Log::info('ProductPackageController@show called');
        return ProductPackage::with('packageName')->get();
    }

    public function showProductPackage($package_id)
    {
        $package = ProductPackage::where('product_package_id', $package_id)->get();
    
        if (!$package) {
            return response()->json(['error' => 'product package items not found'], 404);
        }
    
        return response()->json($package);
    }

    public function savePackage(Request $request)
    {
        try{
            $validatedData = $request->validate([
                
                'product_package_id' => 'nullable|numeric',

                'product_id' => 'nullable|numeric',
                'product_name' => 'nullable|string|max:255',
                'product_quantity' => 'nullable|numeric',
            ]);


            $product_package = ProductPackage::create([

                'product_package_id' => $request->product_package_id,
                
                'product_id' => $request->product_id,
                'product_name' => $request->product_name,
                'product_quantity' => $request->product_quantity,
            ]); 


 

        }catch(Exception $e){
            return response()->json(data:['error in generating product package' => $e->getMessage()], status: 500);
        }
    }

    
    public function updateProductPackage(Request $request, $package_id) {
        // Find all invoice items for the given invoice_system_id

        try{

            
        \Log::info('RECEIVING REQUEST DATA FOR UPDATING PRODUCT PACKAGE: ', $request->toArray());


            $validatedData = $request->validate([
                'product_package_id' => 'nullable|numeric',

                'product_id' => 'nullable|numeric',
                'product_name' => 'nullable|string|max:255',
                'product_quantity' => 'nullable|numeric',
            ]);
            

            $invoice_item = ProductPackage::create([
                'product_package_id' => $request->product_package_id,
                
                'product_id' => $request->product_id,
                'product_name' => $request->product_name,
                'product_quantity' => $request->product_quantity,
            ]);
            
        }catch(Exception $e){
            return response()->json(data:['error in updating package items' => $e->getMessage()], status: 500);
        }
    }
    

    public function deleteProductPackage(Request $request, $product_package_id){
        ProductPackage::where('product_package_id', $product_package_id)->delete();
        return response()->json(['message' => 'Product Package Items deleted successfully']);
    }
    

}
