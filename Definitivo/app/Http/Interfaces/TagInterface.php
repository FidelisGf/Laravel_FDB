<?php
namespace App\Http\interfaces;
use Illuminate\Http\Request;

interface TagInterface{
    public function index();
    public function store(Request $request);
}
