<?php

namespace App\Http\Controllers\InventoryManagement;

use Session;
use DataTables;
use App\Models\Feed;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Models\CompanyBalance;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\CustomerFormRequest;

class FeedController extends Controller
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
        return view('inventorymanagement.feeds.index');
    }

    public function getFeedList()
    {
        $feeds = Feed::orderBy('id','DESC')->get();
        return DataTables::of($feeds)
            ->addIndexColumn()
            ->addColumn('picture', function($row){
                $url= asset('storage/feeds/'.$row?->picture);
                return '<img class="rounded-circle avatar-lg" src="'.$url.'"  alt="No image" />';
            })
            ->addColumn('Actions', function($row){
                return ' <a class="btn btn-secondary btn-sm ViewEmployeeModal"
                FeedId="'.$row["id"].'" href="javascript:void(0);"
                title="View Details" tabindex="0" data-plugin="tippy"
                data-tippy-animation="scale" data-tippy-arrow="true"><i class="fa fa-eye"></i>
                View
            </a>
            <a class="btn btn-info btn-sm openFeedModal"
                FeedId="'.$row["id"].'" data-id="'.$row["id"].'" id="editFeedModal" href="javascript:void(0);"
                title="Click to edit"><i
                    class="fa fa-pencil-alt"></i>
                Edit
            </a>
            <a class="btn btn-danger btn-sm delete-confirm"
                href="'.route("feed.destroy", $row["id"]).'"
                del_title="Feed '.$row["feed_name"].'" title="Click to delete"
                tabindex="0" data-plugin="tippy" data-tippy-animation="scale"
                data-tippy-arrow="true"><i class="fa fa-trash"></i>
                Delete
            </a>';
            })
            ->rawColumns(['picture','Actions'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'feed_name' => 'bail|required|string',
            'purchase_date' => 'bail|required|date',
            'company_id' => 'bail|required|integer',
            'quantity' => 'bail|required|numeric',
            'price' => 'bail|required|numeric',
            'discount_amount' => 'bail|nullable|numeric',
            'discount_percentage' => 'bail|nullable',
            'total_price' => 'bail|required|numeric',
            'image_file' => 'nullable|mimes:jpeg,jpg,png|max:5000',
        ],[
            'image_file.max'=> 'Maximum Image size to upload is 5MB (5000KB). If you are uploading a photo, try to reduce its resolution to make it under 5MB',
        ]);
        if($validator->fails()){
            return response()->json([
                'error' => $validator->errors()->toArray(),
                'success' => 'no',
            ], 201);
        }
        if($request->feed_id_modal > 0){
            $feed_data = Feed::find($request->feed_id_modal);
        }else{
            $feed_data = null;
        }
        // $country = $session?->user?->getAddress()?->country;
        if ($request->hasFile('image_file')) {
            if($feed_data?->picture != null && \Storage::disk('public')->exists('feeds/'.$feed_data?->picture)){
                \Storage::disk('public')->delete('feeds/'.$feed_data?->picture);
            }
            $path = 'feeds/';
            $image_file = $request->file('image_file');
            $extension = $request->file('image_file')->extension();
            $imageName = time().mt_rand(10,99).'.'.$extension;
            $upload = $image_file->storeAs($path, $imageName, 'public');
        }else{
            $imageName = null;
        }

        if($request->feed_id_modal > 0){
            if($feed_data !=''){
                $message = 'Feed Data Updated successfully!';
                $success = 'yes';
                if($feed_data->picture !='' && $imageName == null){
                    $imageName = $feed_data->picture;
                }
                $update_feed = $feed_data->update([
                    'feed_name' => $request->feed_name,
                    'purchase_date' => $request->purchase_date,
                    'company_id' => $request->company_id,
                    'quantity' => $request->quantity,
                    'price' => $request->price,
                    'discount_amount' => $request->discount_amount,
                    'discount_percentage' => $request->discount_percentage,
                    'total_price' => $request->total_price,
                    'picture' => $imageName,
                    'updatedby' => $this->auth_user_id,
                ]);
            }else{
                $message = 'No Feed entry found against this id';
                $success = 'no';
            }
        }else{
            $message = 'New Feed entry created successfully!';
            $success = 'yes';
            try {
               DB::transaction(function () use ($request, $imageName) {
                    $feed = Feed::create([
                        'feed_name' => $request->feed_name,
                        'purchase_date' => $request->purchase_date,
                        'company_id' => $request->company_id,
                        'quantity' => $request->quantity,
                        'price' => $request->price,
                        'discount_amount' => $request->discount_amount,
                        'discount_percentage' => $request->discount_percentage,
                        'total_price' => $request->total_price,
                        'picture' => $imageName,
                        'addedby' => $this->auth_user_id,
                    ]);
                    
                    $companyBalance = CompanyBalance::create([
                        'type' => 'feed',
                        'company_id' => $request->company_id,
                        'feed_purchase_id' => $feed->id,
                        'total_amount' => $request->total_price,
                        'remaining_amount' => $request->total_price,
                        'addedby' => $this->auth_user_id,
                    ]);
                });
            }
            catch (\Throwable $e) {
                return $e;
                $message = 'Data not save something went wrong!';
                $success = 'no';
            }
        }
        return response()->json([
            'message' => $message,
            'success' => $success,
        ], 200);
    }

    public function show($id)
    {
        $customer = Employee::find($id);
        if($customer){
            $html_data = \View::make('layouts._partial.customerdetail', compact('customer'))->render();
            $message = 'Employee Detail Data';
            $success = 'yes';
        }else{
            $message = 'No employee detail found against this id';
            $success = 'no';
            $html_data = '';
        }
        return response()->json([
            'message' => $message,
            'success' => $success,
            'html_data' => $html_data,
        ], 201);

        return response()->json($data, 200, $headers);
    }

    public function edit($id)
    {
        $feed = Feed::with('company:id,name,address,contact_no')->find($id);
        if($feed){
            $message = 'yes';
            return response()->json([
                'message' => $message,
                'feed' => $feed->toArray(),
            ], 201);
        }
    }

    public function destroy($id)
    {
        $feed = Feed::findOrFail($id);
        $img_path = 'feeds/'.$feed?->picture;
        if($feed?->picture != null && \Storage::disk('public')->exists($img_path)){
            \Storage::disk('public')->delete($img_path);
        }
        $feed->delete();
        Session::flash('swal_notification', ['title' => 'Deleted', 'icon_type' => 'success', 'message' => 'Data Deleted Successfully!']);
        // return back();
        return redirect()->route('feed.index');
    }
}
