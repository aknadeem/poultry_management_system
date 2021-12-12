<?php

namespace App\Http\Controllers\PoultryShed;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PoultryShedController extends Controller
{
    public function index()
    {
        return view('shedmanagement.personalsheds.index');
    }

    public function create()
    {
        return view('shedmanagement.personalsheds.create');
    }


    public function store(Request $request)
    {
        //
    }


    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

  
    public function destroy($id)
    {
        //
    }
}
