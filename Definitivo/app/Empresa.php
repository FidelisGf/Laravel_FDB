<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    protected $table = 'EMPRESAS';


    protected $primaryKey = 'ID_EMPRESA';
    protected $generator = 'GEN_EMPRESAS_ID';
    protected $keyType = 'integer';

    public $timestamps = false;

    protected $fillable = ['NOME', 'CNPJ'];


    public function category(){
        return $this->hasMany(Category::class, 'ID_EMPRESA', 'ID_EMPRESA');
    }

    public function product(){
        return $this->hasManyThrough(Product::class, Category::class, "ID_CATEGORIA", "ID_EMPRESA", "ID_EMPRESA");
    }

}
