<?php

namespace App;

use Firebird\Eloquent\Model as EloquentModel;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'PRODUCTS';


    protected $primaryKey = 'ID_PRODUTO';
    protected $generator = 'GEN_PRODUCTS_ID';
    protected $keyType = 'integer';

    public $timestamps = false;


    protected $fillable = ['NOME', 'DESC', 'VALOR'];
}
