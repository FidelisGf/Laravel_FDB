<?php
namespace App\Http\interfaces;
use Illuminate\Http\Request;

interface UsuarioInterface{
    public function index();
    public function destroy($id);
    public function vinculaUsuarioEmpresa(Request $request);
    public function checkIfUserHasEmpresa();
    public function getEmpresaByUser();
    public function profile();
    public function getActiveUsers();
}
