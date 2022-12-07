<?php

namespace App\Repositories;

use App\Events\MakeLog;
use App\Http\interfaces\MateriaisInterface;
use App\Materiais;
use Illuminate\Http\Request;
class MateriaisRepository implements MateriaisInterface
{

    public function __construct()
    {
        //
    }

    public function index()
    {
        $user = auth()->user();
        $empresa = $user->empresa;
        try{
            $materiais = Materiais::where('ID_EMPRESA', $empresa->ID)
            ->where('QUANTIDADE', '>', 0)->paginate(7);
            return $materiais;
        }catch(\Exception $e){
            return response()->json(
                [
                    "message" => $e->getMessage()
                ],400
            );
        }
    }
    public function adicionaQuantidadeMaterial(Request $request, $id){
        $user = auth()->user();
        $empresa = $user->empresa;
        try{
            $material = Materiais::FindOrFail($id);
            $tmp = $material->QUANTIDADE;
            $material->QUANTIDADE += $request->QUANTIDADE;
            $material->save();
            event(new MakeLog("Materiais", "QUANTIDADE", "update", "$material->QUANTIDADE", "$tmp", $material->ID, $empresa->ID, $user->ID));
            return response()->json(["message" => 'Quantidade Adicionada com sucesso !']);
        }catch(\Exception $e){
            return response()->json(
                [
                    "message" => $e->getMessage()
                ],400
            );
        }
    }
    public function removeQuantidadeMaterial($materiais, $quantidade){
        $user = auth()->user();
        $empresa = $user->empresa;
        try{
            foreach($materiais as $mat){
                $tmp = $mat;
                $mat = Materiais::FindOrFail($mat->ID);
                $mat->QUANTIDADE -= ($tmp->QUANTIDADE * $quantidade);
                $mat->save();
            }
        }catch(\Exception $e){
            return response()->json(
                [
                    "message" => $e->getMessage()
                ],400
            );
        }
    }
    public function store(Request $request){
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
                if($material->save()) {
                    event(new MakeLog("Materiais", "", "insert", "$material->NOME", "", $material->ID, $empresa->ID, $user->ID));
                    return $material;
                };

            }
        }catch(\Exception $e){
            return response()->json(
                [
                    "message" => $e->getMessage()
                ],400
            );
        }
    }
    public function show($id){
        try{
            $mat = Materiais::FindOrFail($id);
            return response()->json($mat);
        }catch(\Exception $e){
            return response()->json(
                [
                    "message" => $e->getMessage()
                ],400
            );
        }
    }
}
