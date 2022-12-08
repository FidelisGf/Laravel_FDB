<?php

namespace App\Repositories;

use App\Empresa;
use App\Http\interfaces\UsuarioInterface;
use App\Role;
use App\Usuario;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Stmt\UseUse;
use Tymon\JWTAuth\Facades\JWTAuth;

class UsuarioRepository implements UsuarioInterface
{
    public function __construct()
    {

    }
    public function index(){
        try{
            $empresa = auth()->user()->empresa->ID;
            $usuarios = Usuario::where('EMPRESA_ID', $empresa)->with([
                'role' => function($query){
                    $query->select('ID', 'NOME');
                }
            ])->where('ID_ROLE', '!=', 1)->paginate(15);
            return $usuarios;
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()]);
        }
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
    public function showRolesAvaibles(){
        try{
            $roles = Role::all();
            return $roles;
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
            return response()->json(['level' => $user->role->LEVEL, 'cargo' => $user->role->NOME]) ;
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()],400);
        }
    }
    public function getActiveUsers(){
        try{
            $empresa = auth()->user()->empresa->ID;
            $actives = Usuario::where('EMPRESA_ID', $empresa)->count();
            return $actives;
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()],400);
        }
    }
    public function destroy($id){
        try{
            $usuario = Usuario::FindOrFail($id);
            $usuario->delete();
            return response(['message'=> 'Usuario excluido com sucesso !'],200);
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()],400);
        }
    }
    public function getPenalidades($id){
        try{
            $user = Usuario::FindOrFail($id);
            return $user->penalidades;
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()]);
        }
    }
    public function update($id, Request $request){
        try{

            $user = Usuario::FindOrFail($id);
            $user->update($request->all());

            return response()->json(['message' => 'Usuario Editado com sucesso !']);
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()],400);
        }
    }

    public function show($id){
        try{
            $valorTotalVendas = 0;
            $usuario = Usuario::FindOrFail($id);
            $cargo = $usuario->role->NOME;
            $qntdVendas = $usuario->pedidos->count();
            $valorTotalVendas = floatval( $usuario->pedidos->where('DT_PAGAMENTO', '!=', null)->sum('VALOR_TOTAL'));
            $qntdPenalidades = $usuario->penalidades->count();
            $usuario = $usuario->only('ID', 'NAME', 'EMAIL', 'CPF', 'CREATED_AT', 'UPDATED_AT', 'ID_ROLE');
            return response()->json(['usuario' => $usuario, 'qntdVendas' => $qntdVendas, 'qntdPenalidades'
            =>$qntdPenalidades, 'totalVendido' => $valorTotalVendas, 'cargo' => $cargo],200);
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()],400);
        }
    }
    public function getVendasByUser($id){
        try{
            $user = Usuario::FindOrFail($id);
            return $user->pedidos;
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()],400);
        }
    }
    public function getUserMediaVendasByAno($id){
        $yearStart = date('01-01-Y');
        $yearStart = Carbon::parse($yearStart);
        $yearFinal = date('31-12-Y');
        $yearFinal = Carbon::parse($yearFinal);
        $mediaVendas = floatval(DB::table('PEDIDOS')->whereBetween('PEDIDOS.DT_PAGAMENTO', [$yearStart, $yearFinal])
        ->where('PEDIDOS.ID_USER', $id)
        ->avg('PEDIDOS.VALOR_TOTAL'));
        return response()->json([$mediaVendas]);
    }
}
