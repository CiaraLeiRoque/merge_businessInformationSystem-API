<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\InvoiceComputationController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\InvoiceItemController;
use App\Http\Controllers\InvoiceAdditionalController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Log;

use App\Models\User;
use App\Models\Business;
use App\Models\Product;
use App\Models\Invoice;
use App\Http\Controllers\business_info_controller;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\csrfController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::get('/products', [ProductController::class, 'index']);
Route::post('/products', [ProductController::class, 'store']);
Route::get('products/{id}', [ProductController::class, 'show']);
Route::put('/products/{id}', [ProductController::class, 'update']);
Route::delete('/products/{id}', [ProductController::class, 'destroy']);
//Dedicated for seniorPWD_discoubtable
Route::put('/products/{id}/discountable', [ProductController::class, 'updateDiscountable']);

Route::get('/business_info', [BusinessController::class, 'show']);
Route::post('/business_info', [BusinessController::class, 'store']);
Route::put('/business_info/{id}', [BusinessController::class, 'update']);
Route::delete('/business_info/{id}', [BusinessController::class, 'destroy']);

Route::get('/invoice', [InvoiceController::class, 'index']);
Route::post('/invoice', [InvoiceController::class, 'store']);
Route::post('/invoice/{invoice_system_id}', [InvoiceController::class, 'update']);
Route::delete('/invoice/{invoice_system_id}', [InvoiceController::class, 'destroy']);
Route::get('/invoice/{invoice_system_id}', [InvoiceController::class, 'show']);

Route::get('invoice_item',[InvoiceItemController::class,'index']);
Route::post('invoice_item',[InvoiceItemController::class,'store']);
Route::get('/invoice_item/{invoice_system_id}', [InvoiceItemController::class, 'show']);

Route::get('invoice_additional',[InvoiceAdditionalController::class,'index']);
Route::post('invoice_additional',[InvoiceAdditionalController::class,'store']);
Route::get('/invoice_additional/{invoice_system_id}', [InvoiceAdditionalController::class, 'show']);

Route::post('invoice_computation',[InvoiceComputationController::class,'store']);
Route::get('/invoice_computation/{invoice_system_id}',[InvoiceComputationController::class,'show']);

Route::get('invoice_print/{invoice_id}', [InvoiceController::class, 'invoice_print']);

/*Route::get('/categories', [CategoryController::class, 'index']);
Route::post('/categories', [CategoryController::class, 'store']);
Route::put('/categories/{category}', [CategoryController::class, 'update']);
Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);*/

Route::get('finance', [FinanceController::class, 'index']);
Route::post('finance', [FinanceController::class, 'store']);
Route::put('/finance/{id}', [FinanceController::class, 'update']);
Route::put('/finance/{id}', [FinanceController::class, 'destroy']);

Route::post('finance_category', [FinanceController::class, 'storeCategory']);
Route::delete('/finance_category/{id}', [FinanceController::class, 'destroyCategory']);


Route::apiResource('categories', CategoryController::class);

