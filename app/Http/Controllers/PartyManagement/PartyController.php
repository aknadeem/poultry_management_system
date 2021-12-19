<?php

namespace App\Http\Controllers\PartyManagement;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PartyController extends Controller
{
    public function index()
    {
        return view('partymanagement.party.index');
    }


    public function create()
    {
        return view('partymanagement.party.create');
    }

    public function store(Request $request)
    {
        # code...
    }
}
