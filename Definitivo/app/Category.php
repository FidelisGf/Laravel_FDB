<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'CATEGORIAS';


    protected $primaryKey = 'ID_CATEGORIA';
    protected $generator = 'GEN_CATEGORIAS_ID';
    protected $keyType = 'integer';

    public $timestamps = false;

    public function product(){
        return $this->hasMany(Product::class, 'ID_CATEGORIA', 'ID_CATEGORIA');
    }

    public function empresa(){
        return $this->belongsTo(Empresa::class, 'ID_EMPRESA', 'ID_CATEGORIA');
    }

    protected $fillable = ['NOME', 'ID_EMPRESA'];

}
