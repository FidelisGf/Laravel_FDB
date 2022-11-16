<?php

namespace App\Http\Controllers;

use App\Despesa;
use App\Repositories\DespesaRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class DespesaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, DespesaRepository $despesaRepository)
    {
        return $despesaRepository->index($request);
    }


    public function create()
    {
        //
    }

    public function sumDespesasMensais(DespesaRepository $despesaRepository){
        return $despesaRepository->sumDespesasMensais();
    }
    public function store(Request $request, DespesaRepository $despesaRepository)
    {
        return $despesaRepository->store($request);
    }

    public function despesasByTag($id, DespesaRepository $despesaRepository){
        return $despesaRepository->despesasByTag($id);
    }

    public function show($id,DespesaRepository $despesaRepository)
    {
       return $despesaRepository->show($id);
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id, DespesaRepository $despesaRepository)
    {
        return $despesaRepository->update($request, $id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, DespesaRepository $despesaRepository)
    {
        return $despesaRepository->destroy($id);
    }
}
