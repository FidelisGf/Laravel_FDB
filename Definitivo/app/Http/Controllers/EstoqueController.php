<?php

namespace App\Http\Controllers;

use App\Estoque;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class EstoqueController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $user = JWTAuth::parseToken()->authenticate();
            $empresa = $user->empresa;
            $PRODUCTS = Estoque::where('EMPRESA_ID', '=', $empresa->ID)->with([
                'product' => function($query){
                    $query->select('ID_PRODUTO', 'NOME', 'VALOR', 'DESC');
                }
            ])->paginate(5);
            return response()->json([$PRODUCTS]);
        }catch(\Exception $e){
            return response()->json(['message' => $e]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeProdutoInEstoque($product_id, $quantidade)
    {
        try{
            $user = JWTAuth::parseToken()->authenticate();
            $empresa = $user->empresa;
            $Estoque = new Estoque();
            $Estoque->PRODUCT_ID = $product_id;
            $Estoque->EMPRESA_ID = $empresa->ID;
            $Estoque->QUANTIDADE = $quantidade;
            if($Estoque->save()){
                return response()->json(['message' => 'Produto criado e adicionado ao estoque da empresa'],200);
            }
        }catch(\Exception $e){
            return response()->json([
                "message" => $e->getMessage()
            ]);
        }
    }

    public function addEstoque(Request $request){
        try{
            $product_id = $request->product_id;
            $quantidade = $request->quantidade;
            $user = JWTAuth::parseToken()->authenticate();
            $empresa = $user->empresa;
            $Estoque = Estoque::where('EMPRESA_ID', '=', $empresa->ID)->where('PRODUCT_ID', '=', $product_id)->first();
            $Estoque->QUANTIDADE += $quantidade;
            $Estoque->save();
            return response()->json(['Produto atual no estoque' => $Estoque]);
        }catch(\Exception $e){
            return response()->json([
                "message" => $e->getMessage()
            ]);
        }
    }
    public function removeEstoque($product_id, $quantidade){
        try{
            $user = JWTAuth::parseToken()->authenticate();
            $empresa = $user->empresa;
            $Estoque = Estoque::where('EMPRESA_ID', '=', $empresa->ID)->where('PRODUCT_ID', '=', $product_id)->first();
            $Estoque->QUANTIDADE -= $quantidade;
            $Estoque->SAIDAS += $quantidade;
            $Estoque->save();
            return response()->json(['Produto atual no estoque' => $Estoque]);
        }catch(\Exception $e){
            return response()->json([
                "message" => $e->getMessage()
            ]);
        }
    }
    public function filterByBiggerEstoque(){
        try{
            $user = JWTAuth::parseToken()->authenticate();
            $empresa = $user->empresa;
            $PRODUCTS = Estoque::where('EMPRESA_ID', '=', $empresa->ID)->orderBy('QUANTIDADE', 'desc')->with([
                'product' => function($query){
                    $query->select('ID_PRODUTO', 'NOME', 'VALOR', 'DESC');
                }
            ])->paginate(5);
            return $PRODUCTS;
        }catch(\Exception $e){
            return response()->json(['message' => $e]);
        }
    }
    public function filterByLowerEstoque(){
        try{
            $user = JWTAuth::parseToken()->authenticate();
            $empresa = $user->empresa;
            $PRODUCTS = Estoque::where('EMPRESA_ID', '=', $empresa->ID)->orderBy('QUANTIDADE', 'asc')->with([
                'product' => function($query){
                    $query->select('ID_PRODUTO', 'NOME', 'VALOR', 'DESC');
                }
            ])->paginate(5);
            return $PRODUCTS;
        }catch(\Exception $e){
            return response()->json(['message' => $e]);
        }
    }
    public function filters(Request $request){
        $opcao = $request->opcao;
        switch($opcao){
            case "Produtos com mais estoque":
                return $this->filterByBiggerEstoque();
                break;
            case "Produtos com pouco estoque":
                return $this->filterByLowerEstoque();
                break;
        }
    }
    public function getQuantidadeProduct($id){
        try{
            $estoque = Estoque::where('PRODUCT_ID', $id)->first()->only('QUANTIDADE');
            return $estoque;
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()]);
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

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
