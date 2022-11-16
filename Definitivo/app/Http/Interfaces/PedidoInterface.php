<?php
namespace App\Http\interfaces;
use Illuminate\Http\Request;

interface PedidoInterface{
    public function index(Request $request);
    public function store(Request $request);
    public function checkQuantidadeProduto(Request $request);
    public function show($id);
    public function aprovarPedido($id);
    public function update(Request $request, $id);
    public function pedidosPorPeriodo(Request $request);
}
