<?php

namespace App\Http\Controllers;

use App\Despesa;
use App\Pedidos;
use App\Product;
use App\Repositories\VendaRepository;
use App\Venda;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;

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
