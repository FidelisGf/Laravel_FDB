<?php

namespace App\Http\Controllers;


use App\Repositories\MedidasRepository;
use Illuminate\Http\Request;

class MedidasController extends Controller
{

    public function index(MedidasRepository $medidasRepository)
    {
       return $medidasRepository->index();
    }

    public function create()
    {
        //
    }

    public function store(Request $request, MedidasRepository $medidasRepository)
    {
        return $medidasRepository->store($request);
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
