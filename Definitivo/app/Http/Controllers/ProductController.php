<?php

namespace App\Http\Controllers;

use App\Category;
use App\Empresa;
use App\Estoque;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductResource;
use App\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\EstoqueController;
use App\Http\Requests\ProductRequest;
use App\Materiais;
use App\Repositories\ProductRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;

class ProductController extends Controller
{
    public function index(ProductRepository $productRepository, Request $request)
    {
        return $productRepository->listAll($request);
    }

    public function findAllProductByCategory($id, ProductRepository $productRepository){
        return $productRepository->findAllProductByCategory($id);
    }

    public function store(Request $request, ProductRepository $productRepository)
    {
        return $productRepository->store($request);
    }

    public function show($id, ProductRepository $productRepository)
    {
       return $productRepository->show($id);
    }

    public function update(Request $request, $id, ProductRepository $productRepository)
    {
        return $productRepository->update($request, $id);
    }

    public function destroy($id, ProductRepository $productRepository)
    {
        return $productRepository->destroy($id);
    }

    public function findLucroByProduto($id, ProductRepository $productRepository){
        return $productRepository->findLucroByProduto($id);
    }

    public function search(Request $request, ProductRepository $productRepository){
        return $productRepository->search($request);
    }

    public function descontaQuantidadeMaterial($id, $quantidade, ProductRepository $productRepository){
        return $productRepository->descontaQuantidadeMaterial($id, $quantidade);
    }
    public function countProducts(ProductRepository $productRepository){
        return $productRepository->countProducts();
    }
}
