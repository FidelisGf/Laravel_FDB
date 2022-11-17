<?php
namespace App\Http\interfaces;
use Illuminate\Http\Request;

interface CategoryInterface{
    public function index();
    public function store(Request $request);
    public function findCategoryWithProductsIn();
    public function show($id);
    public function update(Request $request, $id);
}
