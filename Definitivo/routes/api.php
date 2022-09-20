<?php

use App\Http\Controllers\AuthController;
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
Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {

    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', 'AuthController@refresh');
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::get('profile', [AuthController::class, 'profile']);
    Route::get('profile', [AuthController::class, 'validateTokn']);
});



Route::get('/allByCategory/{id}', [CategoryController::class, 'findAllProductByCategory']);
Route::get('/findCategoryWithProductsIn', [CategoryController::class, 'findCategoryWithProductsIn']);
Route::get('/mostExpansiveProduct/{id}', [CategoryController::class, 'CategoryMostExpansiveProduct']);
Route::get('/avgFromCategorysProducts/{id}', [CategoryController::class, 'CategoryAVGProductPrice']);
Route::get('minFromCategorysProducts/{id}', [CategoryController::class, 'CategoryMinProductPrice']);

Route::get('/searchEmp', [EmpresaController::class, 'applyFilter']);
Route::post('/search', [ProductController::class, 'search']);
Route::post('/filterBy', [ProductController::class, 'filters']);


Route::get('/autoCompleteEmpresa' ,[EmpresaController::class, 'autoCompleteEmpresa']);
Route::get('/allProductsByEmpresa/{id}', [EmpresaController::class, 'allProductsFromEmpresa']);
Route::get('/countCategorysFromEmpresa/{id}', [EmpresaController::class, 'countCategorysFromEmpresa']);
Route::get('/allCategoryFromEmpresa/{id}', [EmpresaController::class, 'allCategoryFromEmpresa']);


Route::resource('empresas', 'EmpresaController');
Route::resource('products', 'ProductController');
Route::resource('categorys', 'CategoryController');

