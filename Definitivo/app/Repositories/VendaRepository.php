<?php

namespace App\Repositories;

use App\Despesa;
use App\Http\interfaces\VendaInterface;
use App\Pedidos;
use App\Product;
use App\Venda;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
                    $pedido->PRODUTOS = json_decode($pedido->PRODUTOS);
                    foreach($pedido->PRODUTOS as $prod){
                        $tmp = 0;
                        $prod = Product::withTrashed()->FindOrFail($prod->id);
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
                    $prod = Product::withTrashed()->FindOrFail($prod->id);
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
    public function getTotalVendasInTheLastThreeMonths(){
        try{
            $user = auth()->user();
            $startData = $this->getStartOfThisMonthByActualDay();
            $endData = date_create('today 23:00');
            $mes3 = floatval($this->getSalesSumBetweenDates($startData, $endData));
            $startData = $this->transformDataToAMonthInThePast("1", "start");
            $endData = $this->transformDataToAMonthInThePast("0", "end");
            $mes2 = floatval( $this->getSalesSumBetweenDates($startData, $endData) );
            $startData = $this->transformDataToAMonthInThePast("2", "start");
            $endData = $this->transformDataToAMonthInThePast("1", "end");
            $mes1 = floatval( $this->getSalesSumBetweenDates($startData, $endData) );
            $meses = [$mes1, $mes2, $mes3];
            return response()->json(["valores" => $meses]);
        }catch(\Exception $e){
            return response()->json([
                'message' => $e->getMessage()
            ],400);
        }
    }
    public function getSalesSumBetweenDates($startData, $endData){
        $user = auth()->user();
        $empresa = $user->empresa;
        $vendas = DB::table('VENDAS')->join('PEDIDOS', function ($joins) use($empresa){
            $joins->on('VENDAS.ID_PEDIDO', '=', 'PEDIDOS.ID')
            ->where('VENDAS.ID_EMPRESA', '=', $empresa->ID);
        })->whereBetween('PEDIDOS.DT_PAGAMENTO', [$startData, $endData])->sum('VENDAS.VALOR_TOTAL');
        return $vendas;
    }

    public function transformDataToAMonthInThePast($mes, $type){
        $temp = strval(date_create('today')->format('Y-m-d'));
        $d = substr(strval($temp), strrpos(strval($temp), '-') + 1);
        $strD = intval( $d - 1 );
        $strD = strval($strD);
        $tempData=  date_create('today');
        if($type == "start"){
            return date_add($tempData, date_interval_create_from_date_string("-$mes month -$strD days"));
        }else{
            $tempData=  date_create('today');
            return date_add($tempData, date_interval_create_from_date_string("-$mes month -$d days"));
        }
    }
    public function getStartOfThisMonthByActualDay(){
        $temp = strval(date_create('today')->format('Y-m-d'));
        $d = substr(strval($temp), strrpos(strval($temp), '-') + 1); // procura dentro de temp a string após o primeiro - , porem o mais 1 faz com que ele vá para a proxima string, ou seja 'd' , que representa nossos dias
        $tempData=  date_create('today');
        $strD = intval( $d - 1 );
        $strD = strval($strD);
        return date_add($tempData, date_interval_create_from_date_string("-$strD days"));
    }
}
