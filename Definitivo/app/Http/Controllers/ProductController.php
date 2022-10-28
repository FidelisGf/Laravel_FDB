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
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try{
            if($request->filled('opcao')){
                $op = $request->opcao;
                switch ($op){
                    case "Produtos mais baratos":
                        return $this->filterByLowestValue();
                        break;
                    case "Produtos mais caros":
                        return $this->filterByExpansiveValue();
                        break;
                }
            }else{
                $user = auth()->user();
                $empresa = $user->empresa;
                $PRODUCTS = DB::table('EMPRESAS')->where('EMPRESAS.ID', $empresa->ID)->join('ESTOQUES', 'ESTOQUES.EMPRESA_ID', '=', 'EMPRESAS.ID')->
                join('PRODUCTS', 'PRODUCTS.ID', '=', 'ESTOQUES.PRODUCT_ID')->join('CATEGORIAS', 'CATEGORIAS.ID_CATEGORIA', '=', 'PRODUCTS.ID_CATEGORIA')->select(
                    'CATEGORIAS.ID_CATEGORIA', 'CATEGORIAS.NOME_C',  'PRODUCTS.ID', 'PRODUCTS.NOME', 'PRODUCTS.VALOR', 'ESTOQUES.QUANTIDADE'
                )->paginate(8);
                return $PRODUCTS;
            }
        }catch(\Exception $e){
            return response()->json(
                [
                    "message" => $e->getMessage()
                ],400
            );
        }

    }
    public function findAllProductByCategory($id){
        try{
            $user = auth()->user();
            $empresa = $user->empresa;
            $PRODUCTS = DB::table('EMPRESAS')->where('EMPRESAS.ID', $empresa->ID)->join('ESTOQUES', 'ESTOQUES.EMPRESA_ID', '=', 'EMPRESAS.ID')->
            join('PRODUCTS', 'PRODUCTS.ID', '=', 'ESTOQUES.PRODUCT_ID')->join('CATEGORIAS', 'CATEGORIAS.ID_CATEGORIA', '=', 'PRODUCTS.ID_CATEGORIA')->select(
                'CATEGORIAS.ID_CATEGORIA', 'CATEGORIAS.NOME_C',  'PRODUCTS.ID', 'PRODUCTS.NOME', 'PRODUCTS.VALOR', 'ESTOQUES.QUANTIDADE'
            )->where('CATEGORIAS.ID_CATEGORIA', $id)->paginate(8);
            return $PRODUCTS;
        }catch(\Exception $e){
            return response()->json(
                [
                    "message" => $e->getMessage()
                ]
            );
        }
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try{
            $helper = new Help();
            $helper->startTransaction();
            $validatedData = $request->validate([
                'NOME' => ['required', 'unique:PRODUCTS', 'max:60', 'min:2'],
                'DESC' => ['required', 'max:120', 'min:4'],
                'VALOR'=> ['required', 'min:0'],
                'ID_CATEGORIA' => ['required'],
                'quantidade_inicial' => ['required', 'min:0']
            ]);
            if($validatedData){
                $produto = Product::create($request->all());

                if($produto){
                    $quantidade = $request->quantidade_inicial;
                    $estoque = new EstoqueController();
                    $estoque->storeProdutoInEstoque($produto->ID, $quantidade);
                    $helper->commit();
                    return response()->json(
                        $produto
                    );
                }
            }
        }catch(\Exception $e){
            $helper->rollbackTransaction();
            return response()->json(
                [
                    "message" => $e->getMessage()
                ],400
            );
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try{
            $user = auth()->user();
            $PRODUCTS = Product::FindOrFail($id)->with(['category' => function($query){
                $query->select('ID_CATEGORIA', 'NOME_C');
                }
            ])->first();
            return $PRODUCTS;
        }catch(\Exception $e){
            return response()->json(
                $e->getMessage(), 400
            );
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        try{
            $PRODUCT = Product::FindOrFail($id);
            $PRODUCT->update($request->all());
            return response()->json(
                [
                    "message" => "Produto editado com sucesso"
                ],200
            );
        }catch(\Exception $e){
            return response()->json(
                [
                    "message" => $e->getMessage()
                ],400
                );
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
            $user = auth()->user();
            $estoque = Estoque::where('EMPRESA_ID', $user->empresa->ID)->where('PRODUCT_ID', $id);
            $estoque->delete();
            $PRODUCT = Product::where('ID',$id)->first();
            $PRODUCT->delete();
            return response()->json(
                [
                    "message" => "Produto excluido com sucesso !"
                ],200
                );
        }catch(\Exception $e){
            return response()->json(
                [
                    "message" => $e->getMessage()
                ],400
                );
        }
    }

    public function filterByLowestValue(){
        try{
            $user = auth()->user();
            $empresa = $user->empresa;
            $PRODUCTS = Empresa::FindOrFail($empresa->ID)->product()->with([
                'category' => function($query){
                    $query->select('ID_CATEGORIA', 'NOME_C');
                }
            ])->orderBy('PRODUCTS.VALOR', 'asc')->paginate(6);
            return $PRODUCTS;
        }catch(\Exception $e){
            return response()->json(
                [
                    "message" => $e->getMessage()
                ],400
                );
        }
    }

    public function filterByExpansiveValue(){
        try{
            $user = auth()->user();
            $empresa = $user->empresa;
            $PRODUCTS = Empresa::FindOrFail($empresa->ID)->product()->with([
                'category' => function($query){
                    $query->select('ID_CATEGORIA', 'NOME_C');
                }
            ])->orderBy('PRODUCTS.VALOR', 'desc')->paginate(6);
            return $PRODUCTS;
        }catch(\Exception $e){
            return response()->json(
                [
                    "message" => $e->getMessage()
                ],400
            );
        }
    }
    //%{$searchTerm}%
    public function search(Request $request){
        try{
            $search = $request->search;
            $search = ucwords($search);
            $PRODUCTS = Product::query()->where('NOME', 'LIKE', '%'. $search.'%')
                                ->orWhere('DESC', 'LIKE', '%'.$search.'%')->paginate(15);
            return ProductResource::collection($PRODUCTS);
        }catch(\Exception $e){
            return response()->json(
                [
                    "message" => $e->getMessage()
                ],400
                );
        }
    }
    //ucwords($bar);

    public function filters(Request $request){
        $op = $request->opcao;
        switch ($op){
            case "Produtos mais baratos":
                return $this->filterByLowestValue();
                break;

            case "Produtos mais caros":
                return $this->filterByExpansiveValue();
                break;
        }
    }

}
