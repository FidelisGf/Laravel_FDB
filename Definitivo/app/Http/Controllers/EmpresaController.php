<?php

namespace App\Http\Controllers;

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
            $empresas = Empresa::paginate(15);
            return EmpresaResource::collection($empresas);
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
            return response()->json(
                [
                    "message" => "Empresa adicionada com sucesso"
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


    public function allProductsAndCategoryFromEmpresa($id){
        try{
            $PRODUCTS = Empresa::with(['category.product'])->findOrFail($id)->paginate(15);
            return EmpresaResource::collection($PRODUCTS);
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
            return EmpresaResource::collection(Empresa::with(['category:ID_EMPRESA,NOME'])->findOrFail($id)->paginate(15));
            //return EmpresaResource::collection($CATEGORYS);
        }catch(\Exception $e){
            return response()->json(
                [
                    "message" => $e->getMessage()
                ],400
            );
        }
    }


    public function countCategorysFromEmpresa($id){
        try{
            $count = Empresa::findOrFail($id)->withCount('category')->get();
            return $count;
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
     * @param  \App\Empresa  $empresa
     * @return \Illuminate\Http\Response
     */
    public function show(Empresa $empresa)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Empresa  $empresa
     * @return \Illuminate\Http\Response
     */
    public function edit(Empresa $empresa)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Empresa  $empresa
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Empresa $empresa)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Empresa  $empresa
     * @return \Illuminate\Http\Response
     */
    public function destroy(Empresa $empresa)
    {
        //
    }
}
