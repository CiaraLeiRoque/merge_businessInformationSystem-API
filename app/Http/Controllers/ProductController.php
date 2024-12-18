<?php

namespace App\Http\Controllers;

use App\Exports\ExportProduct;
use App\Imports\ImportProduct;
use App\Exports\ExportProductTemplate;
use App\Exports\ExportMasterProduct;
use App\Models\ProductColumnTableVisibility;
use App\Models\Product;
use App\Exports\ProductsExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Carbon\carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProductsImport;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Validators\ValidationException; 
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Subscribers;
use Illuminate\Support\Facades\Mail;

class ProductController extends Controller
{

    public function getStock($id)
    {
        // Find the product by its ID and select only the stock field
        $product = Product::find($id);

        // Check if the     product exists
        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        // Return the stock value in JSON format
        return response()->json(['stock' => $product->stock]);
    }


    public function updateSold(Request $request, $id)
    {
        // Validate only the sold field
        $validated = $request->validate([
            'sold' => 'required|integer|min:0',
        ]);

        // Find the product
        $product = Product::findOrFail($id);

        // Update only the sold field
        $product->sold = $validated['sold'];
        $product->save();

        return response()->json(['message' => 'Sold attribute updated successfully.', 'product' => $product], 200);
    }

    public function updateStock(Request $request, $id)
    {
        $validated = $request->validate([
            'stock' => 'required|integer', // Validate that stock is required and must be an integer
        ]);

        // Find the product
        $product = Product::findOrFail($id);

        $product->stock = $validated['stock'];

        // Update only the stock field
        if ($product->stock <= 0) {
            $product->status = "Out of Stock";
        }
    
        $product->save();

        return response()->json(['message' => 'Stock updated successfully.', 'product' => $product], 200);
    }

