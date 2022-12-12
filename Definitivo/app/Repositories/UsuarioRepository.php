<?php

namespace App\Repositories;

use App\Empresa;
use App\Http\interfaces\UsuarioInterface;
use App\Http\Requests\RegisterEmployeeValidator;
use App\Http\Requests\StoreEmpresaValidator;
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
    public function vinculaUsuarioEmpresa(StoreEmpresaValidator $request){
        try{
            $validator = $request->validated();
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
    public function update($id, RegisterEmployeeValidator $request){
        try{
            $validator = $request->validated();
            if($validator){
                $user = Usuario::FindOrFail($id);
                $user->update($request->all());
            }
            return response()->json(['message' => 'Usuario Editado com sucesso !']);
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()],400);
        }
    }

    public function show($id){
        try{
            $valorTotalVendas = 0;
            $usuario = Usuario::FindOrFail($id);
            $cargo = $usuario->role;
            $qntdVendas = $usuario->pedidos->count();
            $valorTotalVendas = floatval( $usuario->pedidos->where('DT_PAGAMENTO', '!=', null)->sum('VALOR_TOTAL'));
            $qntdPenalidades = $usuario->penalidades->count();
            $usuario = $usuario->only('ID', 'NAME', 'EMAIL', 'CPF', 'CREATED_AT', 'UPDATED_AT', 'SALARIO', 'ID_ROLE');
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
    public function getUserTotalVendasByMes($id){
        $monthStart = date('01-M-Y');
        $monthStart = Carbon::parse($monthStart);
        $monthFinal = date('30-M-Y');
        $monthFinal = Carbon::parse($monthFinal);
        $totalVendasMes = floatval(DB::table('PEDIDOS')
        ->whereBetween('PEDIDOS.DT_PAGAMENTO', [$monthStart, $monthFinal])
        ->where('PEDIDOS.ID_USER', $id)
        ->sum('PEDIDOS.VALOR_TOTAL'));
        return response()->json([$totalVendasMes]);
    }
    public function getHistoricoSalarioUser($id){
        try{
            $salarios = DB::table('SALARIOS_CHANGES')
            ->where('ID_USER', $id)
            ->orderBy('SALARIO', 'asc')
            ->select('SALARIO')
            ->get();
            $salariosArray = [];
            foreach($salarios as $s){
                $salariosArray[] = floatval($s->SALARIO);
            }
            return response()->json([$salariosArray]);
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()]);
        }
    }
}
