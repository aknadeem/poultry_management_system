<?php

namespace App\Http\Controllers\PartyManagement;

use App\Models\Country;
use Illuminate\Http\Request;
use App\Models\ConductPerson;
use App\Http\Controllers\Controller;
use Session;

class ConductPersonController extends Controller
{

    private $auth_user_id;
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->auth_user_id= \Auth::user()->id;
            return $next($request);
        });
    }

    public function index()
    {
        $conduct_people = ConductPerson::with('country:id,name','province:id,name', 'city:id,name')->get();
        return view('partymanagement.conductperson.index', compact('conduct_people'));
    }
    
    public function create()
    {
        $conductperson = new ConductPerson();

        $countries = Country::with('provinces:id,name,country_id',
        'provinces.cities:id,name,province_id')->get(['id','name']);
        // dd($countries->toArray());

        return view('partymanagement.conductperson.create', compact('conductperson', 'countries'));
    }
    
    public function edit($id)
    {
        $conductperson = ConductPerson::with('province:id,name', 'city:id,name')->find($id);

        $countries = Country::with('provinces:id,name,country_id',
        'provinces.cities:id,name,province_id')->get(['id','name']);

        return view('partymanagement.conductperson.create', compact('conductperson', 'countries'));
    }

    public function store(Request $request)
    {
        $id = null;
        $this->validationRules($request, $id);

        $path = 'conduct_persons/';
        if ($request->hasFile('image_file')) {
            $image_file = $request->file('image_file');
            $extension = $request->file('image_file')->extension();
            $imageName = time().mt_rand(10,99).'.'.$extension;
            // $upload_to_folder = $image_file->storeAs($path, $imageName, 'public');
        }else{
            $imageName = null;
        }
        $message = 'Data created successfully!';
        $title = 'Saved';
        $icon_type = 'success';

        try{
            $save = ConductPerson::create([
                'name' => $request->name,
                'guardian_name' => $request->guardian_name,
                'cnic_no' => $request->cnic_no,
                'email' => $request->email,
                'contact_number' => $request->contact_number,
                'country_id' => $request->country_id,
                'province_id' => $request->province_id,
                'city_id' => $request->city_id,
                'address' => $request->address,
                'picture' => $imageName,
                'addedby' => $this->auth_user_id,
            ]);

            if($save){
                if($imageName){
                    $upload_to_folder = $image_file->storeAs($path, $imageName, 'public');
                }
            }
        }
        catch (\Throwable $e) {
            // return $e;
            $message = 'Something went wrong';
            $title = 'Error';
            $icon_type = 'warning';
        }

        Session::flash('swal_notification', ['title' => $title, 'icon_type' => $icon_type, 'message' => $message]);
        return redirect()->route('conductpersons.index');
    }
    
    
    public function update(Request $request, $id)
    {
        $this->validationRules($request, $id);
        
        $path = 'conduct_persons/';
        $conduct_person_data = ConductPerson::find($id);
        if ($request->hasFile('image_file')) {
            if($conduct_person_data?->picture != null && \Storage::disk('public')->exists('conduct_persons/'.$conduct_person_data?->picture)){
                \Storage::disk('public')->delete('conduct_persons/'.$conduct_person_data?->picture);
            }
            $image_file = $request->file('image_file');
            $extension = $request->file('image_file')->extension();
            $imageName = time().mt_rand(10,99).'.'.$extension;
            // $upload_to_folder = $image_file->storeAs($path, $imageName, 'public');
        }else{
            $imageName = null;
        }
        $message = 'Data Updated successfully!';
        $title = 'Saved';
        $icon_type = 'success';

        try{
            $update_data = $conduct_person_data->update([
                'name' => $request->name,
                'guardian_name' => $request->guardian_name,
                'cnic_no' => $request->cnic_no,
                'email' => $request->email,
                'contact_number' => $request->contact_number,
                'country_id' => $request->country_id,
                'province_id' => $request->province_id,
                'city_id' => $request->city_id,
                'address' => $request->address,
                'picture' => $imageName,
                'updatedby' => $this->auth_user_id,
            ]);

            if($update_data){
                if($imageName){
                    $upload_to_folder = $image_file->storeAs($path, $imageName, 'public');
                }
            }
        }
        catch (\Throwable $e) {
            // return $e;
            $message = 'Something went wrong';
            $title = 'Error';
            $icon_type = 'warning';
        }

        Session::flash('swal_notification', ['title' => $title, 'icon_type' => $icon_type, 'message' => $message]);
        return redirect()->route('conductpersons.index');
    }

    public function validationRules($request, $id)
    {
        $this->validate($request, [
            'name' => 'bail|required|string',
            'guardian_name' => 'bail|required|string',
            'cnic_no' => 'bail|required|string|min:13|max:13|unique:conduct_people,cnic_no,'.$id,
            'email' => 'bail|required|string',
            'contact_number' => 'bail|required|string|min:11|max:11',
            'country_id' => 'bail|required|integer',
            'province_id' => 'bail|required|integer',
            'city_id' => 'bail|required|integer',
            'address' => 'bail|nullable|string',
            'picture' => 'bail|nullable|string',
        ],[
            'cnic_no.min'=> 'The CNIC Number must be at least 13 Digits',
            'cnic_no.min'=> 'The CNIC Number must not be greater than 13 Digits',
            'contact_number.min'=> 'The Contact number must be at least 11 Digits',
            'contact_number.max'=> 'The Contact number must not be greater than 11 Digits',
        ]);

    }

    public function destroy($id)
    {
        $person = ConductPerson::findOrFail($id);
        $img_path = 'conduct_persons/'.$person?->picture;
        if($person?->picture != null && \Storage::disk('public')->exists($img_path)){
            \Storage::disk('public')->delete($img_path);
        }
        $person->delete();
        return redirect()->route('conductpersons.index');
    }
}
