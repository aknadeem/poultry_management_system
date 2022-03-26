<?php

namespace App\Http\Controllers\PartyManagement;

use Session;
use App\Models\Broker;
use App\Models\Country;
use Illuminate\Http\Request;
use App\Models\ConductPerson;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class BrokerController extends Controller
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
        $brokers = Broker::with('country:id,name','province:id,name', 'city:id,name')->get();
        return view('partymanagement.brokers.index', compact('brokers'));
    }
    
    public function create()
    {
        $broker = new Broker();

        $countries = Country::with('provinces:id,name,country_id',
        'provinces.cities:id,name,province_id')->get(['id','name']);
        return view('partymanagement.brokers.create', compact('broker', 'countries'));
    }
    
    public function edit($id)
    {
        $broker = Broker::with('province:id,name', 'city:id,name')->find($id);

        $countries = Country::with('provinces:id,name,country_id',
        'provinces.cities:id,name,province_id')->get(['id','name']);

        return view('partymanagement.brokers.create', compact('broker', 'countries'));
    }

    public function store(Request $request)
    {
        $id = null;
        $this->validationRules($request, $id);

        $path = 'brokers/';
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
            $save = Broker::create([
                'name' => $request->name,
                'guardian_name' => $request->guardian_name,
                'cnic_no' => $request->cnic_no,
                'email' => $request->email,
                'contact_no' => $request->contact_number,
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
            Log::error($e);
            $message = 'Something went wrong';
            $title = 'Error';
            $icon_type = 'warning';
        }

        Session::flash('swal_notification', ['title' => $title, 'icon_type' => $icon_type, 'message' => $message]);
        return redirect()->route('brokers.index');
    }
    
    
    public function update(Request $request, $id)
    {
        $this->validationRules($request, $id);
        
        $path = 'brokers/';
        $broker_data = Broker::find($id);
        if ($request->hasFile('image_file')) {
            if($broker_data?->picture != null && \Storage::disk('public')->exists('brokers/'.$broker_data?->picture)){
                \Storage::disk('public')->delete('brokers/'.$broker_data?->picture);
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
            $update_data = $broker_data->update([
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
            Log::error($e);
            $message = 'Something went wrong';
            $title = 'Error';
            $icon_type = 'warning';
        }

        Session::flash('swal_notification', ['title' => $title, 'icon_type' => $icon_type, 'message' => $message]);
        return redirect()->route('brokers.index');
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
        $borker = Broker::findOrFail($id);
        $img_path = 'brokers/'.$borker?->picture;
        if($borker?->picture != null && \Storage::disk('public')->exists($img_path)){
            \Storage::disk('public')->delete($img_path);
        }
        $borker->delete();
        return redirect()->route('brokers.index');
    }
}
