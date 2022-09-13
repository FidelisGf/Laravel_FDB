<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\ProductController;
use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/allByCategory/{id}', [CategoryController::class, 'findAllProductByCategory']);
Route::post('/findByInputedValue', [CategoryController::class, 'findProductFromCategoryWithInputedValue']);


Route::post('/search', [ProductController::class, 'search']);
Route::post('/filterBy', [ProductController::class, 'filters']);



Route::get('/allProductsByEmpresa/{id}', [EmpresaController::class, 'allProductsFromEmpresa']);
Route::get('/countCategorysFromEmpresa/{id}', [EmpresaController::class, 'countCategorysFromEmpresa']);
Route::get('/allCategoryFromEmpresa/{id}', [EmpresaController::class, 'allCategoryFromEmpresa']);


Route::resource('products', 'ProductController');
Route::resource('empresas', 'EmpresaController');
Route::resource('categorys', 'CategoryController');
