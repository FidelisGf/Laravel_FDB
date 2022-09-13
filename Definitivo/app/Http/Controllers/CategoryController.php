<?php

namespace App\Http\Controllers;

use App\Category;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductResource;
use App\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

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
            return CategoryResource::collection(Category::paginate(15));
        }catch(\Exception $e){
            return response()->json(
                [
                    "message" => $e->getMessage()
                ]
                );
        }

    }
    public function findAllProductByCategory($id){
        try{
            return Category::findOrFail($id)->product->first()->paginate(15);
        }catch(\Exception $e){
            return response()->json(
                [
                    "message" => $e->getMessage()
                ]
            );
        }
    }


    public function findProductFromCategoryWithInputedValue(Request $request){
        return Category::whereHas('product', function(Builder $query){
            $query->where('VALOR', '>=', 9000);
        })->paginate(15);
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
            Category::create($request->all());
            return response()->json(
                [
                    "message" => "Categoria Criada com sucesso"
                ],200
            );
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
            $category = Category::findOrFail($id);
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
            $Category = Category::findOrFail($category->ID_CATEGORIA);
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
