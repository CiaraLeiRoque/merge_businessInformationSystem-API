<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductColumnTableVisibility;

class ProductColumnTableVisibilityController extends Controller
{
    //
    public function show()
    {
        \Log::info('ProductColumnTableVisibilityController@show called');
        return ProductColumnTableVisibility::all();
    }


    public function updateVisibility(Request $request)
    {
            // Validate input
            
        \Log::info('Request received', $request->all()); // Logs the request data
            $validated = $request->validate([
                '*.column_Table' => 'required|string|exists:product_column_table_visibilities,column_Table',
                '*.is_visible' => 'required|boolean',
            ]);

            // Log received data for debugging
            \Log::info('Validated request received', $validated);

            // Update settings
            foreach ($validated as $setting) {
                ProductColumnTableVisibility::where('column_Table', $setting['column_Table'])
                    ->update(['is_visible' => $setting['is_visible']]);
            }

            return response()->json(['message' => 'Settings updated successfully']);

        

        foreach ($request->all() as $setting) {
            ProductColumnTableVisibility::where('column_Table', $setting['column_Table'])
                ->update(['is_visible' => $setting['is_visible']]);
        }
        return response()->json(['message' => 'Settings updated successfully']);
    }
}
