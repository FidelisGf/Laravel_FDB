<?php

namespace App\Repositories;

use App\Http\Controllers\Help;
use App\Http\interfaces\TagInterface;
use App\Tag;
use Illuminate\Http\Request;

class TagRepository implements TagInterface
{
    private $model;
    public function __construct(Tag $model)
    {
        $this->model = $model;
    }
    public function index(){
        try{
            $user = auth()->user();
            $empresa = $user->empresa;
            $tags = Tag::where('ID_EMPRESA', $empresa->ID)->get();
            return $tags;
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()]);
        }
    }
    public function store(Request $request){
        $helper = new Help();
        try{
            $user = auth()->user();
            $empresa = $user->empresa;
            $validatedData = $request->validate([
                'NOME' => ['required', 'max:100', 'min:2']
            ]);
            if($validatedData){
                $tag = new Tag();
                $NOME_REAL = "$request->NOME _ $empresa->ID";
                $tag->NOME = $request->NOME;
                $tag->ID_EMPRESA = $empresa->ID;
                $tag->NOME_REAL = $NOME_REAL;
                $helper->startTransaction();
                $tag->save();
                $helper->commit();
                return $tag;
            }
        }catch(\Exception $e){
            $helper->rollbackTransaction();
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
