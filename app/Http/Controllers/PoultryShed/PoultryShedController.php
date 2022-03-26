<?php

namespace App\Http\Controllers\PoultryShed;

use App\Models\Country;
use App\Models\FarmType;
use App\Models\FarmSubtype;
use App\Models\PersonalFarm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class PoultryShedController extends Controller
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
        $farms = PersonalFarm::with('type:id,name', 'subtype:id,name')->get();
        return view('farmmanagement.personalfarms.index', compact('farms'));
    }

    public function create()
    {
        $farm = new PersonalFarm();
        
        $countries = Country::with('provinces:id,name,country_id',
        'provinces.cities:id,name,province_id')->get(['id','name']);

        $farm_types = FarmType::get();
        $farm_subtypes = FarmSubtype::get();
        return view('farmmanagement.personalfarms.create', compact('farm', 'countries', 'farm_types', 'farm_subtypes'));
    }


    public function store(Request $request)
    {
        $id = null;
        $this->validationRules($request, $id);
        try {
            $message = 'Data created successfully';
            $title = 'Success';
            $icon_type = 'success';

            if ($request->hasFile('farm_image')) {
                $farm_image_file = $request->file('farm_image');
                $extension = $request->file('farm_image')->extension();
                $imageName = time().mt_rand(10,99).'.'.$extension;
                
            }else{
                $imageName = null;
            }
            $personal_farm = PersonalFarm::create([
                'farm_type_id' => $request->farm_type_id,
                'farm_subtype_id' => $request->farm_subtype_id,
                'farm_name' => $request->farm_name,
                'farm_noc' => $request->farm_noc,
                'farm_area' => $request->farm_area,
                'farm_capacity' => $request->farm_capacity,
                'feed_room_size' => $request->feed_room_size,
                'farm_image' => $imageName,
                'farm_address' => $request->farm_address,
                'country_id' => $request->country_id,
                'province_id' => $request->province_id,
                'city_id' => $request->city_id,
                'addedby' => $this->auth_user_id,
            ]);

            if($personal_farm){
                $upload_to_folder = $farm_image_file->storeAs('personalfarms/', $imageName, 'public');
            }
        }
        catch (\Throwable $e) {
            Log::error($e);
            $message = 'Something went wrong';
            $title = 'Error';
            $icon_type = 'warning';
        }

        Session::flash('swal_notification', ['title' => $title, 'icon_type' => $icon_type, 'message' => $message]);
        return redirect()->route('personalfarms.index');
    }

    public function validationRules($request, $id)
    {
        // $validator = Validator::make($request->all(),[
        $this->validate($request, [
            'farm_type_id' => 'bail|required|integer',
            'farm_subtype_id' => 'bail|required|integer',
            'farm_name' => 'bail|required|string',
            'farm_noc' => 'bail|required|string',
            'farm_area' => 'bail|required',
            'farm_capacity' => 'bail|required',
            'feed_room_size' => 'bail|required',
            'country_id' => 'bail|required|integer',
            'province_id' => 'bail|required|integer',
            'city_id' => 'bail|required|integer',
            // 'farm_address' => 'bail|required|string',
            'farm_image' => 'bail|required|mimes:jpeg,jpg,png|max:5000',
        ],[
            'farm_image.required'=> 'The farm image field is required',
        ]);

        // if($validator->fails()){
        //     return response()->json([
        //         'error' => $validator->errors()->toArray(),
        //         'success' => 'no',
        //     ], 201);
        // }
    }


    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $farm = PersonalFarm::with('country:id,name','province:id,name','city:id,name')->find($id);
        $countries = Country::with('provinces:id,name,country_id',
        'provinces.cities:id,name,province_id')->get(['id','name']);

        $farm_types = FarmType::get();
        $farm_subtypes = FarmSubtype::get();
        return view('farmmanagement.personalfarms.create', compact('farm', 'countries', 'farm_types', 'farm_subtypes'));
    }

    public function update(Request $request, $id)
    {
        $this->validationRules($request, $id);
        try {

            $message = 'Data updated successfully';
            $title = 'Success';
            $icon_type = 'success';

            $personal_farm = PersonalFarm::findOrFail($id);

            if ($request->hasFile('farm_image')) {
                if($personal_farm?->farm_image != null && \Storage::disk('public')->exists('personalfarms/'.$personal_farm?->farm_image)){
                    \Storage::disk('public')->delete('personalfarms/'.$party_farm?->farm_image);
                }
                $farm_image_file = $request->file('farm_image');
                $extension = $request->file('farm_image')->extension();
                $imageName = time().mt_rand(10,99).'.'.$extension;
                
            }else{
                $imageName = null;
            }

            if($personal_farm->farm_image !='' && $imageName == null){
                $imageName = $personal_farm->farm_image;
            }

            $personal_farm->update([
                'farm_type_id' => $request->farm_type_id,
                'farm_subtype_id' => $request->farm_subtype_id,
                'farm_name' => $request->farm_name,
                'farm_noc' => $request->farm_noc,
                'farm_area' => $request->farm_area,
                'farm_capacity' => $request->farm_capacity,
                'feed_room_size' => $request->feed_room_size,
                'farm_image' => $imageName,
                'farm_address' => $request->farm_address,
                'country_id' => $request->country_id,
                'province_id' => $request->province_id,
                'city_id' => $request->city_id,
                'updatedby' => $this->auth_user_id,
            ]);

            if($party_farm){
                $upload_to_folder = $farm_image_file->storeAs('personalfarms/', $imageName, 'public');
            }
        }
        catch (\Throwable $e) {
            Log::error($e);
            $message = 'Something went wrong';
            $title = 'Error';
            $icon_type = 'warning';
        }

        Session::flash('swal_notification', ['title' => $title, 'icon_type' => $icon_type, 'message' => $message]);
        return redirect()->route('personalfarms.index');
    }

  
    public function destroy($id)
    {
        $personal_farm = PersonalFarm::findOrFail($id);
        $personal_farm->delete();
        $message = 'Data deleted successfully';
        $title = 'Deleted!';
        $icon_type = 'success';
        Session::flash('swal_notification', ['title' => $title, 'icon_type' => $icon_type, 'message' => $message]);
        return redirect()->route('personalfarms.index');
    }
    
    public function forceDelete($id)
    {
        $personal_farm = PersonalFarm::findOrFail($id);

        if($personal_farm?->farm_image != null && \Storage::disk('public')->exists('personalfarms/'.$personal_farm?->farm_image)){
            \Storage::disk('public')->delete('personalfarms/'.$personal_farm?->farm_image);
        }

        $personal_farm->delete();
        $message = 'Data deleted successfully';
        $title = 'Deleted!';
        $icon_type = 'success';
        Session::flash('swal_notification', ['title' => $title, 'icon_type' => $icon_type, 'message' => $message]);
        return redirect()->route('personalfarms.index');
    }
}
