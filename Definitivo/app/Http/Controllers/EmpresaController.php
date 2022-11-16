<?php

namespace App\Http\Controllers;

use App\Category;
use App\Empresa;
use App\Http\Requests\EmpresaRequest;
use App\Http\Resources\EmpresaResource;
use App\Repositories\EmpresaRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator as FacadesValidator;
use Illuminate\Validation\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class EmpresaController extends Controller
{

    public function index(EmpresaRepository $empresaRepository)
    {
        return $empresaRepository->index();
    }

    public function getEmpresaFromUser(EmpresaRepository $empresaRepository){
        return $empresaRepository->getEmpresaFromUser();
    }

    public function create()
    {
        //
    }

    public function store(Request $request, EmpresaRepository $empresaRepository)
    {
        return $empresaRepository->store($request);
    }

    public function show($id, EmpresaRepository $empresaRepository)
    {
        return $empresaRepository->show($id);
    }

    public function edit($id, EmpresaRepository $empresaRepository)
    {
        return $empresaRepository->edit($id);
    }

    public function update(Request $request, $id, EmpresaRepository $empresaRepository)
    {
        return $empresaRepository->update($request, $id);

    }

    public function destroy($id, EmpresaRepository $empresaRepository)
    {
        return $empresaRepository->destroy($id);
    }




}
