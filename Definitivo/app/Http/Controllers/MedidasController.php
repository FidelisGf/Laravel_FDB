<?php

namespace App\Http\Controllers;

use App\Medidas;
use Illuminate\Http\Request;

class MedidasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $user = auth()->user();
            $empresa = $user->empresa;
            return Medidas::where('ID_EMPRESA', $empresa->ID)->paginate(20);
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
        $validatedData = $request->validate([
            'NOME' => ['required', 'max:100', 'min:1']
        ]);
        if($validatedData){
            $medida = new Medidas();
            $NOME_REAL = "$request->NOME _ $empresa->ID";
            $medida->NOME = $request->NOME;
            $medida->ID_EMPRESA = $empresa->ID;
            $medida->NOME_REAL = $NOME_REAL;
            $medida->save();
            return $medida;
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
