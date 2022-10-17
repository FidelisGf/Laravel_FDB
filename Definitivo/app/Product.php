<?php

namespace App;

use Firebird\Eloquent\Model as EloquentModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    protected $table = 'PRODUCTS';
    const CREATED_AT = 'CREATED_AT';
    const UPDATED_AT = 'UPDATED_AT';
    const DELETED_AT = 'DELETED_AT';
    use SoftDeletes;
    protected $primaryKey = 'ID_PRODUTO';
    protected $generator = 'GEN_PRODUCTS_ID';
    protected $keyType = 'integer';

    public function category(){
        return $this->belongsTo(Category::class, 'ID_CATEGORIA', 'ID_CATEGORIA');
    }
    public function estoque(){
        return $this->belongsTo(Estoque::class, 'PRODUCT_ID', 'ID_PRODUTO');
    }
    public $timestamps = true;

    protected $fillable = ['NOME', 'DESC', 'VALOR', 'ID_CATEGORIA'];
    protected $dates = ['DELETED_AT'];




}
