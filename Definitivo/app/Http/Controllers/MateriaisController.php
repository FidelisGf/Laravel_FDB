<?php

namespace App\Http\Controllers;

use App\Materiais;
use Illuminate\Http\Request;

class MateriaisController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();
        $empresa = $user->empresa;
        try{
            $materiais = Materiais::where('ID_EMPRESA', $empresa->ID)->
            where('QUANTIDADE', '>', 0)->paginate(7);
            return $materiais;
        }catch(\Exception $e){
            return response()->json(
                [
                    "message" => $e->getMessage()
                ],400
            );
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
        $user = auth()->user();
        $empresa = $user->empresa;
        try{
            $validatedData = $request->validate([
                'NOME' => ['required', 'max:60', 'min:2'],
                'CUSTO' => ['required']
            ]);
            if($validatedData){
                $NOME_REAL = "$request->NOME _ $empresa->ID";
                $material = new Materiais();
                $material->ID_EMPRESA = $empresa->ID;
                $material->NOME = $request->NOME;
                $material->CUSTO = $request->CUSTO;
                $material->NOME_REAL = $NOME_REAL;
                $material->QUANTIDADE = $request->QUANTIDADE;
                $material->save();
                return $material;
            }
        }catch(\Exception $e){
            return response()->json(
                [
                    "message" => $e->getMessage()
                ],400
            );
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
            $material = Materiais::FindOrFail($id);
            return $material;
        }catch(\Exception $e){
            return response()->json(
                [
                    "message" => $e->getMessage()
                ],400
            );
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
