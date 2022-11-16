<?php
namespace App\Http\interfaces;
use Illuminate\Http\Request;

interface MedidasInterface{
    public function index();
    public function store(Request $request);
}
