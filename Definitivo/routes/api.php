<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\DespesaController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\EstoqueController;
use App\Http\Controllers\MateriaisController;
use App\Http\Controllers\MedidasController;
use App\Http\Controllers\PedidosController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ResetPwController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\VendaController;
use App\Http\Middleware\FuncMiddleware;
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
    Route::get('/showAvalibleRoles', [UsuarioController::class, 'showAvalibleRoles']);
    Route::get('/logout', [AuthController::class, 'logout']);
    Route::post('sendMailResetPassword', [ResetPwController::class, 'sendResetPwEmail']);
    Route::post('resetPassword', [ResetPwController::class, 'resetPassword']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('profile', [AuthController::class, 'profile']);
    Route::get('validateTkn', [AuthController::class, 'getAuthenticatedUser']);
});

Route::group(['middleware' => ['jwt.verify']], function() {

    //Ações que o funcionário comum pode realizar no sistema

    Route::get('refresh', [AuthController::class, 'refresh']);
    Route::get('getQuantidadeProduct/{id}', [EstoqueController::class, 'getQuantidadeProduct']);
    Route::get('/findCategoryWithProductsIn', [CategoryController::class, 'findCategoryWithProductsIn']);

    Route::get('pedidoPorData', [PedidosController::class, 'pedidosPorPeriodo']);
    Route::get('countProducts', [ProductController::class, 'countProducts']);
    Route::post('/search', [ProductController::class, 'search']);
    Route::get('/allByCategory/{id}', [ProductController::class, 'findAllProductByCategory']);
    Route::get('/checaEmpUser', [UsuarioController::class, 'checkIfUserHasEmpresa'] );
    Route::get('/getEmpresaFromUser', [EmpresaController::class, 'getEmpresaFromUser']);
    Route::get('/profile', [UsuarioController::class, 'profile']);
    Route::get('despesasByTag/{id}', [DespesaController::class, 'despesasByTag']);
    Route::get('sumDespesasMensais', [DespesaController::class, 'sumDespesasMensais']);
    Route::post('checkQuantidadeProduto', [PedidosController::class, 'checkQuantidadeProduto']);
    Route::get('findLucroByProduto/{id}', [ProductController::class, 'findLucroByProduto']);
    Route::put('adicionaQuantidadeMaterial/{id}', [MateriaisController::class, 'adicionaQuantidadeMaterial']);



    //Ações que Admin's e gerentes podem executar no sistema

    Route::post('/vincularUserEmpresa', [UsuarioController::class, 'vinculaUsuarioEmpresa'])->middleware(FuncMiddleware::class);
    Route::get('/empresaPorUsuario', [UsuarioController::class, 'getEmpresaByUser'])->middleware(FuncMiddleware::class);
    Route::get('/getVendasPorDia', [VendaController::class, 'getVendasPorDia'])->middleware(FuncMiddleware::class);
    Route::get("getLucroAndGastos", [VendaController::class, 'getLucroAndGastos'])->middleware(FuncMiddleware::class);
    Route::get('/getTotalVendasUltimosTresMeses', [VendaController::class, 'getTotalVendasInTheLastThreeMonths'])->middleware(FuncMiddleware::class);
    Route::post('/addEstoque', [EstoqueController::class, 'addEstoque'])->middleware(FuncMiddleware::class);
    Route::get('/pedidos/{id}', [PedidosController::class, 'show'])->middleware(FuncMiddleware::class);
    Route::put('/pedidos/{id}', [PedidosController::class, 'update'])->middleware(FuncMiddleware::class);
    Route::delete('/products/{id}', [ProductController::class, 'destroy'])->middleware(FuncMiddleware::class);
    Route::put('/products/{id}', [ProductController::class, 'update'])->middleware(FuncMiddleware::class);
    Route::put('aprovarPedido/{id}', [PedidosController::class, 'aprovarPedido'])->middleware(FuncMiddleware::class);

    //Resources

    Route::resource('materiais', 'MateriaisController');
    Route::resource('medidas', 'MedidasController');
    Route::resource('despesas', 'DespesaController');
    Route::resource('tags', 'TagController');
    Route::resource('clientes', 'ClienteController');
    Route::resource('vendas', 'VendaController')->middleware(FuncMiddleware::class);
    Route::resource('products', 'ProductController')->except(['destroy']);
    Route::resource('pedidos', 'PedidosController')->except(['show', 'update']);
    Route::resource('empresas', 'EmpresaController');
    Route::resource('categorys', 'CategoryController');
    Route::resource('estoques', 'EstoqueController');
});




