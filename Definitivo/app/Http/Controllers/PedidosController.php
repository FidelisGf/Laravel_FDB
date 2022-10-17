<?php

namespace App\Http\Controllers;

use App\FakeProduct;
use App\Http\Resources\FakeProduct as ResourcesFakeProduct;
use App\Http\Resources\ProductResource;
use App\Pedidos;
use App\Product;
use Illuminate\Http\Request;
use League\CommonMark\Util\ArrayCollection;
use Tymon\JWTAuth\Facades\JWTAuth;

class PedidosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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

            $validatedData = $request->validate([
                'METODO_PAGAMENTO' => ['required'],
                'produtos' => ['required'],
                'valor_total'=> ['required', 'min:0.01'],
                'aprovado' => ['required'],
            ]);
            if($validatedData){
                    $metodo_pagamento = $request->METODO_PAGAMENTO;
                    $user = JWTAuth::parseToken()->authenticate();
                    $empresa = $user->empresa;
                    $pedido = new Pedidos();
                    $estoque = new EstoqueController();
                    $FakeProducts = collect(new FakeProduct());
                    $PRODUCTS = collect(new FakeProduct());
                    foreach($request->produtos as $produto){
                        $FakeProduct = new ResourcesFakeProduct((object) $produto);
                        $FakeProducts->push($FakeProduct);
                    }
                    foreach($FakeProducts as $produto){
                        $PRODUCTS->push($produto);
                        $estoque->removeEstoque($produto->id, $produto->quantidade);
                    }
                    $pedido->PRODUTOS = $PRODUCTS;
                    $pedido->METODO_PAGAMENTO = $metodo_pagamento;
                    $pedido->ID_EMPRESA = $empresa->ID;
                    $pedido->VALOR_TOTAL = $request->valor_total;
                    $pedido->APROVADO = $request->aprovado;
                    $helper->startTransaction();
                    $pedido->save();
                    $helper->commit();
                    return $pedido;
            }
        }catch(\Exception $e){
            $helper->rollbackTransaction();
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
        //
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
