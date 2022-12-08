<?php
namespace App\Http\interfaces;
use Illuminate\Http\Request;

interface ResetPwInterface{
    public function sendResetPwEmail(Request $request);
    public function resetPassword(Request $request);
}
