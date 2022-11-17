<?php

namespace App\Repositories;

use App\Despesa;
use App\Http\interfaces\DespesaInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DespesaRepository implements DespesaInterface
{
    public function __construct()
    {
        //
    }
    public function index(Request $request)
    {
        try{
            if($request->filled('opcao')){
                $opcao = $request->opcao;
                switch($opcao){
                    case 'Despesas entre duas datas' :
                        return $this->despesaBetweenTwoDates($request);
                        break;
                }
            }else{
                $user = auth()->user();
                $empresa = $user->empresa;
                $despesas = Despesa::where('ID_EMPRESA', $empresa->ID)->with([
                    'Tags' => function($query){
                        $query->select('ID', 'NOME');
                    }
                ])->paginate(6);
                return $despesas;
            }
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()],400);
        }
    }
    public function sumDespesasMensais(){
        try{
            $startData = date('Y-m-01');
            $endData = date('Y-m-t');
            $empresa = auth()->user()->empresa;
            $valor = Despesa::where('ID_EMPRESA', $empresa->ID)->whereBetween('DATA', [$startData, $endData])->sum('CUSTO');
            return $valor;
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()],400);
        }
    }
    public function store(Request $request)
    {
        try{
            $user = auth()->user();
            $empresa = $user->empresa;
            $validatedData = $request->validate([
                'DESC' => ['required', 'max:200', 'min:2'],
                'CUSTO' => ['required', 'min:0.01'],
                'ID_TAG' => ['required'],
                'DATA' => ['required'],
            ]);
            if($validatedData){
                $despesa = new Despesa();
                $despesa->DESC = $request->DESC;
                $despesa->ID_EMPRESA = $empresa->ID;
                $despesa->CUSTO = $request->CUSTO;
                $despesa->DATA = Carbon::parse($request->DATA);
                $despesa->ID_TAG = $request->ID_TAG;
                $despesa->save();
                return $despesa;
            }
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()],400);
        }
    }
    public function despesasByTag($id){
        try{
            $user = auth()->user();
            $empresa = $user->empresa;
            $despesa = Despesa::where('ID_EMPRESA', $empresa->ID)->where('ID_TAG', $id)->with([
                'Tags' => function($query){
                    $query->select('ID', 'NOME');
                }
            ])->paginate(6);
            return $despesa;
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()],400);
        }
    }
    public function despesaBetweenTwoDates(Request $request){
        try{
            $user = auth()->user();
            $empresa = $user->empresa;
            $startData = Carbon::parse($request->start);
            $endData = Carbon::parse($request->end);
            $despesas = Despesa::whereBetween('DATA', [$startData, $endData])->where('ID_EMPRESA', $empresa->ID)
            ->with(['Tags' => function($query){
                $query->select('ID', 'NOME');
            }])->paginate(6);
            return $despesas;
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()],400);
        }
    }
    public function show($id)
    {
        try{
            $user = auth()->user();
            return Despesa::where('ID', $id)->with(['Tags' => function($query){
                $query->select('ID', 'NOME');
            }])->firstOrFail();

        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()],400);
        }
    }
    public function update(Request $request, $id)
    {
        try{
            $user = auth()->user();
            $empresa = $user->empresa;
            $despesa = Despesa::FindOrFail($id);
            $despesa->DESC = $request->DESC;
            $despesa->ID_EMPRESA = $empresa->ID;
            $despesa->CUSTO = $request->CUSTO;
            $despesa->DATA = Carbon::parse($request->DATA);
            $despesa->ID_TAG = $request->ID_TAG;
            $despesa->save();
            return $despesa;
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()],400);
        }
    }
    public function destroy($id)
    {
        try{
            $despesa = Despesa::FindOrFail($id);
            $despesa->delete();
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()],400);
        }
    }

}