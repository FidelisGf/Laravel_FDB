<?php

namespace App\Repositories;

use App\Despesa;
use App\Http\interfaces\VendaInterface;
use App\Materiais;
use App\Pedidos;
use App\Product;
use App\Venda;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\VarDumper\Cloner\Data;

class VendaRepository implements VendaInterface
{
    private $model;
    public function __construct(Venda $model)
    {
        $this->model = $model;
    }
    public function index(Request $request){
        if($request->filled('opcao')){
            $op = $request->opcao;
            switch($op){
                case "Vendas por periodo de dias":
                    return $this->getVendasByDate($request);
                    break;
                case "Vendas por Tipo de Pagamento":
                    return $this->getVendasByTipoPagamento($request);
                    break;
                case "Lucros e Gastos por dias" :
                    return $this->getLucroAndGastos($request);
                    break;
            }
        }
    }
    public function getVendasPorDia()
    {
        try{
            $user = auth()->user();
            $empresa = $user->empresa;
            $startData =  date_create('today');
            $endData = date_create('today 23:00');
            $vendas = DB::table('VENDAS')->join('PEDIDOS', function ($joins) use($empresa){
                $joins->on('VENDAS.ID_PEDIDO', '=', 'PEDIDOS.ID')
                ->where('VENDAS.ID_EMPRESA', '=', $empresa->ID);
            })->whereBetween('PEDIDOS.DT_PAGAMENTO', [$startData, $endData])
            ->select('VENDAS.VALOR_TOTAL');
            $v = [];
            $v[0] = floatval( $vendas->min('VENDAS.VALOR_TOTAL') );
            $v[1] = floatval( $vendas->avg('VENDAS.VALOR_TOTAL') );
            $v[2] = floatval( $vendas->max('VENDAS.VALOR_TOTAL') );
            $v[3] = floatval( $vendas->sum('VENDAS.VALOR_TOTAL'));
            $qntd = intval($vendas->count());
            return response()->json(["values" => $v, "quantidade" => $qntd]);
        }catch(\Exception $e){
            return response()->json([
                'message' => $e->getMessage()
            ],400);
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
                    $produtos = collect(new Product());
                    foreach($pedido->itens as $p){
                        $p = Product::withTrashed()->FindOrFail($p->ID_PRODUTO);
                        $produtos->push($p);
                    }
                    foreach($produtos as $prod){
                        $tmp = 0;
                        $materias = collect(new Materiais());
                        foreach($prod->materias as $matItem){
                            $qntd = $matItem->QUANTIDADE;
                            $matItem = Materiais::FindOrFail($matItem->ID_MATERIA);
                            $matItem->QUANTIDADE = $qntd;
                            $materias->push($matItem);
                        }
                        foreach($materias as $material){
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
            $vlGastosDespesas = Despesa::where('ID_EMPRESA', $empresa->ID)->whereBetween('DATA', [$startData, $endData])->sum('CUSTO');
            $vlTotal_vendas = 0;
            $vlReal = 0;
            $saldoFinal = 0;
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
                    $custo = 0;
                    $tmpProd = Product::withTrashed()->FindOrFail($prod->id);
                    $tmpProd->MATERIAIS = json_decode($tmpProd->MATERIAIS);
                    foreach($tmpProd->MATERIAIS as $material){
                        $custo += $material->CUSTO * $material->QUANTIDADE;
                    }
                    $vlReal += ( ($tmpProd->VALOR - $custo) * $prod->quantidade) ;
                }
                $vlTotal_vendas += $venda->VALOR_TOTAL;
                $venda->DT_PAGAMENTO = Carbon::parse($venda->DT_PAGAMENTO)->format('d / m / Y :  H:i');
            }
            $saldoFinal = $vlReal - $vlGastosDespesas;
            return response()->json(["Total" => $vlTotal_vendas, "Lucro_Vendas" => $vlReal, "Despesas" => $vlGastosDespesas, "Saldo_Final" => $saldoFinal]);
        }catch(\Exception $e){
            return response()->json([
                'message' => $e->getMessage()
            ],400);
        }
    }
    public function getVendasByTipoPagamento(Request $request){ // fazer somente na query sql
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
            })->whereBetween('PEDIDOS.DT_PAGAMENTO', [$startData, $endData])
            ->select(DB::raw('sum(PEDIDOS.VALOR_TOTAL) as valores'))
            ->groupBy('PEDIDOS.METODO_PAGAMENTO')->orderBy('PEDIDOS.METODO_PAGAMENTO', 'asc')->get();
            return response()->json($vendas);
        }catch(\Exception $e){
            return response()->json([
                'message' => $e->getMessage()
            ],400);
        }
    }
    public function getTotalVendasInTheLastThreeMonths(){
        try{
            $user = auth()->user();
            $empresa = $user->empresa;
            $date =  date_create('today 23:00')->format('Y-m-d H:i');
            $tmp = $date;
            $hoje = date('d');
            $hoje = strval(60 + $hoje);
            $dateIni = date_create("-$hoje days")->format('Y-m-d H:i');
            $meses = DB::table('PEDIDOS')->where('PEDIDOS.DT_PAGAMENTO', '!=', null)->
            whereBetween('PEDIDOS.DT_PAGAMENTO', [$dateIni, $date])
            ->select(DB::raw('extract (MONTH from PEDIDOS.DT_PAGAMENTO) as mes, sum(PEDIDOS.VALOR_TOTAL) as valores'))
            ->groupBy(DB::raw('mes'))->get();
            $valores = [];
            foreach($meses as $mes){
                $valores[] = floatval( $mes->VALORES );
            }
            return response()->json([$dateIni]);
        }catch(\Exception $e){
            return response()->json([
                'message' => $e->getMessage()
            ],400);
        }
    }

}
