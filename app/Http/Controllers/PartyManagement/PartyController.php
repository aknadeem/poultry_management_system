<?php

namespace App\Http\Controllers\PartyManagement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
}
