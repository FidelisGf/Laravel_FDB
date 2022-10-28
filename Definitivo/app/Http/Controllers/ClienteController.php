<?php

namespace App\Http\Controllers;

use App\Cliente;
use App\Mail\SendMailUser;
use Facade\FlareClient\Http\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Tymon\JWTAuth\Facades\JWTAuth;

class ClienteController extends Controller
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
            $clientes = Cliente::where('ID_EMPRESA', $empresa->ID)->paginate(8);
            return $clientes;
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()],400);
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

    public function test(){
        $to = "jpinformaticafoz@gmail.com";
        Mail::to($to)->send(new SendMailUser(auth()->user()));
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
            $user = auth()->user();
            $empresa = $user->empresa;
            $validatedData = $request->validate([
                'NOME' => ['required', 'max:60', 'min:2'],
                'CPF' => ['required', 'min:11', 'max:11'],
                'ENDERECO'=> ['required', 'min:8'],
                'TELEFONE' => ['required', 'min:11', 'max:12']
            ]);
            if($validatedData){
                $cliente = new Cliente();
                $NOME_REAL = "$request->NOME _ $empresa->ID";
                $cliente->NOME = $request->NOME;
                $cliente->CPF = $request->CPF;
                $cliente->ENDERECO = $request->ENDERECO;
                $cliente->TELEFONE = $request->TELEFONE;
                $cliente->ID_EMPRESA = $empresa->ID;
                $cliente->NOME_REAL = $NOME_REAL;
                $helper->startTransaction();
                $cliente->save();
                $helper->commit();
                return $cliente;
            }
        }catch(\Exception $e){
            $helper->rollbackTransaction();
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
            $cliente = Cliente::FindOrFail($id);
            return $cliente;
        }catch(\Exception $e){
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
