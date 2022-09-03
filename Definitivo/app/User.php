<?php

namespace App;

use Firebird\Eloquent\Model;

class User extends Model
{
    protected $table = 'TESTE';


    protected $primaryKey = 'NOME';

    protected $keyType = 'string';


    public $timestamps = false;


    protected $fillable = ['NOME'];
}
