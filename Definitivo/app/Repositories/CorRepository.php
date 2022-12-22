<?php

namespace App\Repositories;

use App\Cor;

class CorRepository
{
    public function __construct()
    {
        //
    }
    public function index(){
        try{
            $cores = Cor::all();
            return $cores;
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()]);
        }
    }
}
