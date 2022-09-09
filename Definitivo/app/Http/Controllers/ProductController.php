<?php

namespace App\Http\Controllers;

use App\Category;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductResource;
use App\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $PRODUCTS = Product::paginate(15);
        return ProductResource::collection($PRODUCTS);
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
            Product::create($request->all());
            return response()->json(
                [
                    "message" => "Produto criado com sucesso"
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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try{
            $PRODUCT = Product::findOrFail($id);
            return new ProductResource($PRODUCT);
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
            $PRODUCT = Product::findOrFail($id);
            $PRODUCT->update($request->all());
            return response()->json(
                [
                    "message" => "Produto editado com sucesso"
                ]
                );
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
            $PRODUCT = Product::findOrFail($id);
            $PRODUCT->delete();
            return response()->json(
                [
                    "message" => "Produto excluido com sucesso !"
                ]
                );
        }catch(\Exception $e){
            return response()->json(
                [
                    "message" => $e->getMessage()
                ]
                );
        }
    }

    public function filterByLowestValue(){
        try{
            $PRODUCT = Product::orderBy('VALOR', 'asc')->paginate(15);
            return ProductResource::collection($PRODUCT);

        }catch(\Exception $e){
            return response()->json(
                [
                    "message" => $e->getMessage()
                ]
                );
        }
    }
    public function filterByExpansiveValue(){
        try{
            $PRODUCTS = Product::orderBy('VALOR', 'desc')->paginate(15);
            return ProductResource::collection($PRODUCTS);
        }catch(\Exception $e){
            return response()->json(
                [
                    "message" => $e->getMessage()
                ]
            );
        }
    }
    //%{$searchTerm}%
    public function search(Request $request){
        try{
            $search = $request->search;
            $search = ucwords($search);
            $PRODUCTS = Product::query()->where('NOME', 'LIKE', '%'. $search.'%')
                                ->orWhere('DESC', 'LIKE', '%'.$search.'%')->paginate(15);
            return ProductResource::collection($PRODUCTS);
        }catch(\Exception $e){
            return response()->json(
                [
                    "message" => $e->getMessage()
                ]
                );
        }
    }
    //ucwords($bar);
    public function bringAllProductsFromCategory(Request $request){
        try{
            $NOME = $request->input('NOME');
            $NOME = ucwords($NOME);
            $CATEGORY = Category::where('NOME', '=', $NOME)->first();
            if($CATEGORY != null){
                $PRODUCTS = Product::where('ID_CATEGORIA', '=', $CATEGORY->ID_CATEGORIA)->paginate(15);
                return ProductResource::collection($PRODUCTS);

            }else{
                return response()->json([
                    "message" => "Não foi possivel encontrar uma Categoria com esse nome"
                ],404);
            }
        }catch(\Exception $e){
            return response()->json(
                [
                    "message" => $e->getMessage()
                ],400
            );
        }
    }

    public function filters(Request $request){
        switch (true){
            case $request->has('lower'):
                return $this->filterByLowestValue();
                break;

            case $request->has('expansive'):
                return $this->filterByExpansiveValue();
                break;
        }
    }

}
