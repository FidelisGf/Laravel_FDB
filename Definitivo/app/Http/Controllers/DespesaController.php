<?php

namespace App\Http\Controllers;

use App\Despesa;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class DespesaController extends Controller
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
                    case 'Duas datas' :
                        return $this->despesaBetweenTwoDates($request);
                        break;
                }
            }else{
                $user = JWTAuth::parseToken()->authenticate();
                $empresa = $user->empresa;
                $despesas = Despesa::where('ID_EMPRESA', $empresa->ID)->with([
                    'Tags' => function($query){
                        $query->select('ID', 'NOME');
                    }
                ])->paginate(6);
                return $despesas;
            }
        }catch(Exception $e){
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
        $helper = new Help();
        try{
            $user = JWTAuth::parseToken()->authenticate();
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
                $helper->startTransaction();
                $despesa->save();
                $helper->commit();
                return $despesa;
            }
        }catch(\Exception $e){
            $helper->rollbackTransaction();
            return response()->json(['message' => $e->getMessage()],400);
        }
    }
    public function despesaBetweenTwoDates(Request $request){
        try{
            $user = JWTAuth::parseToken()->authenticate();
            $empresa = $user->empresa;
            $startData = Carbon::parse($request->start);
            $endData = Carbon::parse($request->end);
            $despesas = Despesa::whereBetween('DATA', [$startData, $endData])->where('ID_EMPRESA', $empresa->ID)->paginate(6);
            return $despesas;
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()],400);
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
        try{
            $user = JWTAuth::parseToken()->authenticate();
            $empresa = $user->empresa;
            $despesa = Despesa::where('ID', $id)->where('ID_EMPRESA', $empresa->ID)->with(['Tags' => function($query){
                $query->select('ID', 'NOME');
            }])->firstOrFail();
            return $despesa;
        }catch(Exception $e){
            return response()->json(['message' => $e->getMessage()],400);
        }
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
        $helper = new Help();
        try{
            $user = JWTAuth::parseToken()->authenticate();
            $empresa = $user->empresa;
            $validatedData = $request->validate([
                'DESC' => ['required', 'max:200', 'min:2'],
                'CUSTO' => ['required', 'min:0.01'],
                'ID_TAG' => ['required'],
                'DATA' => ['required'],
            ]);
            if($validatedData){
                $despesa = Despesa::where('ID', $id)->firstOrFail();
                $despesa->DESC = $request->DESC;
                $despesa->ID_EMPRESA = $empresa->ID;
                $despesa->CUSTO = $request->CUSTO;
                $despesa->DATA = Carbon::parse($request->DATA);
                $despesa->ID_TAG = $request->ID_TAG;
                $helper->startTransaction();
                $despesa->save();
                $helper->commit();
                return $despesa;

            }
        }catch(Exception $e){
            $helper->rollbackTransaction();
            return response()->json(['message' => $e->getMessage()],400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $helper = new Help();
        try{
            $despesa = Despesa::where('ID', $id)->firstOrFail();
            $helper->startTransaction();
            $despesa->delete();
            $helper->commit();
        }catch(\Exception $e){
            $helper->rollbackTransaction();
            return response()->json(['message' => $e->getMessage()],400);
        }
    }
}
