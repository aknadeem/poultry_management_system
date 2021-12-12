<?php

namespace App\Http\Controllers\PartyManagement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ConductPersonController extends Controller
{
    public function index()
    {
        return view('partymanagement.conductperson.index');
    }
    
    public function create()
    {
        return view('partymanagement.conductperson.create');
    }
}
