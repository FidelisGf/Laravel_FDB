<?php

namespace App;


use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'TEST';


    protected $primaryKey = 'NOME';

    protected $keyType = 'string';


    public $timestamps = false;


    protected $fillable = ['NOME'];
}
