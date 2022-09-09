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

    protected $fillable = ['NOME'];

}
