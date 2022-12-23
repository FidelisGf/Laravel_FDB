<?php

namespace App\Repositories;

use App\Cliente;
use App\Config_General;
use App\Estoque;
use App\Events\MakeLog;
use App\Events\VendaGerada;
use App\FakeProduct;
use App\Http\Controllers\Help;
use App\Http\interfaces\PedidoInterface;
use App\Http\Requests\StoreEstoqueValidator;
use App\Http\Requests\StorePedidoValidator;
use App\Http\Resources\FakeProduct as ResourcesFakeProduct;
use App\Mail\SendMailUser;
use App\Notifications\EmailNotify;
use App\Pedido_Itens;
use App\Pedidos;
use App\Product;
use App\Venda;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification as FacadesNotification;

class PedidosRepository implements PedidoInterface
{
    private $model;
    public function __construct(Pedidos $model)
    {
        $this->model = $model;
    }
    public function pedido_factory(Request $request, $id){
        $user = auth()->user();
        $empresa = $user->empresa;
        $pedido = new Pedidos();
        if($id != 0){
            $pedido = Pedidos::FindOrFail($id);
        }
        $pedido->METODO_PAGAMENTO = $request->METODO_PAGAMENTO;
        $pedido->ID_EMPRESA = $empresa->ID;
        $pedido->VALOR_TOTAL = 0;
        $pedido->APROVADO = "$request->aprovado";

        if($request->filled('ID_CLIENTE')){
            $pedido->ID_CLIENTE = $request->ID_CLIENTE;
        }
        if($pedido->APROVADO == 'T'){
            $pedido->DT_PAGAMENTO = now()->format('Y-m-d H:i');
        }
        $pedido->ID_USER = $user->ID;
        return $pedido;


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

                $Pedidos = Pedidos::where('ID_EMPRESA', $empresa->ID)
                ->paginate(6);

                return $Pedidos;
            }
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()]);
        }
    }
    public function store(StorePedidoValidator $request){
        try{
            $validatedData = $request->validated();
            if($validatedData){
                    $vlTotal = 0;
                    $estoque = new EstoqueRepository();
                    $ItensPedido = collect(new Pedido_Itens());
                    $FakeProducts = collect(new FakeProduct());
                    $pedido = $this->pedido_factory($request, 0);
                    foreach($request->produtos as $produto){
                        $itens = new Pedido_Itens();
                        $FakeProduct = new ResourcesFakeProduct((object) $produto);
                        $itens->ID_PRODUTO = $FakeProduct->ID;
                        $itens->QUANTIDADE = $FakeProduct->QUANTIDADE;
                        $itens->VALOR = $FakeProduct->VALOR;
                        $ItensPedido->push($itens);
                        $vlTotal += $FakeProduct->VALOR * $FakeProduct->QUANTIDADE;
                        $estoque->removeEstoque($FakeProduct->ID, $FakeProduct->QUANTIDADE);
                        $FakeProducts->push($FakeProduct);
                    }
                    $pedido->VALOR_TOTAL = $vlTotal;
                    $comissao = Config_General::where('NOME', '=', 'Comissao')->first();
                    if($comissao != null && $pedido->APROVADO == 'T' && $comissao->ESTADO == 1){
                        $pedido->COMISSAO = ($pedido->VALOR_TOTAL * floatval($comissao->PARAMETRO) / 100);
                    }else{
                        $comissao = false;
                    }
                    $pedido->save();
                    foreach($ItensPedido as $item){
                        $item->ID_PEDIDO = $pedido->ID;
                        $item->save();
                    }
                    if($pedido->APROVADO == 'T' && $request->filled('ID_CLIENTE')){
                        $user = auth()->user();

                        FacadesNotification::route('mail', $user->EMAIL)
                        ->notify(new EmailNotify($pedido, $FakeProducts));
                    }
                    return response()->json(['message' => "Pedido Registrado com sucesso",
                    'pedido' => $pedido]);
            }
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()],400);
        }
    }
    public function checkQuantidadeProduto(Request $request){
        try{
            $prod = Estoque::where('PRODUCT_ID', $request->ID)
            ->firstOrFail();

            if($prod->QUANTIDADE <= $request->QUANTIDADE){
                return response()->json(false,400);
            }else{
                return response()->json(true);
            }
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()],400);
        }
    }
    public function show($id){ // sem necessadide de otimazar por enquanto
        try{
            $pedido = Pedidos::FindOrFail($id);
            $prod =  $pedido->itens;
            $produtos = collect(new Product());
            foreach($prod as $p){
                $qntd = $p->QUANTIDADE;
                $valor = $p->VALOR;
                $p = Product::where('ID', $p->ID_PRODUTO)
                ->with(['medida' => function($query){
                    $query->select('ID', 'NOME');
                }])->firstOrFail();
                $p->MEDIDA = $p->medida->NOME;
                $p->QUANTIDADE = $qntd;
                $p->VALOR = $valor;
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
    public function update(StorePedidoValidator $request, $id){
        $helper = new Help();
        try{
            $validator = $request->validated();
            if($validator){
                $user = auth()->user();
                $estoque = new EstoqueRepository();
                $pedido = $this->pedido_factory($request, $id);
                $FakeProducts = collect(new FakeProduct());
                $valor_total = 0;
                foreach($request->produtos as $produto){
                    $flag = false;
                    $helper->startTransaction();
                    $FakeProduct = new ResourcesFakeProduct((object) $produto);
                    $query = Pedido_Itens::where('ID_PEDIDO', $id)
                    ->where('ID_PRODUTO', $FakeProduct->ID)
                    ->first();
                    if($query != null){
                        $query->QUANTIDADE = $FakeProduct->QUANTIDADE;
                        $query->save();
                        if($query->QUANTIDADE <  $FakeProduct->QUANTIDADE){

                            $FakeProduct->QUANTIDADE = intval($query->QUANTIDADE - $FakeProduct->QUANTIDADE);

                        }else if($query->QUANTIDADE > $FakeProduct->QUANTIDADE){

                            $estoque->restauraEstoque($FakeProduct->ID,
                            intval($query->QUANTIDADE - $FakeProduct->QUANTIDADE));

                        }else{
                            $flag = true;
                        }
                    }else{
                        $itens = new Pedido_Itens();
                        $itens->ID_PRODUTO = $FakeProduct->ID;
                        $itens->QUANTIDADE = $FakeProduct->QUANTIDADE;
                        $itens->ID_PEDIDO = $id;
                        $itens->VALOR = $FakeProduct->VALOR;
                        $itens->save();
                    }
                    $pedido->VALOR_TOTAL += $FakeProduct->VALOR * $FakeProduct->QUANTIDADE;
                    if(!$flag){
                        $estoque->removeEstoque($FakeProduct->ID, $FakeProduct->QUANTIDADE);
                    }
                    $FakeProducts->push($FakeProduct);
                    $helper->commit();
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
    public function pedidosPorPeriodo(Request $request){
        try{
            if($request->filled('pdf')){
                $user = auth()->user();
                $empresa = $user->empresa;
                $startData = Carbon::parse($request->start);
                $endData = Carbon::parse($request->end);

                $Pedidos = Pedidos::whereBetween('CREATED_AT', [$startData, $endData])
                ->where('ID_EMPRESA', $empresa->ID)
                ->select('ID', 'METODO_PAGAMENTO', 'VALOR_TOTAL', 'APROVADO', 'CREATED_AT')
                ->get();

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

                $Pedidos = Pedidos::whereBetween('CREATED_AT', [$startData, $endData])
                ->where('ID_EMPRESA', $empresa->ID)
                ->paginate(6);

                return $Pedidos;
            }
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()],400);
        }
    }

}
