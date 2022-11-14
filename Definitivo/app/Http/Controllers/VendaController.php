<?php

namespace App\Http\Controllers;

use App\Despesa;
use App\Pedidos;
use App\Product;
use App\Venda;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;

class VendaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->filled('opcao')){
            $op = $request->opcao;
            switch($op){
                case "Vendas por periodo de dias":
                    return $this->getVendasByDate($request);
                    break;
                case "Vendas por Tipo de Pagamento":
                    return $this->getVendasByTipoPagamento($request);
                    break;
            }
        }
    }
    public function getVendasByDate(Request $request){
        try{
            $startData = Carbon::parse($request->start);
            $endData = Carbon::parse($request->end);
            $user = auth()->user();
            $empresa = $user->empresa;
            $vendas = DB::table('VENDAS')->join('PEDIDOS', function ($joins) use($empresa){
                $joins->on('VENDAS.ID_PEDIDO', '=', 'PEDIDOS.ID')
                ->where('VENDAS.ID_EMPRESA', '=', $empresa->ID);
            })->whereBetween('PEDIDOS.DT_PAGAMENTO', [$startData, $endData])
            ->select('VENDAS.ID', 'VENDAS.VALOR_TOTAL', 'VENDAS.ID_PEDIDO', 'PEDIDOS.METODO_PAGAMENTO', 'PEDIDOS.DT_PAGAMENTO');
            if($request->filled('pdf')){
                $vlTotal_vendas = 0;
                $vlReal = 0;
                $vendas = $vendas->get();
                foreach($vendas as $venda){
                    $pedido = Pedidos::FindOrFail($venda->ID_PEDIDO);
                    $pedido->PRODUTOS = json_decode($pedido->PRODUTOS);
                    foreach($pedido->PRODUTOS as $prod){
                        $tmp = 0;
                        $prod = Product::FindOrFail($prod->id);
                        $prod->MATERIAIS = json_decode($prod->MATERIAIS);
                        foreach($prod->MATERIAIS as $material){
                            $tmp += $material->CUSTO * $material->QUANTIDADE;
                        }
                        $vlReal += ($prod->VALOR - $tmp);
                    }
                    $vlTotal_vendas += $venda->VALOR_TOTAL;
                    $venda->DT_PAGAMENTO = Carbon::parse($venda->DT_PAGAMENTO)->format('d / m / Y :  H:i');
                }
                return response()->json(["dados" => $vendas, "vlTotal" => $vlTotal_vendas,
                "vlReal" => $vlReal, "vlDiff" => ($vlTotal_vendas - $vlReal)]);
            }else{
                $vendas = $vendas->paginate(8);
                foreach($vendas as $venda){
                    $venda->DT_PAGAMENTO = Carbon::parse($venda->DT_PAGAMENTO)->format('d m Y H:i');
                }
                return $vendas;
            }
            return $vendas;
        }catch(\Exception $e){
            return response()->json([
                'message' => $e->getMessage()
            ],400);
        }
    }
    public function getLucroAndGastos(Request $request){
        try{
            $startData = Carbon::parse($request->start);
            $endData = Carbon::parse($request->end);
            $user = auth()->user();
            $empresa = $user->empresa;
            $vlGastosDespesas = Despesa::where('ID_EMPRESA', $empresa->ID)->whereBetween('PEDIDOS.DT_PAGAMENTO', [$startData, $endData])->sum('CUSTO');
            $vlTotal_vendas = 0;
            $vlReal = 0;
            $vendas = DB::table('VENDAS')->join('PEDIDOS', function ($joins) use($empresa){
                $joins->on('VENDAS.ID_PEDIDO', '=', 'PEDIDOS.ID')
                ->where('VENDAS.ID_EMPRESA', '=', $empresa->ID);
            })->whereBetween('PEDIDOS.DT_PAGAMENTO', [$startData, $endData])
            ->select('VENDAS.ID', 'VENDAS.VALOR_TOTAL', 'VENDAS.ID_PEDIDO', 'PEDIDOS.METODO_PAGAMENTO', 'PEDIDOS.DT_PAGAMENTO');
            $vendas = $vendas->get();
            foreach($vendas as $venda){
                $pedido = Pedidos::FindOrFail($venda->ID_PEDIDO);
                $pedido->PRODUTOS = json_decode($pedido->PRODUTOS);
                foreach($pedido->PRODUTOS as $prod){
                    $tmp = 0;
                    $prod = Product::FindOrFail($prod->id);
                    $prod->MATERIAIS = json_decode($prod->MATERIAIS);
                    foreach($prod->MATERIAIS as $material){
                        $tmp += $material->CUSTO * $material->QUANTIDADE;
                    }
                    $vlReal += ($prod->VALOR - $tmp);
                }
                $vlTotal_vendas += $venda->VALOR_TOTAL;

                $venda->DT_PAGAMENTO = Carbon::parse($venda->DT_PAGAMENTO)->format('d / m / Y :  H:i');
            }
            $vlReal -= $vlGastosDespesas;
            return response()->json(["Total" => $vlTotal_vendas, "Lucro" => $vlReal, "Despesas" => $vlGastosDespesas]);
        }catch(\Exception $e){
            return response()->json([
                'message' => $e->getMessage()
            ],400);
        }
    }
    public function getVendasByTipoPagamento(Request $request){
        try{
            $startData = Carbon::parse($request->start);
            $endData = Carbon::parse($request->end);
            $vlPix = 0;
            $vlDinheiro = 0;
            $vlCartao = 0;
            $vlTotal = 0;
            $vlReal = 0;
            $user = auth()->user();
            $empresa = $user->empresa;
            $vendas = DB::table('VENDAS')->join('PEDIDOS', function ($joins) use($empresa){
                $joins->on('VENDAS.ID_PEDIDO', '=', 'PEDIDOS.ID')
                ->where('VENDAS.ID_EMPRESA', '=', $empresa->ID);
            })->whereBetween('PEDIDOS.DT_PAGAMENTO', [$startData, $endData])->paginate(8);
            foreach($vendas as $venda){
                $pedido = Pedidos::FindOrFail($venda->ID_PEDIDO);
                $pedido->PRODUTOS = json_decode($pedido->PRODUTOS);
                foreach($pedido->PRODUTOS as $prod){
                    $tmp = 0;
                    $prod = Product::FindOrFail($prod->id);
                    $prod->MATERIAIS = json_decode($prod->MATERIAIS);
                    foreach($prod->MATERIAIS as $material){
                        $tmp += $material->CUSTO * $material->QUANTIDADE;
                    }
                    $vlReal += ($prod->VALOR - $tmp);
                }
                if($venda->METODO_PAGAMENTO == 'Dinheiro'){
                    $vlDinheiro += $venda->VALOR_TOTAL;
                    $vlTotal += $vlDinheiro;
                }else if($venda->METODO_PAGAMENTO == 'Cartao/Debito'){
                    $vlCartao += $venda->VALOR_TOTAL;
                    $vlTotal += $vlCartao;
                }else if($venda->METODO_PAGAMENTO == 'Pix'){
                    $vlPix += $venda->VALOR_TOTAL;
                    $vlTotal += $venda->VALOR_TOTAL;
                }
            }
            $vls = array($vlDinheiro, $vlCartao, $vlPix, $vlTotal, $vlReal);
            $valores = json_encode($vls);
            return $valores;
        }catch(\Exception $e){
            return response()->json([
                'message' => $e->getMessage()
            ],400);
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
