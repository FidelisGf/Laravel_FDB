<?php

namespace App\Http\Controllers;

use App\Repositories\TagRepository;
use App\Tag;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class TagController extends Controller
{

    public function index(TagRepository $tagRepository)
    {
        return $tagRepository->index();
    }
    public function store(Request $request, TagRepository $tagRepository)
    {
       return $tagRepository->store($request);
    }
    public function show($id)
    {
        //
    }
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
