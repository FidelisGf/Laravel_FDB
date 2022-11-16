<?php
namespace App\Http\interfaces;
use Illuminate\Http\Request;

interface DespesaInterface{
    public function index(Request $request);
    public function store(Request $request);
    public function sumDespesasMensais();
    public function despesasByTag($id);
    public function despesaBetweenTwoDates(Request $request);
    public function show($id);
    public function update(Request $request, $id);
    public function destroy($id);
}
