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
    public function showAvalibleRoles(UsuarioRepository $usuarioRepository){
        return $usuarioRepository->showRolesAvaibles();
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
