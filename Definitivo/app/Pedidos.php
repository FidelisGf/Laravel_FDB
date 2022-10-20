<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pedidos extends Model
{

    protected $table = 'PEDIDOS';
    const CREATED_AT = 'CREATED_AT';
    const UPDATED_AT = 'UPDATED_AT';
    const DELETED_AT = 'DELETED_AT';
    use SoftDeletes;
    protected $primaryKey = 'ID';
    protected $generator = 'GEN_PEDIDOS_ID';
    protected $keyType = 'integer';
    public $timestamps = true;
    protected $fillable = ['ID', 'PRODUTOS', 'METODO_PAGAMENTO',
                          'ID_EMPRESA', 'VALOR_TOTAL', 'APROVADO', 'DT_PAGAMENTO'];
    public function empresas(){
        return $this->belongsTo(Empresa::class, 'ID_EMPRESA', 'ID');
    }
    public function vendas(){
        return $this->belongsTo(Venda::class, 'ID_PEDIDO', 'ID');
    }
    protected $dates = ['DELETED_AT'];
}
