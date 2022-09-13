<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Empresa extends Model
{
    protected $table = 'EMPRESAS';
    const CREATED_AT = 'CREATED_AT';
    const UPDATED_AT = 'UPDATED_AT';
    const DELETED_AT = 'DELETED_AT';
    use SoftDeletes;

    protected $primaryKey = 'ID_EMPRESA';
    protected $generator = 'GEN_EMPRESAS_ID';
    protected $keyType = 'integer';

    public $timestamps = true;

    protected $fillable = ['NOME', 'CNPJ'];
    protected $dates = ['DELETED_AT'];

    public function category(){
        return $this->hasMany(Category::class, 'ID_EMPRESA', 'ID_EMPRESA');
    }

    public function product(){
        return $this->hasManyThrough(Product::class, Category::class, "ID_EMPRESA", "ID_CATEGORIA", "ID_EMPRESA");
    }

}
