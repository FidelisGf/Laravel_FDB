<?php

namespace App\Http\Controllers;

use App\Category;
use App\Empresa;
use App\Http\Resources\EmpresaResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmpresaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $EMPRESAS = Empresa::paginate(3)->toArray();
            return $EMPRESAS;
            //return response()->json(Empresa::all());
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
        try{
            $Empresa = new Empresa();
            $validatedData = $request->validate([
                'NOME' => ['required', 'unique:EMPRESAS', 'max:50', 'min:2'],
                'CNPJ' => ['required', 'unique:EMPRESAS', 'max:14', 'min:14']
            ]);
            if($validatedData){
                $Empresa->NOME = $request->NOME;
                $Empresa->CNPJ = $request->CNPJ;
                if($Empresa->save()){
                    return response()->json([
                        "message" => "Empresa criada com sucesso"
                    ],200);
                }else{
                    return false;

                }
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
            return Empresa::where('ID',$id)->first();

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
        try{
            return Empresa::where('ID',$id)->first();
        }catch(\Exception $e){
            return response()->json(
                [
                    "message" => $e->getMessage()
                ],400
            );
        }
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
        try{
            $Empresa = Empresa::findOrFail($id);
            $Empresa->update($request->all());
            return response()->json(
                [
                    "message" => "Empresa editada com sucesso"
                ],200
                );
        }catch(\Exception $e){
            return response()->json(
                [
                    "message" => $e->getMessage()
                ],400
                );
        }

    }
    public function filterByAscName(Empresa $empresa){
        try{
            return $empresa::orderBy('NOME', 'asc')->paginate(3)->toArray();
        }catch(\Exception $e){
            return response()->json(
                [
                    "message" => $e->getMessage()
                ],400
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
            Empresa::findOrFail($id)->delete();
            return response()->json([
                "message" => "Empresa Deletada com sucesso !"
            ]);
        }catch(\Exception $e){
            return response()->json(
                [
                    "message" => $e->getMessage()
                ],200
            );
        }
    }
    public function allProductsFromEmpresa($id){
        try{
            return Empresa::findOrFail($id)->product->first();
        }catch(\Exception $e){
            return response()->json(
                [
                    "message" => $e->getMessage()
                ],400
            );
        }
    }

    public function allCategoryFromEmpresa($id){
        try{
            return Empresa::where('ID',$id)->with(['category'])->first();
        }catch(\Exception $e){
            return response()->json(
                [
                    "message" => $e->getMessage()
                ],400
            );
        }
    }

    protected $appends = ['categorysCount'];
    public function countCategorysFromEmpresa($id){
        try{
            return Empresa::findOrFail($id)->withCount('category')->get();

        }catch(\Exception $e){
            return response()->json(
                [
                    "message" => $e->getMessage()
                ],400
            );
        }

    }

    public function applyFilter(Request $request){
        try{
            $query = Empresa::query();
            if($request->filled('search')){
                $search = $request->search;
                $search = ucwords($search);
                $query->where('NOME', 'LIKE', "%{$search}%")->orWhere('CNPJ', 'LIKE', "%{$search}%");
            }

            if($request->filled('selected')){
                $selected = $request->selected;
                switch($selected){
                    case 'BYNAME':
                        $query->orderBy('NOME', 'asc');
                        break;
                }
            }
            return $query->paginate(3);
        }catch(\Exception $e){
            return response()->json(
                [
                    "message" => $e->getMessage()
                ],400
            );
        }
    }
    public function autoCompleteEmpresa(Request $request){
        try{
            $search = $request->search;
            $search = ucwords($search);
            $Empresas = Empresa::where('NOME', 'LIKE', "%{$search}%")->get();
            return response()->json($Empresas);
        }catch(\Exception $e){
            return response()->json(
                [
                    "message" => $e->getMessage()
                ],400
            );
        }
    }



}
