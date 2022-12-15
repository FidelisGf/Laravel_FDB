<?php

namespace App\Repositories;

use App\Config_General;
use GuzzleHttp\Psr7\Request;

class ConfigGeneralRepository
{
    public function __construct()
    {
        //
    }
    public function setConfig(Request $request){
        try{
            $empresa = auth()->user()->empresa;
            $config = Config_General::where('NOME', '=', $request->NOME)->first();
            if($config ===null){
                $config = new Config_General();
                $config->NOME = $request->NOME;
                $config->PARAMETRO = $request->PARAMETRO;
            }
            $config->ESTADO = $request->ESTADO;
            $config->ID_EMPRESA = $empresa->ID;
            $config->save();
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()]);
        }
    }
    public function getConfig($nome){
        try{
            $config = Config_General::where('NOME', '=', $nome)->first();
            if($config->ESTADO == "T"){
                return $config->PARAMETRO;
            }else{
                return false;
            }
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()]);
        }
    }
}
