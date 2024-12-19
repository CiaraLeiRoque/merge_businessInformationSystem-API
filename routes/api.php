<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\InvoiceComputationController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\InvoiceItemController;
use App\Http\Controllers\InvoiceAdditionalController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductNotificationSettingsController;
use App\Http\Controllers\ProductColumnTableVisibilityController;
use App\Http\Controllers\ProductPackageController;
use App\Http\Controllers\ProductPackageNameController;
use App\Models\InvoiceAdditional;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Models\User;
use App\Models\Business;
use App\Models\Product;
use App\Models\Invoice;
use App\Models\ProductNotificationSettings;
use App\Http\Controllers\business_info_controller;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\GetIdController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WebsiteController;
use App\Http\Controllers\ImagesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatbotController;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\SubscriberController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/all-users', function(){
    return User::all();
});

Route::get('/all-business', function(){
    return Business::all();
});

Route::get('/all-invoices', function(){
    return Invoice::all();
});


Route::get('productPackageNamesWithProducts', [ProductPackageNameController::class, 'getAllPackageNamesWithProducts']);


Route::get('productPackageName', [ProductPackageNameController::class, 'show']);  
Route::post('productPackageName', [ProductPackageNameController::class, 'savePackageName']);  


Route::delete('productPackageName/{id}', [ProductPackageNameController::class, 'deletePackage']);  
Route::post('productPackageName/{id}', [ProductPackageNameController::class, 'updatePackageName']);  
Route::get('productPackageName/{id}', [ProductPackageNameController::class, 'showPackageName']);  

Route::get('productPackage', [ProductPackageController::class, 'show']);  
Route::post('productPackage', [ProductPackageController::class, 'savePackage']);  

Route::get('productPackage/{id}', [ProductPackageController::class, 'showProductPackage']);  
Route::delete('productPackage/{id}', [ProductPackageController::class, 'deleteProductPackage']);  
Route::post('productPackage/{id}', [ProductPackageController::class, 'updateProductPackage']);  


Route::get('productVis', [ProductColumnTableVisibilityController::class, 'show']);  
Route::put('productVis', [ProductColumnTableVisibilityController::class, 'updateVisibility']);

Route::get('/productNotif', [ProductNotificationSettingsController::class, 'show']);
Route::put('/productNotif', [ProductNotificationSettingsController::class, 'updateCounts']);

Route::patch('/products/{id}/sold', [ProductController::class, 'updateSold']);
Route::patch('/products/{id}/stock', [ProductController::class, 'updateStock']);
Route::get('/products/{id}/stock', [ProductController::class, 'getStock']);


Route::get('/business', [BusinessController::class, 'show']);
Route::get('/business_info', [BusinessController::class, 'index']);
Route::get('/business_info', [BusinessController::class, 'showBusiness']);
Route::post('/website', [WebsiteController::class, 'store']);
Route::get('/website', [WebsiteController::class, 'info']);
Route::post('/website-update', [WebsiteController::class, 'update']);

Route::get('/images', [ImagesController::class, 'info']);
Route::post('/images', [ImagesController::class, 'store']);
Route::post('/images-update', [ImagesController::class, 'update']);

Route::get('/products', [ProductController::class, 'index']);
Route::post('/products', [ProductController::class, 'store']);
Route::put('/products/{id}', [ProductController::class, 'update']);
Route::delete('/products/{id}', [ProductController::class, 'destroy']);
Route::get('/featured-products', [ProductController::class, 'featured_products'])->name('featured_products');
Route::get('/listed-products', [ProductController::class, 'listed_products'])->name('listed_products');
Route::get('/sale-products', [ProductController::class, 'sale_products'])->name('sale_products');

Route::get('/Business', [BusinessController::class, 'show']);
Route::post('/Business', [BusinessController::class, 'store']);
Route::put('/Business/{id}', [BusinessController::class, 'update']);
Route::delete('/Business/{id}', [BusinessController::class, 'destroy']);

Route::get('/chatbot-response', [ChatbotController::class, 'index']);
Route::post('/chatbot-response', [ChatbotController::class, 'store']);
Route::put('/chatbot-response/{id}', [ChatbotController::class, 'update']);
Route::delete('/chatbot-response/{id}', [ChatbotController::class, 'destroy']);

Route::post('/subscribe', [SubscriberController::class, 'store']);
Route::get('/verify-subscription', [SubscriberController::class, 'verify'])->name('verify-subscription');


Route::get('/featured-products', [ProductController::class, 'featured_products'])->name('featured_products');

//Dedicated for seniorPWD_discountable
Route::put('/products/{id}/discountable', [ProductController::class, 'updateDiscountable']);


Route::post('/business_info', [BusinessController::class, 'store']);
Route::put('/business_info/{id}', [BusinessController::class, 'update']);
Route::delete('/business_info/{id}', [BusinessController::class, 'destroy']);

Route::get('/backup', function () {
    Artisan::call('db:export');
    return response()->download(storage_path('app/backups/backup.sql'))->deleteFileAfterSend(true);
});

