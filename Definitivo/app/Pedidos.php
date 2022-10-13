<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pedidos extends Model
{
    protected $table = 'PEDIDOS';
    protected $primaryKey = 'ID';
    protected $generator = 'GEN_PEDIDOS_ID';
    protected $keyType = 'integer';
    public $timestamps = false;
    protected $fillable = ['ID', 'PRODUTOS', 'METODO_PAGAMENTO',
                          'ID_EMPRESA', 'VALOR_TOTAL', 'APROVADO'];
}
