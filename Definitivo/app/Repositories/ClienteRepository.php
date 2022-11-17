<?php

namespace App\Repositories;

use App\Cliente;
use App\Http\interfaces\ClienteInterface;
use App\Mail\SendMailUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ClienteRepository implements ClienteInterface
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
            $clientes = Cliente::where('ID_EMPRESA', $empresa->ID)->paginate(8);
            return $clientes;
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()],400);
        }
    }
    public function test($id, $cod){
        try{
            $cliente = Cliente::FindOrFail($id);
            Mail::to($cliente->EMAIL)->send(new SendMailUser($cliente, $cod));
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
                'NOME' => ['required', 'max:60', 'min:2'],
                'CPF' => ['required', 'min:11', 'max:11'],
                'ENDERECO'=> ['required', 'min:8'],
                'EMAIL'=> ['required', 'min:12'],
                'TELEFONE' => ['required', 'min:11', 'max:12']
            ]);
            if($validatedData){
                $cliente = new Cliente();
                $NOME_REAL = "$request->NOME _ $empresa->ID";
                $cliente->NOME = $request->NOME;
                $cliente->CPF = $request->CPF;
                $cliente->EMAIL = $request->EMAIL;
                $cliente->ENDERECO = $request->ENDERECO;
                $cliente->TELEFONE = $request->TELEFONE;
                $cliente->ID_EMPRESA = $empresa->ID;
                $cliente->NOME_REAL = $NOME_REAL;
                $cliente->save();
                return $cliente;
            }
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()],400);
        }
    }
    public function show($id)
    {
        try{
            $cliente = Cliente::FindOrFail($id);
            return $cliente;
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()],400);
        }
    }
}
