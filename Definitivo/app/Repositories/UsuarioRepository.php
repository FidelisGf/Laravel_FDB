<?php

namespace App\Repositories;

use App\Empresa;
use App\Http\interfaces\UsuarioInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class UsuarioRepository implements UsuarioInterface
{
    public function __construct()
    {

    }
    public function vinculaUsuarioEmpresa(Request $request){
        try{
            $validator = $request->validate([
                'NOME' => 'required|unique:EMPRESAS|max:50|min:2',
                'CNPJ' => 'required|min:18|max:18',
                'EMAIL'=> 'required|unique:EMPRESAS|email|max:120|min:5',
                'NOME_FANTASIA' => 'required|max:60|min:2',
                'ENDERECO' => 'required|max:255|min:2',
                'INC_ESTADUAL' => 'required|unique:EMPRESAS|max:12|min:9',
            ]);
            if($validator){
                $Empresa = Empresa::create($request->all());
                if($Empresa){
                    $auth = JWTAuth::parseToken()->authenticate();
                    $USER = $auth;
                    $USER->EMPRESA_ID = $Empresa->ID;
                    if($USER->save()){
                        return response()->json(['message' => 'Usuario e Empresa vinculados com sucesso !']);
                    }else{
                        return response()->json(['message' => 'Falhar ao vincular usuario !'],400);
                    }
                }
            }
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()],400);
        }
    }
    public function checkIfUserHasEmpresa(){
        try{
            $user = auth()->user();
            if($user->EMPRESA_ID != null ){
                return response()->json(1);
            }else{
                return response()->json(0);
            }
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()]);
        }
    }
    public function getEmpresaByUser(){
        try{
            $user = auth()->user();
            return response()->json([$user->empresa]);
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()]);
        }
    }
    public function profile(){
        try{
            $user = auth()->user();
            return $user;
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()],400);
        }
    }

}
