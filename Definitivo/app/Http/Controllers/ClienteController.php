<?php

namespace App\Http\Controllers;

use App\Cliente;
use App\Mail\SendMailUser;
use App\Repositories\ClienteRepository;
use Facade\FlareClient\Http\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Tymon\JWTAuth\Facades\JWTAuth;

class ClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ClienteRepository $clienteRepository)
    {
        return $clienteRepository->index();
    }

    public function create()
    {
        //
    }

    public function test(ClienteRepository $clienteRepository){
        return $clienteRepository->test();
    }

    public function store(Request $request, ClienteRepository $clienteRepository)
    {
        return $clienteRepository->store($request);
    }

    public function show($id, ClienteRepository $clienteRepository)
    {
        return $clienteRepository->show($id);
    }

    public function edit($id)
    {
        //
    }

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