    public function index()
    {
        return Product::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'brand' => 'nullable|string|max:255',
            'price' => 'nullable|numeric',
            'category' => 'nullable|string|max:255',
            'total_stock' => 'nullable|integer',
            'stock' => 'nullable|integer',
            'sold' => 'nullable|integer',
            'status' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'expDate' => 'nullable|date',
            'image' => 'nullable|image|mimes:jpg,png,jpeg|max:5120', // Validate the file input
            'on_sale' => 'nullable|in:yes,no',
            'on_sale_price' => 'nullable|numeric',
            'featured' => 'nullable|in:true,false',
        ]);

        $product = new Product($request->except('image'));

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $product->image = $path;
        }

        $product->save();

        if ($product->on_sale === 'yes') {
            $this->notifySubscribers($product);
        }

        return response()->json(['message' => 'Product added successfully', 'product' => $product], 201);
    }


    public function show($id)
    {
        $product = Product::findOrFail($id);
        return response()->json(['product' => $product], 200);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'brand' => 'nullable|string|max:255',
            'price' => 'nullable|numeric',
            'category' => 'nullable|string|max:255',
            'total_stock' => 'nullable|integer',
            'stock' => 'nullable|integer',
            'sold' => 'nullable|integer',
            'status' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'expDate' => 'nullable|date',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
            'on_sale' => 'nullable|in:yes,no',
            'on_sale_price' => 'nullable|numeric',
            'featured' => 'nullable|in:true,false',
        ]);

        $product = Product::findOrFail($id);
        $previousOnSale = $product->on_sale;

       $product->update($validated);

        if ($request->hasFile('image')) {
            // Handle image upload
            $imagePath = $request->file('image')->store('products', 'public');
            $product->image = $imagePath;
            $product->save();
        }

        if ($previousOnSale !== 'yes' && $product->on_sale === 'yes') {
            $this->notifySubscribers($product);
        }
    

        return response()->json(['product' => $product], 200);
    }

    private function notifySubscribers(Product $product)
{
    $subscribers = Subscribers::whereNotNull('email_verified_at')->pluck('email');

    foreach ($subscribers as $email) {
        Mail::send('emails.product_on_sale', ['product' => $product, 'email' => $email], function ($message) use ($email, $product) {
            $message->to($email)
                    ->subject("Product on Sale: {$product->name}");
        });   
    }
}





    //Dedicated for seniorPWD_discoubtable 
    public function updateDiscountable(Request $request, $id)
{
    // Validate only the seniorPWD_discountable field
    $validated = $request->validate([
        'seniorPWD_discountable' => 'required|in:yes,no',
    ]);

    // Find the product
    $product = Product::findOrFail($id);

    // Update only the seniorPWD_discountable field
    $product->seniorPWD_discountable = $validated['seniorPWD_discountable'];
    $product->save();

    return response()->json(['message' => 'Discountable status updated successfully.'], 200);
}

    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);
            $product->delete();

            return response()->json(['message' => 'Product deleted successfully'], 200);
        } catch (\Exception $e) {
            Log::error('Error deleting product: ' . $e->getMessage());
            return response()->json(['error' => 'Unable to delete product'], 500);
        }
    }

    public function getProductsByDate(Request $request)
    {
        Log::info('Incoming request data FOR PRODUCTS BY DATE:', $request->all());

        if (!$request->has(['start_date', 'end_date'])) {
            return response()->json(['error' => 'Start date and end date are required'], 400);
        }

        
        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();
    
        Log::info('Received PRODUCTS start_date: ' . $startDate);
        Log::info('Received PRODUCTS end_date: ' . $endDate);

        $financesByDate = Product::whereBetween('date', [$startDate, $endDate])
                        ->orderBy('date')
                        ->get();
    
        return response()->json($financesByDate);
    }

    public function exportMasterProductsXlsx(Request $request)
    {
        return Excel::download(new ExportMasterProduct, 'products_master.xlsx');
    }


    public function exportProductsPdf()
{
    $visibleColumns = ProductColumnTableVisibility::where('is_visible', true)
        ->pluck('column_Table')
        ->toArray();

    $columnMapping = [
        'productId' => 'id',
        'productImage' => 'image',
        'productName' => 'name',
        'productBrand' => 'brand',
        'productPrice' => 'price',
        'productCategory' => 'category',
        'productTotalStock' => 'total_stock',
        'productStock' => 'stock',
        'productSold' => 'sold',
        'productStatus' => 'status',
        'productExpiry' => 'expDate',
    ];

    $products = Product::all();

    $data = [
        'visibleColumns' => $visibleColumns,
        'columnMapping' => $columnMapping,
        'products' => $products,
    ];



    $pdf = \PDF::loadView('productsPdf', $data)->setPaper([0, 0, 612, 936], 'landscape'); ;
    return $pdf->download('products.pdf');
}


    public function exportProductsXlsx(Request $request)
    {
        return Excel::download(new ExportProduct, 'products.xlsx');
    }

    public function downloadTemplate(Request $request)
    {
        return Excel::download(new ExportProductTemplate, 'products_template.xlsx');
    }


    public function importProductsXlsx(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240', // Max file size: 10MB
        ]);
    
        try {
            Excel::import(new ImportProduct, $request->file('file'));
            return response()->json(['message' => 'Products imported successfully.']);
        } catch (ValidationException $e) {
            $failures = $e->failures();
            $errors = [];
    
            foreach ($failures as $failure) {
                $errors[] = "Row {$failure->row()}: {$failure->errors()[0]}";
            }
    
            return response()->json(['message' => 'Import failed', 'errors' => $errors], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Import failed: ' . $e->getMessage()], 500);
        }
    }

    public function featured_products(Request $request){
       
        $featuredProducts = Product::where('business_id', $request->business_id)
                            ->where('featured', 'true')->get();
        if ($featuredProducts->isEmpty()) {
            return response()->json(['error' => 'No featured products found'], 404);
        }
        $productsArray=[];

        foreach($featuredProducts as $product){
            $productsArray[]=[
                'product_name' => $product->name,
                'product_img' => $product->image
            ];
        }
        return response()->json($productsArray);
    }

    public function listed_products(Request $request){
       
        $listedProducts = Product::where('business_id', $request->business_id)
                            ->where('status', 'Listed')->get();

        if ($listedProducts->isEmpty()) {
            return response()->json(['error' => 'No on sale products found'], 404);
        }
        $productsArray=[];

        foreach($listedProducts as $product){
            $productsArray[]=[
                'product_name' => $product->name,
                'product_img' => $product->image,
                'product_desc' => $product->description,
                'product_price' => $product->price
            ];
        }
        return response()->json($productsArray);
    }


    public function sale_products(Request $request){
       
        $saleProducts = Product::where('business_id', $request->business_id)
                            ->where('on_sale', 'yes')->get();

        if ($saleProducts->isEmpty()) {
            return response()->json(['error' => 'No on sale products found'], 404);
        }
        $productsArray=[];

        foreach($saleProducts as $product){
            $productsArray[]=[
                'product_name' => $product->name,
                'product_img' => $product->image,
                'product_price' => $product->on_sale_price
            ];
        }
        return response()->json($productsArray);
    }

}