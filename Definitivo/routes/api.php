<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\EstoqueController;
use App\Http\Controllers\PedidosController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UsuarioController;
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
    Route::post('profile', [AuthController::class, 'profile']);
    Route::get('validateTkn', [AuthController::class, 'getAuthenticatedUser']);
});

Route::group(['middleware' => ['jwt.verify']], function() {

    Route::get('getQuantidadeProduct/{id}', [EstoqueController::class, 'getQuantidadeProduct']);
    Route::get('/allByCategory/{id}', [ProductController::class, 'findAllProductByCategory']);
    Route::get('/findCategoryWithProductsIn', [CategoryController::class, 'findCategoryWithProductsIn']);
    Route::get('/mostExpansiveProduct/{id}', [CategoryController::class, 'CategoryMostExpansiveProduct']);
    Route::get('/avgFromCategorysProducts/{id}', [CategoryController::class, 'CategoryAVGProductPrice']);
    Route::get('minFromCategorysProducts/{id}', [CategoryController::class, 'CategoryMinProductPrice']);


    Route::get('/searchEmp', [EmpresaController::class, 'applyFilter']);
    Route::post('/search', [ProductController::class, 'search']);
    Route::get('/filterBy', [ProductController::class, 'filters']);

    Route::get('/autoCompleteEmpresa' ,[EmpresaController::class, 'autoCompleteEmpresa']);
    Route::get('/allProductsByEmpresa/{id}', [EmpresaController::class, 'allProductsFromEmpresa']);
    Route::get('/countCategorysFromEmpresa/{id}', [EmpresaController::class, 'countCategorysFromEmpresa']);
    Route::get('/allCategoryFromEmpresa/{id}', [EmpresaController::class, 'allCategoryFromEmpresa']);

    Route::get('/checaEmpUser', [UsuarioController::class, 'checkIfUserHasEmpresa'] );
    Route::post('/vincularUserEmpresa', [UsuarioController::class, 'vinculaUsuarioEmpresa'] );
    Route::get('/empresaPorUsuario', [UsuarioController::class, 'getEmpresaByUser'] );

    Route::post('/addEstoque', [EstoqueController::class, 'addEstoque']);
    Route::get('/filterEstoque', [EstoqueController::class, 'filters']);
    Route::resource('products', 'ProductController');
    Route::resource('pedidos', 'PedidosController');
    Route::resource('empresas', 'EmpresaController');
    Route::resource('categorys', 'CategoryController');
    Route::resource('estoques', 'EstoqueController');
});



