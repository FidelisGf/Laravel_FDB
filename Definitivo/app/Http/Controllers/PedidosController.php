<?php

namespace App\Http\Controllers;

use App\FakeProduct;
use App\Http\Resources\FakeProduct as ResourcesFakeProduct;
use App\Http\Resources\ProductResource;
use App\Pedidos;
use App\Product;
use Carbon\Carbon;
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
    public function index(Request $request)
    {
        try{
            if($request->filled('opcao')){
                $opcao = $request->opcao;
                switch($opcao){
                    case 'Pedidos realizados entre duas datas' :
                        return $this->pedidosPorPeriodo($request);
                        break;
                }
            }else{
                $user = JWTAuth::parseToken()->authenticate();
                $empresa = $user->empresa;
                $Pedidos = Pedidos::where('ID_EMPRESA', $empresa->ID)->paginate(6);
                return $Pedidos;
            }
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()]);
        }
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
                'aprovado' => ['required'],
            ]);
            if($validatedData){
                    $metodo_pagamento = $request->METODO_PAGAMENTO;
                    $user = JWTAuth::parseToken()->authenticate();
                    $empresa = $user->empresa;
                    $vlTotal = 0;
                    $pedido = new Pedidos();
                    $estoque = new EstoqueController();
                    $FakeProducts = collect(new FakeProduct());
                    $PRODUCTS = collect(new FakeProduct());
                    foreach($request->produtos as $produto){
                        $FakeProduct = new ResourcesFakeProduct((object) $produto);
                        $FakeProducts->push($FakeProduct);
                    }
                    foreach($FakeProducts as $produto){
                        $vlTotal += $produto->valor;
                        $PRODUCTS->push($produto);
                        $estoque->removeEstoque($produto->id, $produto->quantidade);
                    }
                    $pedido->PRODUTOS = json_encode($PRODUCTS); // manda em formato de json para o banco
                    $pedido->METODO_PAGAMENTO = $metodo_pagamento;
                    $pedido->ID_EMPRESA = $empresa->ID;
                    $pedido->VALOR_TOTAL = $vlTotal;
                    $pedido->APROVADO = "$request->aprovado";
                    if($pedido->APROVADO == 'T'){
                        $pedido->DT_PAGAMENTO = now()->format('Y-m-d H:i');
                    }
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
    public function aprovarPedido($id){
        $helper = new Help();
        try{
            $Pedido = Pedidos::where('ID', $id)->firstOrFail();
            $Pedido->APROVADO = 'T';
            $Pedido->DT_PAGAMENTO = now()->format('Y-m-d H:i');
            $helper->startTransaction();
            $Pedido->save();
            $helper->commit();
            return $Pedido;
        }catch(\Exception $e){
            $helper->rollbackTransaction();
            return response()->json(['message' => $e->getMessage()]);
        }
    }
    public function pedidosPorPeriodo(Request $request){
        try{
            $startData = Carbon::parse($request->start);
            $endData = Carbon::parse($request->end);
            $tmp = null;
            $Pedidos = Pedidos::whereBetween('DT_PAGAMENTO', [$startData, $endData])->paginate(6);
            foreach($Pedidos as $Pedido){
                 $Pedido->PRODUTOS = json_decode($Pedido->PRODUTOS); // transforma o json em um objeto novamente
            }
            return $Pedidos;
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
