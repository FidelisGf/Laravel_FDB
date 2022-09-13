<?php

namespace App;

use Firebird\Eloquent\Model as EloquentModel;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'PRODUCTS';
    const CREATED_AT = 'CREATED_AT';
    const UPDATED_AT = 'UPDATED_AT';


    protected $primaryKey = 'ID_PRODUTO';
    protected $generator = 'GEN_PRODUCTS_ID';
    protected $keyType = 'integer';
    public function Category(){
        return $this->belongsTo(Category::class, 'ID_CATEGORIA', 'ID_PRODUTO');
    }



    public $timestamps = true;




    protected $fillable = ['NOME', 'DESC', 'VALOR', 'ID_CATEGORIA'];




}
