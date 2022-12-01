<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pedido_Itens extends Model
{
    protected $table = 'PEDIDOS_ITENS';
    protected $primaryKey = 'ID';
    protected $generator = 'GEN_PEDIDOS_ITENS_ID';
    protected $keyType = 'integer';
    public $timestamps = false;
    protected $fillable = ['ID', 'ID_PRODUTO', 'ID_PEDIDO', 'QUANTIDADE'];
}
