<?php

namespace App\Repositories;

use App\Http\interfaces\PenalidadeInterface;
use App\Penalidade;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PenalidadeRepository implements PenalidadeInterface
{
    public function __construct()
    {

    }

    public function store(Request $request){
        try{
            $validator = $request->validate([
                'TIPO' => 'required|max:20|min:5',
                'DESC' => 'required|max:120|min:0',
                'DATA'=> 'required',
            ]);
            if($validator){
                $penalidade = new Penalidade();
                $penalidade->DATA = Carbon::parse($request->DATA);
                $penalidade->TIPO = $request->TIPO;
                $penalidade->DESC = $request->DESC;
                $penalidade->ID_USER = $request->ID_USER;
                $penalidade->save();
                return response()->json(['message' => 'Penalidade registrada com sucesso !']);
            }
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()]);
        }
    }
}
