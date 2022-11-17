<?php
namespace App\Http\interfaces;
use Illuminate\Http\Request;

interface ClienteInterface{
    public function index();
    public function store(Request $request);
    public function show($id);
}
