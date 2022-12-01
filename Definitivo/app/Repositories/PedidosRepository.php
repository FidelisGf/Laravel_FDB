<?php

namespace App\Repositories;

use App\Cliente;
use App\Estoque;
use App\Events\MakeLog;
use App\Events\VendaGerada;
use App\FakeProduct;
use App\Http\Controllers\Help;
use App\Http\interfaces\PedidoInterface;
use App\Http\Resources\FakeProduct as ResourcesFakeProduct;
use App\Mail\SendMailUser;
use App\Pedido_Itens;
use App\Pedidos;
use App\Product;
use App\Venda;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

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
                    $pedido->ID_USER = $user->ID;
                    $pedido->save();
                    foreach($request->produtos as $produto){
                        $itens = new Pedido_Itens();
                        $FakeProduct = new ResourcesFakeProduct((object) $produto);
                        $itens->ID_PRODUTO = $FakeProduct->ID;
                        $itens->QUANTIDADE = $FakeProduct->QUANTIDADE;
                        $itens->ID_PEDIDO = $pedido->ID;
                        $itens->save();
                        $vlTotal += $FakeProduct->VALOR * $FakeProduct->QUANTIDADE;
                        $estoque->removeEstoque($FakeProduct->ID, $FakeProduct->QUANTIDADE);
                        $FakeProducts->push($FakeProduct);
                    }
                    $pedido->VALOR_TOTAL = $vlTotal;
                    $pedido->save();
                    if($pedido->APROVADO == 'T' && $request->filled('ID_CLIENTE')){
                        //$pedido->PRODUTOS = json_decode($PRODUCTS);
                        Mail::to($pedido->cliente->EMAIL)->send(new SendMailUser($pedido, $FakeProducts, $pedido->cliente));
                    }
                    return $pedido;
            }
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()],400);
        }
    }
    public function checkQuantidadeProduto(Request $request){
        try{
            $prod = Estoque::where('PRODUCT_ID', $request->ID)->firstOrFail();
            if($prod->QUANTIDADE <= $request->QUANTIDADE){
                return response()->json(false,400);
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
            $prod =  $pedido->itens;
            $produtos = collect(new Product());
            foreach($prod as $p){
                $qntd = $p->QUANTIDADE;
                $p = Product::FindOrFail($p->ID_PRODUTO);
                $p->QUANTIDADE = $qntd;
                $produtos->push($p);

            }
            return response()->json(['pedido' => $pedido, 'produtos' => $produtos]);
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
    public function destroy($id){
        try{
            $user = auth()->user();
            $empresa = $user->empresa;
            $pedido = Pedidos::FindOrFail($id);
            $pedido->delete();
            event(new MakeLog("Pedidos", "", "delete", "", "", $pedido->ID, $empresa->ID, $user->ID));
            return response()->json(['message' => "Deletado com sucesso !"]);
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()]);
        }
    }
    public function update(Request $request, $id){
        $user = auth()->user();
        $helper = new Help();

        try{
            $estoque = new EstoqueRepository();
            $pedidoItensRep = new PedidoItensRepository();
            $pedido = Pedidos::FindOrFail($id);
            $FakeProducts = collect(new FakeProduct());
            $valor_total = 0;
            $pedido->METODO_PAGAMENTO = $request->METODO_PAGAMENTO;
            $pedido->VALOR_TOTAL = $valor_total;
            $pedido->APROVADO = "$request->APROVADO";
            $pedido->ID_USER = $user->ID;
            if($pedido->APROVADO == 'T'){
                $pedido->DT_PAGAMENTO = now()->format('Y-m-d H:i');
            }
            if($request->filled('ID_CLIENTE')){
                $pedido->ID_CLIENTE = $request->ID_CLIENTE;
            }else{
                $pedido->ID_CLIENTE = null;
            }
            $pedido->save();
            foreach($request->PRODUTOS as $produto){
                $helper->startTransaction();
                $FakeProduct = new ResourcesFakeProduct((object) $produto);
                $request->product_id = $FakeProduct->ID;
                $request->quantidade = $FakeProduct->QUANTIDADE;
                $estoque->addEstoque($request);
                $query = Pedido_Itens::where('ID_PEDIDO', $id)->where('ID_PRODUTO', $FakeProduct->ID)->first();
                if($query != null){
                    $query->QUANTIDADE = $FakeProduct->QUANTIDADE;
                    $query->save();
                    //return response()->json(['pedido' => $query]);
                }else{
                    $itens = new Pedido_Itens();
                    $itens->ID_PRODUTO = $FakeProduct->ID;
                    $itens->QUANTIDADE = $FakeProduct->QUANTIDADE;
                    $itens->ID_PEDIDO = $id;
                    $itens->save();
                }
                $valor_total += $FakeProduct->VALOR * $FakeProduct->QUANTIDADE;
                $estoque->removeEstoque($FakeProduct->ID, $FakeProduct->QUANTIDADE);
                $FakeProducts->push($FakeProduct);
                $helper->commit();
            }

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
                foreach($Pedidos as $p){
                    $p->APROVADO = $p->APROVADO == "T" ? "PAGO" : "PENDENTE";
                }
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
