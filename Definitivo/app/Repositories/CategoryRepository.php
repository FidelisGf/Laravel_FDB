<?php

namespace App\Repositories;

use App\Category;
use App\Events\MakeLog;
use App\Http\interfaces\CategoryInterface;
use App\Http\Requests\StoreCategoryValidator;
use Illuminate\Http\Request;

class CategoryRepository implements CategoryInterface //Precisa de refatoração
{
    private $model;
    public function __construct(Category $model)
    {
        $this->model = $model;
    }
    public function index()
    {
        try{
            $user = auth()->user();
            $empresa = $user->empresa;
            $Category = Category::where('ID_EMPRESA', $empresa->ID)->get();
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
            return Category::whereHas('product')
            ->paginate(15);
        }catch(\Exception $e){
            return response()->json(
                [
                    "message" => $e->getMessage()
                ]
            );
        }
    }
    public function store(StoreCategoryValidator $request)
    {
        try{
            $user = auth()->user();
            $empresa = $user->empresa;
            $Category = new Category();
            $validator = $request->validated();
            if($validator){
                $NOME_REAL = "$request->NOME_C _ $empresa->ID";
                $Category->ID_EMPRESA = $empresa->ID;
                $Category->NOME_C = $request->NOME_C;
                $Category->NOME_REAL = $NOME_REAL;
                if($Category->save()){

                    event(new MakeLog("Categorias", "", "insert",
                    "$Category->NOME_C", "", $Category->ID_CATEGORIA, $empresa->ID, $user->ID));

                    return response()->json(["message" => "Categoria cadastrada com sucesso !"]);
                }
                event(new MakeLog("Categorias", "", "insert", json_encode($Category), "",
                $Category->ID, $empresa->ID, $user->ID));
            }
        }catch(\Exception $e){
           return response()->json(
            [
                "message" => $e->getMessage()
            ],400
            );
        }
    }
    public function show($id)
    {
        try{
            $category = Category::FindOrFail($id);
            return response()->json($category);
        }catch(\Exception $e){
            return response()->json([
                "message" => $e->getMessage()
            ],400);
        }
    }
    public function update(Request $request, $id)
    {
        try{
            $Category = Category::FindOrFail($id);
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
}
