<?php
namespace App\Http\interfaces;
use Illuminate\Http\Request;

interface PenalidadeInterface{
    public function store(Request $request);
}
