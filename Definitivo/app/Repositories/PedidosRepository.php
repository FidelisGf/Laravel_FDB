<?php

namespace App\Repositories;

use App\Estoque;
use App\FakeProduct;
use App\Http\Controllers\Help;
use App\Http\interfaces\PedidoInterface;
use App\Http\Resources\FakeProduct as ResourcesFakeProduct;
use App\Pedidos;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PedidosRepository implements PedidoInterface
{
    private $model;
    public function __construct(Pedidos $model)
    {
        $this->model = $model;
    }
    public function index(Request $request){
        try{
            if($request->filled('opcao')){
                $opcao = $request->opcao;
                switch($opcao){
                    case 'Pedidos realizados entre duas datas' :
                        return $this->pedidosPorPeriodo($request);
                        break;
                }
            }else{
                $user = auth()->user();
                $empresa = $user->empresa;
                $Pedidos = Pedidos::where('ID_EMPRESA', $empresa->ID)->paginate(6);
                return $Pedidos;
            }
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()]);
        }
    }
    public function store(Request $request){
        try{
            $helper = new Help();

            $validatedData = $request->validate([
                'METODO_PAGAMENTO' => ['required'],
                'produtos' => ['required'],
                'aprovado' => ['required'],
            ]);
            if($validatedData){
                    $metodo_pagamento = $request->METODO_PAGAMENTO;
                    $user = auth()->user();
                    $empresa = $user->empresa;
                    $vlTotal = 0;
                    $pedido = new Pedidos();
                    $estoque = new EstoqueRepository();
                    $FakeProducts = collect(new FakeProduct());
                    $PRODUCTS = collect(new FakeProduct());
                    foreach($request->produtos as $produto){
                        $FakeProduct = new ResourcesFakeProduct((object) $produto);
                        $FakeProducts->push($FakeProduct);
                    }
                    foreach($FakeProducts as $produto){
                        $vlTotal += $produto->valor * $produto->quantidade;
                        $PRODUCTS->push($produto);
                        $estoque->removeEstoque($produto->id, $produto->quantidade);
                    }
                    $pedido->PRODUTOS = json_encode($PRODUCTS); // manda em formato de json para o banco
                    $pedido->METODO_PAGAMENTO = $metodo_pagamento;
                    $pedido->ID_EMPRESA = $empresa->ID;
                    $pedido->VALOR_TOTAL = $vlTotal;
                    $pedido->APROVADO = "$request->aprovado";
                    if($request->filled('ID_CLIENTE')){
                        $pedido->ID_CLIENTE = $request->ID_CLIENTE;
                    }
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
            return response()->json(['message' => $e->getMessage()],400);
        }
    }
    public function checkQuantidadeProduto(Request $request){
        try{
            $prod = Estoque::where('PRODUCT_ID', $request->id)->firstOrFail();
            if($prod->QUANTIDADE <= $request->quantidade){
                return response()->json(false);
            }else{
                return response()->json(true);
            }
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()],400);
        }
    }
    public function show($id){
        try{
            $pedido = Pedidos::FindOrFail($id);
            $pedido->PRODUTOS = json_decode($pedido->PRODUTOS); // transforma o json em um objeto novamente
            return $pedido;
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()],404);
        }
    }
    public function aprovarPedido($id){
        try{
            $Pedido = Pedidos::FindOrFail($id);
            $Pedido->APROVADO = 'T';
            $Pedido->DT_PAGAMENTO = now()->format('Y-m-d H:i');
            $Pedido->save();
            return $Pedido;
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()]);
        }
    }
    public function update(Request $request, $id){
        $helper = new Help();
        try{
            $estoque = new EstoqueRepository();
            $pedido = Pedidos::FindOrFail($id);
            $pedido->PRODUTOS = json_decode($pedido->PRODUTOS);
            foreach($pedido->PRODUTOS as $produto){
                $request->product_id = $produto->id;
                $request->quantidade = $produto->quantidade;
                $estoque->addEstoque($request);
            }
            $FakeProducts = collect(new FakeProduct());
            $PRODUCTS = collect(new FakeProduct());
            foreach($request->PRODUTOS as $produto){
                $FakeProduct = new ResourcesFakeProduct((object) $produto);
                $FakeProducts->push($FakeProduct);
            }
            $valor_total = 0;
            foreach($FakeProducts as $produto){
               $valor_total += $produto->valor * $produto->quantidade;
               $PRODUCTS->push($produto);
               $estoque->removeEstoque($produto->id, $produto->quantidade);
            }
            $pedido->METODO_PAGAMENTO = $request->METODO_PAGAMENTO;
            $pedido->VALOR_TOTAL = $valor_total;
            $pedido->APROVADO = "$request->APROVADO";
            $pedido->PRODUTOS = json_encode($PRODUCTS);
            if($pedido->APROVADO == 'T'){
                $pedido->DT_PAGAMENTO = now()->format('Y-m-d H:i');
            }
            if($request->filled('ID_CLIENTE')){
                $pedido->ID_CLIENTE = $request->ID_CLIENTE;
            }else{
                $pedido->ID_CLIENTE = null;
            }
            $helper->startTransaction();
            $pedido->save();
            $helper->commit();
            $pedido->PRODUTOS = json_decode($pedido->PRODUTOS);
            return $pedido;
        }catch(\Exception $e){
            $helper->rollbackTransaction();
            return response()->json(['message' => $e->getMessage()],400);
        }
    }
    public function pedidosPorPeriodo(Request $request){
        try{
            if($request->filled('pdf')){
                $user = auth()->user();
                $empresa = $user->empresa;
                $startData = Carbon::parse($request->start);
                $endData = Carbon::parse($request->end);
                $Pedidos = Pedidos::whereBetween('CREATED_AT', [$startData, $endData])->where('ID_EMPRESA', $empresa->ID)->
                select('ID', 'METODO_PAGAMENTO', 'VALOR_TOTAL', 'APROVADO', 'CREATED_AT')->get();
                return $Pedidos;
            }else{
                $user = auth()->user();
                $empresa = $user->empresa;
                $startData = Carbon::parse($request->start);
                $endData = Carbon::parse($request->end);
                $tmp = null;
                $Pedidos = Pedidos::whereBetween('CREATED_AT', [$startData, $endData])->where('ID_EMPRESA', $empresa->ID)->paginate(6);
                foreach($Pedidos as $Pedido){
                     $Pedido->PRODUTOS = json_decode($Pedido->PRODUTOS); // transforma o json em um objeto novamente
                }
                return $Pedidos;
            }
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()],400);
        }
    }
}
