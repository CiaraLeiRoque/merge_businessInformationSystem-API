<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Image;

class ImagesController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'image1' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image2' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image3' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image4' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image5' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $images = new Image();
        foreach (['image1', 'image2', 'image3', 'image4', 'image5'] as $field) {
            if ($request->hasFile($field)) {
                $path = $request->file($field)->store('images', 'public');
                $images->$field = $path;
            }
        }
        $images->save();

        return response()->json(['message' => 'Images stored successfully'], 200);
    }

    public function update(Request $request){
       
        
        $request->validate([
            'image1'=>'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image2'=>'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image3'=>'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image4'=>'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image5'=>'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $images = Image::first(); 

        foreach (['image1', 'image2', 'image3', 'image4', 'image5'] as $field) {
            if ($request->hasFile($field)) {
                $path = $request->file($field)->store('images', 'public');
                $images->$field = $path;
            }
        }

        $images->save();

        return response()->json(['message' => 'Images updated successfully'], 200);


    }

    public function info(Request $request){
        $business_id = $request->query('business_id');
        $images = Image::where('business_id', $business_id)->first();
        return response()->json($images);
    }
}
