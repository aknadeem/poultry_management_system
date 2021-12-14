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

    public function store(Request $request)
    {
        $this->validate($request,[
            'name' => 'bail|required|string',
            'guardian_name' => 'bail|required|string',
            'cnic' => 'bail|required|numeric|min:13|max:13|unique:users,cnic',
            'email' => 'bail|required|integer',
            'contact_no' => 'bail|required|integer',
            'country_id' => 'bail|required|integer',
            'province_id' => 'bail|required|integer',
            'city_id' => 'bail|required|integer',
            'address' => 'bail|required|integer',
            'picture' => 'bail|required|integer',
        ]);
    }
}
