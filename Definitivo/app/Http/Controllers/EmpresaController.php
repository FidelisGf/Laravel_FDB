<?php

namespace App\Http\Controllers;

use App\Category;
use App\Empresa;
use App\Http\Resources\EmpresaResource;
use Illuminate\Http\Request;

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
            Empresa::create($request->all());
            return response()->json([
                "message" => "Empresa criada com sucesso"
            ],200);
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
            return Empresa::where('ID_EMPRESA',$id)->first();

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
            return Empresa::where('ID_EMPRESA',$id)->with(['category'])->first();
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

    public function applyFilter(Empresa $empresa, $filtro){
        try{
            switch($filtro){
                case 'ASC':
                    $this->filterByAscName($empresa);
                    break;
            }
        }catch(\Exception $e){
            return response()->json(
                [
                    "message" => $e->getMessage()
                ],400
            );
        }
    }
    public function search(Request $request){
        try{
            $search = $request->search;
            $search = ucwords($search);
            $Empresa = Empresa::query()->where('NOME', 'LIKE', '%'. $search.'%')
                                ->orWhere('CNPJ', 'LIKE', '%'.$search.'%')->paginate(3);
            if($request->filled('selected')){

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
