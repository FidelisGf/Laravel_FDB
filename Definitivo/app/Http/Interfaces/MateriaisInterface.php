<?php
namespace App\Http\interfaces;
use Illuminate\Http\Request;

interface MateriaisInterface{
    public function index();
    public function adicionaQuantidadeMaterial(Request $request, $id);
    public function removeQuantidadeMaterial($materiais, $quantidade);
    public function store(Request $request);
    public function show($id);
}
