<?php

namespace App\Repositories;

use App\Mail\SendMailPw;
use App\ResetPw;
use App\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
class ResetPwRepository
{
    public function __construct()
    {
        //
    }
    public function sendResetPwEmail(Request $request){ // criar uma repository para
        try{
            $email = $request->EMAIL;
            $token = (string) Str::uuid();
            $ResetPw = new ResetPw();
            $ResetPw->CODIGO = $token;
            $ResetPw->EMAIL = $email;
            $ResetPw->save();
            Mail::to($email)->send(new SendMailPw($token));
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()],400);
        }
    }
    public function resetPassword(Request $request){
        try{
            $newPw = bcrypt($request->PW);
            $ResetPw = null;
            if($ResetPw = ResetPw::where('CODIGO', $request->token)->first()){
                if($ResetPw->DT_USO == null){
                    $user = Usuario::where('EMAIL', $ResetPw->EMAIL)->first();
                    $user->PASSWORD = $newPw;
                    $user->save();
                    $ResetPw->DT_USO = now()->format('Y-m-d H:i');
                    $ResetPw->save();
                    return response()->json("Senha Alterada com sucesso !", 200);
                }else{
                    return response()->json("Token já utilizado !", 400);
                }
            }else{
                return response()->json("Token Invalido", 400);
            }

        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()],400);
        }
    }
}
