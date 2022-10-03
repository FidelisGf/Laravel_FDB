<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
class Usuario extends Authenticatable implements JWTSubject
{
    use Notifiable;
    protected $table = 'USERS';
    const CREATED_AT = 'CREATED_AT';
    const UPDATED_AT = 'UPDATED_AT';
    protected $fillable = ['NAME', 'EMAIL', 'PASSWORD', 'EMPRESA_ID'];
    protected $rememberTokenName = 'REMEMBER_TOKEN';
    protected $hidden = ['PASSWORD', 'REMEMBER_TOKEN'];
    protected $primaryKey = 'ID';
    protected $generator = 'GEN_USERS_ID';
    protected $keyType = 'integer';
    public $timestamps = true;
    public function Empresa(){
        return $this->belongsTo(Empresa::class, 'EMPRESA_ID', 'ID');
    }
    public function getJWTCustomClaims()
    {
        return [];
    }
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    protected $casts = [
        'EMAIL_VERIFIED_AT' => 'datetime',
    ];

}
