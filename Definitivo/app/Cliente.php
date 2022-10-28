<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table = 'CLIENTES';
    protected $primaryKey = 'ID';
    protected $generator = 'GEN_CLIENTES_ID';
    protected $keyType = 'integer';
    public $timestamps = false;
    protected $fillable = ['ID', 'NOME', 'CPF', 'ENDERECO', 'TELEFONE', 'NOME_REAL', 'ID_EMPRESA'];


    public function pedido(){
        return $this->belongsToMany(Pedidos::class, 'ID_CLIENTE', 'ID');
    }
    public function empresa(){
        return $this->belongsTo(Empresa::class, 'ID_EMPRESA', 'ID');
    }
}
