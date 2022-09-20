<?php

namespace App\Http\Controllers;


use App\Usuario;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Contracts\Providers\Auth as ProvidersAuth;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register', 'profile', 'logout', 'validateTokn']]);
    }
    public function register(Request $request){

        $user = Usuario::create(array_merge(
            $request->all(),
            ['PASSWORD' => bcrypt($request->PASSWORD)]// 'criptografa' a senha antes de inserir no banco
        ));
        return response()->json([
            'message' => 'Usuario registrado',
            'Usuario' => $user
        ]);
    }
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        try{
            $Usuario = Usuario::where('NAME', $request->NAME)->first();
            if($Usuario){
                if(Hash::check($request->PASSWORD, $Usuario->PASSWORD )){
                    if ($token = auth()->guard('api')->login($Usuario)) {
                        return $this->respondWithToken($token);
                    }
                }else{
                    return response()->json(['message' => 'Senha Não confere !'],400);
                }
            }else{
                return response()->json(['message' => 'Usuario Não encontrado !'],404);
             }
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()],404);
        }

    }
    public function profile(Request $request) {
        return response()->json(Auth::user());
    }

    public function validateTokn(){
        return response()->json([ 'valid' => auth()->check()]);
    }

    public function logout()
    {
        Auth::logout();
        return response()->json(['message' => 'Successfully logged out']);
    }
    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->guard('api')->refresh());
    }
    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->guard('api')->factory()->getTTL() * 60
        ]);
    }
}
