<?php

namespace App\Http\Controllers;


use App\Repositories\UsuarioRepository;
use Illuminate\Http\Request;


class UsuarioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(UsuarioRepository $usuarioRepository)
    {
        return $usuarioRepository->index();
    }
    public function destroy($id, UsuarioRepository $usuarioRepository){
        return $usuarioRepository->destroy($id);
    }
    public function show($id, UsuarioRepository $usuarioRepository){
        return $usuarioRepository->show($id);
    }

    public function create()
    {
        //
    }

    public function vinculaUsuarioEmpresa(Request $request, UsuarioRepository $usuarioRepository){
        return $usuarioRepository->vinculaUsuarioEmpresa($request);
    }
    public function checkIfUserHasEmpresa(UsuarioRepository $usuarioRepository){
        return $usuarioRepository->checkIfUserHasEmpresa();
    }
    public function getEmpresaByUser(UsuarioRepository $usuarioRepository){
        return $usuarioRepository->getEmpresaByUser();
    }
    public function profile(UsuarioRepository $usuarioRepository){
        return $usuarioRepository->profile();
    }
    public function showAvalibleRoles(UsuarioRepository $usuarioRepository){
        return $usuarioRepository->showRolesAvaibles();
    }
    public function getActiveUsers(UsuarioRepository $usuarioRepository){
        return $usuarioRepository->getActiveUsers();
    }
}
