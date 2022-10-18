<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Venda extends Model
{
    protected $table = 'VENDAS';
    protected $primaryKey = 'ID';
    protected $generator = 'GEN_VENDAS_ID';
    protected $keyType = 'integer';
    public $timestamps = false;
    protected $fillable = ['ID', 'VALOR_TOTAL', 'ID_PEDIDO'];


    public function pedidos(){
        return $this->belongsTo(Pedidos::class, 'ID_PEDIDO', 'ID');
    }
}
