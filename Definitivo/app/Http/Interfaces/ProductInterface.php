<?php
namespace App\Http\interfaces;
use Illuminate\Http\Request;

interface ProductInterface{
    public function listAll(Request $request);
    public function filterByLowestValue(Request $request);
    public function filterByExpansiveValue(Request $request);
    public function findAllProductByCategory($id);
    public function show($id);
    public function store(Request $request);
    public function update(Request $request, $id);
    public function destroy($id);
    public function findLucroByProduto($id);
    public function search(Request $request);
    public function descontaQuantidadeMaterial($id, $quantidade);
    public function countProducts();
}
