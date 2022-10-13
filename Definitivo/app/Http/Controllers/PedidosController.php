<?php

namespace App\Http\Controllers;

use App\Pedidos;
use Illuminate\Http\Request;
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
        $nomesProdutos = '';
        $valor_total = 0;
        $metodo_pagamento = $request->METODO_PAGAMENTO;
        $user = JWTAuth::parseToken()->authenticate();
        $empresa = $user->empresa;
        $pedido = new Pedidos();
        $estoque = new EstoqueController();
        $produtos = $request->produtos;
        foreach($produtos as $produto){
            $estoque->removeEstoque($produto->id, $produto->quantidade);
            $nomesProdutos += "$produto->quantidade | $produto->nome, ";
            $valor_total += $produto->valor;
        }
        $pedido->PRODUTOS = $nomesProdutos;
        $pedido->METODO_PAGAMENTO = $metodo_pagamento;
        $pedido->ID_EMPRESA = $empresa->ID;
        $pedido->VALOR_TOTAL = $valor_total;
        $pedido->APROVADO = $request->aprovado;
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
