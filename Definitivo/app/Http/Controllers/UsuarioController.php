<?php

namespace App\Http\Controllers;

use App\Empresa;
use App\Repositories\UsuarioRepository;
use App\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth as FacadesJWTAuth;
use Tymon\JWTAuth\JWT;
use Tymon\JWTAuth\JWTAuth;

class UsuarioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
