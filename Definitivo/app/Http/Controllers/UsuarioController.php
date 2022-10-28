<?php

namespace App\Http\Controllers;

use App\Empresa;
use App\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth as FacadesJWTAuth;
use Tymon\JWTAuth\JWT;
use Tymon\JWTAuth\JWTAuth;

class UsuarioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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

    public function vinculaUsuarioEmpresa(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'NOME' => 'required|unique:EMPRESAS|max:50|min:2',
                'CNPJ' => 'required|unique:EMPRESAS|max:14|min:14',
                'EMAIL'=> 'required|unique:EMPRESAS|email|max:120|min:5',
                'NOME_FANTASIA' => 'required|max:60|min:2',
                'ENDERECO' => 'required|max:255|min:2',
                'INC_ESTADUAL' => 'required|unique:EMPRESAS|max:12|min:9',
            ]);
            if(!$validator->fails()){
                $Empresa = Empresa::create($request->all());
                if($Empresa){
                    $auth = FacadesJWTAuth::parseToken()->authenticate();
                    $USER = $auth;
                    $USER->EMPRESA_ID = $Empresa->ID;
                    if($USER->save()){
                        return response()->json(['message' => 'Usuario e Empresa vinculados com sucesso !' + $USER]);
                    }else{
                        return response()->json(['message' => 'Falha ao vincular Usuario e Empresa']);
                    }
                }
            }
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()]);
        }
    }
    public function checkIfUserHasEmpresa(){
        try{
            $user = auth()->user();
            if($user->EMPRESA_ID != null ){
                return response()->json([1]);
            }else{
                return response()->json([0]);
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
