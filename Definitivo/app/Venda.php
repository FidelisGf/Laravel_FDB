<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Venda extends Model
{
    protected $table = 'VENDAS';
    protected $primaryKey = 'ID';
    protected $generator = 'GEN_PEDIDOS_ID';
    protected $keyType = 'integer';
    public $timestamps = false;
    protected $fillable = ['ID', 'PRODUTOS', 'METODO_PAGAMENTO',
                          'ID_EMPRESA', 'VALOR_TOTAL', 'APROVADO'];
}
