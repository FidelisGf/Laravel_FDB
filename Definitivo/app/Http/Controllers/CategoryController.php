<?php

namespace App\Http\Controllers;

use App\Category;
use App\Http\Resources\CategoryResource;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $user = JWTAuth::parseToken()->authenticate();
            $empresa = $user->empresa;
            $Category = Category::where('ID_EMPRESA', $empresa->ID)->paginate(30);
            return $Category;
        }catch(\Exception $e){
            return response()->json(
                [
                    "message" => $e->getMessage()
                ]
                );
        }

    }



    public function findCategoryWithProductsIn(){
        try{
            return Category::whereHas('product')->paginate(15);
        }catch(\Exception $e){
            return response()->json(
                [
                    "message" => $e->getMessage()
                ]
            );
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
        try{
            $user = auth()->user();
            $empresa = $user->empresa;
            $Category = new Category();
            $NOME_REAL = "$request->NOME_C _ $empresa->ID";
            $Category->ID_EMPRESA = $empresa->ID;
            $Category->NOME_C = $request->NOME_C;
            $Category->NOME_REAL = $NOME_REAL;
            if($Category->save()){
                return $Category;
            }
        }catch(\Exception $e){
           return response()->json(
            [
                "message" => $e->getMessage()
            ]
            );
        }
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try{
            $category = Category::FindOrFail($id);
            return new CategoryResource($category);
        }catch(\Exception $e){
            return response()->json([
                "message" => $e->getMessage()
            ],400);
        }
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category)
    {
        try{
            $Category = Category::FindOrFail($category->ID_CATEGORIA);
            $Category->update($request->all());
            return response()->json(
                [
                    "message" => "Categoria editada com sucesso"
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
    public function CategoryMostExpansiveProduct($id){
        try{
            $PRODUCTS = Category::FindOrFail($id)->product->max('VALOR');
            return response()->json($PRODUCTS);
        }catch(\Exception $e){
            return response()->json(
                [
                    "message" => $e->getMessage()
                ],400
            );
        }
    }
    public function CategoryAVGProductPrice($id){
        try{
            $PRODUCTS = Category::FindOrFail($id)->product->avg('VALOR');
            return response()->json($PRODUCTS);
        }catch(\Exception $e){
            return response()->json(
                [
                    "message" => $e->getMessage()
                ],400
            );
        }
    }
    public function CategoryMinProductPrice($id){
        try{
            $PRODUCTS = Category::FindOrFail($id)->product->min('VALOR');
            return response()->json($PRODUCTS);
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
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        //
    }
}
