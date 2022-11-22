<?php

namespace App\Repositories;

use App\Http\interfaces\MedidasInterface;
use App\Medidas;
use Illuminate\Http\Request;

class MedidasRepository implements MedidasInterface
{
    public function __construct()
    {
        //
    }
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
    public function store(Request $request)
    {
        try{
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
                return response()->json(["message" => "Medida cadastrada com sucesso !"]);
            }
        }catch(\Exception $e){
            return response()->json(
                [
                    "message" => $e->getMessage()
                ],400
            );
        }

    }

}
