<?php

namespace App\Http\Controllers;


use App\Repositories\VendaRepository;
use Illuminate\Http\Request;


class VendaController extends Controller
{
    public function index(Request $request, VendaRepository $vendaRepository)
    {
        return $vendaRepository->index($request);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
