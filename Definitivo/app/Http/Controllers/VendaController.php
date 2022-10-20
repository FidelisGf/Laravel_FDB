<?php

namespace App\Http\Controllers;

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
            }
        }
    }
    public function getVendasByDate(Request $request){
        try{
            $startData = Carbon::parse($request->start);
            $endData = Carbon::parse($request->end);
            $user = JWTAuth::parseToken()->authenticate();
            $empresa = $user->empresa;
            $vendas = DB::table('VENDAS')->join('PEDIDOS', function ($joins) use($empresa){
                $joins->on('VENDAS.ID_PEDIDO', '=', 'PEDIDOS.ID')
                ->where('VENDAS.ID_EMPRESA', '=', $empresa->ID);
            })->whereBetween('PEDIDOS.DT_PAGAMENTO', [$startData, $endData])
            ->select('VENDAS.ID', 'VENDAS.VALOR_TOTAL', 'VENDAS.ID_PEDIDO', 'PEDIDOS.METODO_PAGAMENTO', 'PEDIDOS.DT_PAGAMENTO')->paginate(6);
            foreach($vendas as $venda){
                $venda->DT_PAGAMENTO = Carbon::parse($venda->DT_PAGAMENTO)->format('d M Y H:i');
            }
            return $vendas;
        }catch(\Exception $e){
            return response()->json([
                'message' => $e->getMessage()
            ],400);
        }
    }
    public function getVendasByTipoPagamento(Request $request){
        $startData = Carbon::parse($request->start);
        $endData = Carbon::parse($request->end);
        $user = JWTAuth::parseToken()->authenticate();
        $empresa = $user->empresa;
        $vendas = DB::table('VENDAS')->join('PEDIDOS', function ($joins) use($empresa){
            $joins->on('VENDAS.ID_PEDIDO', '=', 'PEDIDOS.ID')
            ->where('VENDAS.ID_EMPRESA', '=', $empresa->ID);
        })->whereBetween('PEDIDOS.DT_PAGAMENTO', [$startData, $endData])->sum('VENDAS.VALOR_TOTAL')
        ->where('METODO_PAGAMENTO', '=', 'DINHEIRO');
        return $vendas;
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
