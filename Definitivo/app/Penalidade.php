<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Penalidade extends Model
{
    protected $table = 'PENALIDADES';
    protected $primaryKey = 'ID';
    protected $generator = 'GEN_PENALIDADES_ID';
    protected $keyType = 'integer';
    public $timestamps = false;
    protected $fillable = ['ID', 'TIPO', 'ID_USER', 'DATA', 'DESC'];
    public function user(){
        return $this->belongsToMany(Usuario::class, 'ID', 'ID');
    }
}