Route::post('/import', [ImportController::class, 'import']);

/*Route::get('/categories', [CategoryController::class, 'index']);
Route::post('/categories', [CategoryController::class, 'store']);
Route::put('/categories/{category}', [CategoryController::class, 'update']);
Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);*/


Route::get('finance', [FinanceController::class, 'index']);
Route::post('finance', [FinanceController::class, 'store']);
Route::post('/finance/{id}', [FinanceController::class, 'update']);
Route::delete('/finance/{id}', [FinanceController::class, 'destroy']);
Route::get('finance_by_date', [FinanceController::class, 'getFinanceByDate']);

Route::post('finance_category', [FinanceController::class, 'storeCategory']);
Route::delete('/finance_category/{id}', [FinanceController::class, 'destroyCategory']);

Route::get('/featured-products', [ProductController::class, 'featured_products'])->name('featured_products');
Route::get('/listed-products', [ProductController::class, 'listed_products'])->name('listed_products');

Route::get('/Business', [BusinessController::class, 'show']);
Route::post('/Business', [BusinessController::class, 'store']);
Route::put('/Business/{id}', [BusinessController::class, 'update']);
Route::delete('/Business/{id}', [BusinessController::class, 'destroy']);
Route::get('/business_info', [BusinessController::class, 'showBusiness']);


Route::apiResource('categories', CategoryController::class);

Route::get('/invoice', [InvoiceController::class, 'index']);

Route::get('/invoice', [InvoiceController::class, 'index']);
Route::post('/invoice', [InvoiceController::class, 'store']);
Route::delete('/invoice/{invoice_system_id}', [InvoiceController::class, 'destroy']);
Route::get('/invoice/{invoice_system_id}', [InvoiceController::class, 'show']);

Route::get('invoice_item',[InvoiceItemController::class,'index']);
Route::post('invoice_item',[InvoiceItemController::class,'store']);
Route::get('/invoice_item/{invoice_system_id}', [InvoiceItemController::class, 'show']);

Route::get('invoice_additional',[InvoiceAdditionalController::class,'index']);
Route::post('invoice_additional',[InvoiceAdditionalController::class,'store']);
Route::get('/invoice_additional/{invoice_system_id}', [InvoiceAdditionalController::class, 'show']);


Route::get('invoice_computation',[InvoiceComputationController::class,'index']);
Route::post('invoice_computation',[InvoiceComputationController::class,'store']);
Route::get('/invoice_computation/{invoice_system_id}',[InvoiceComputationController::class,'show']);
Route::get('/invoice_computation/{invoice_system_id}',[InvoiceComputationController::class,'show']);



Route::get('invoice_print/{invoice_id}', [InvoiceController::class, 'invoice_print']);


Route::post('/invoice/{invoice_system_id}', [InvoiceController::class, 'update']);
Route::post('/invoice_item/{invoice_system_id}', [InvoiceItemController::class, 'updateInvoiceItems']);
Route::delete('/invoice_item/{invoice_system_id}', [InvoiceItemController::class, 'deleteInvoiceItems']);

Route::post('/invoice_additional/{invoice_system_id}', [InvoiceAdditionalController::class,'updateInvoiceAdditionals']);
Route::delete('/invoice_additional/{invoice_system_id}', [InvoiceAdditionalController::class,'deleteInvoiceAdditionals']);

Route::post('/invoice_computation/{invoice_system_id}', [InvoiceComputationController::class,'updateInvoiceComputation']);
Route::delete('/invoice_computation/{invoice_system_id}', [InvoiceComputationController::class,'deleteInvoiceComputation']);
// Route::post('/invoice_item/{invoice_system_id}', [InvoiceController::class, 'updateInvoiceItems']);

Route::get('/invoices/export', [InvoiceController::class, 'export']);
Route::get('invoice_by_date', [InvoiceController::class, 'getInvoiceByDate']);


Route::get('print/summary/invoice_by_date/pdf', [InvoiceController::class, 'printInvoiceByDate']);
Route::get('print/summary/invoice_by_date/xlsx', [InvoiceController::class, 'printInvoiceByDateExcel']);


Route::get('print/summary/finance_by_date_category/pdf', [FinanceController::class, 'printFinanceByDatePdf']);
Route::get('print/summary/finance_by_date_category/xlsx', [FinanceController::class, 'exportFinanceByDateExcel']);

Route::get('products/print/pdf', [FinanceController::class, 'printProductsPdf']);

Route::post('products/import/xlsx', [ProductController::class, 'importProductsXlsx']);


Route::get('products/print/master/export/xlsx', [ProductController::class, 'exportMasterProductsXlsx']);

Route::get('products/print/export/pdf', [ProductController::class, 'exportProductsPdf']);
Route::get('products/print/export/xlsx', [ProductController::class, 'exportProductsXlsx']);

Route::get('products/print/export/template_xslx', [ProductController::class, 'downloadTemplate']);

Route::get('/export-products-pdf', [ProductController::class, 'exportCriticalStockPdf']);