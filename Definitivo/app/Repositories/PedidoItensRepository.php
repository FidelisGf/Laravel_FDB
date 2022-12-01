<?php

namespace App\Repositories;

use App\Pedido_Itens;

class PedidoItensRepository
{
    public function __construct()
    {
        //
    }
    public function salvaItensPedidos($FakeProducts , $idPedido){
        try{
            foreach($FakeProducts as $FakeProduct){
                $query = Pedido_Itens::where('ID_PEDIDO', $idPedido)->first();
                if($query != null){
                    $query->QUANTIDADE = $FakeProduct->QUANTIDADE;
                    $query->save();
                }else{
                    $itens = new Pedido_Itens();
                    $itens->ID_PRODUTO = $FakeProduct->ID;
                    $itens->QUANTIDADE = $FakeProduct->QUANTIDADE;
                    $itens->ID_PEDIDO = $idPedido;
                    $itens->save();
                }
            }

        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()]);
        }

    }
}
