<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Medidas extends Model
{
    protected $table = 'MEDIDAS';
    protected $primaryKey = 'ID';
    protected $generator = 'GEN_MEDIDAS_ID';
    protected $keyType = 'integer';
    public $timestamps = false;
    protected $fillable = ['ID', 'NOME', 'NOME_REAL', 'ID_EMPRESA'];
}
