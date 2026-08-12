<?php

namespace App\Http\Controllers\InventoryManagement;

use Session;
use DataTables;
use App\Models\Feed;
use App\Models\Product;
use App\Helpers\Constant;
use App\Models\FeedCategory;
use App\Models\PartyCompany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Actions\InventoryManagement\StoreFeedPurchaseAction;
use App\Actions\InventoryManagement\DestroyFeedAction;
use App\Http\Requests\InventoryManagement\StoreFeedPurchaseRequest;

class FeedController extends Controller
{
    private $auth_user_id;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->auth_user_id = \Auth::user()->id;
            return $next($request);
        });
    }

    public function index()
    {
        $products = Product::where('product_group', Constant::PRODUCT_GROUP['Feed'])
            ->with('company:id,company_name', 'category:id,name')
            ->get(['id', 'product_name', 'product_group', 'product_code', 'bar_code', 'party_company_id', 'product_category_id', 'quantity', 'purchase_date', 'is_active']);

        return view('inventorymanagement.feeds.index', compact('products'));
    }

    public function getFeedList()
    {
        $feeds = Feed::with('category:id,name')->orderBy('id', 'DESC')->get();

        return DataTables::of($feeds)
            ->addIndexColumn()
            ->addColumn('feed_category_id', function ($row) {
                return '<b>'.$row?->category?->name.'</b>';
            })
            ->addColumn('Actions', function ($row) {
                return ' <a class="btn btn-secondary btn-sm" href="'.route('feed.show', $row['id']).'"
                title="View Details" tabindex="0" data-plugin="tippy"
                data-tippy-animation="scale" data-tippy-arrow="true"><i class="fa fa-eye"></i>
                View
            </a>
            <a class="btn btn-info btn-sm openFeedModal"
                FeedId="'.$row['id'].'" data-id="'.$row['id'].'" id="editFeedModal" href="javascript:void(0);"
                title="Click to edit"><i
                    class="fa fa-pencil-alt"></i>
                Edit
            </a>
            <a class="btn btn-danger btn-sm delete-confirm"
                href="'.route('feed.destroy', $row['id']).'"
                del_title="Feed '.$row['feed_name'].'" title="Click to delete"
                tabindex="0" data-plugin="tippy" data-tippy-animation="scale"
                data-tippy-arrow="true"><i class="fa fa-trash"></i>
                Delete
            </a>';
            })
            ->rawColumns(['feed_category_id', 'Actions'])
            ->make(true);
    }

    public function create()
    {
        $feed = new Feed();
        $categories = FeedCategory::get();
        $companies = PartyCompany::with('vendor:id,name,guardian_name', 'vendor.balancelimit')->get();

        return view('inventorymanagement.feeds.create', compact('feed', 'categories', 'companies'));
    }

    public function store(StoreFeedPurchaseRequest $request, StoreFeedPurchaseAction $action)
    {
        try {
            $action->execute(
                $request->validated(),
                $request->file('image_file'),
                $this->auth_user_id
            );
            Session::flash('swal_notification', [
                'title' => 'Success',
                'icon_type' => 'success',
                'message' => 'New Feed entry created successfully!',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            Log::error($e);
            if (app()->environment('testing')) {
                throw $e;
            }
            Session::flash('swal_notification', [
                'title' => 'Error',
                'icon_type' => 'danger',
                'message' => 'Data not save something went wrong!',
            ]);
        }

        return redirect()->route('feed.index');
    }

    public function update(Request $request, $id)
    {
        // Modal update path — keep lightweight feed name/category update
        $feed = Feed::find($request->feed_id_modal ?: $id);
        if (! $feed) {
            return response()->json(['message' => 'No Feed entry found against this id', 'success' => 'no'], 200);
        }

        $feed->update([
            'feed_name' => $request->feed_name ?? $feed->feed_name,
            'feed_category_id' => $request->feed_category_id ?? $feed->feed_category_id,
            'updatedby' => $this->auth_user_id,
        ]);

        return response()->json([
            'message' => 'Feed Data Updated successfully!',
            'success' => 'yes',
        ], 200);
    }

    public function show($id)
    {
        $feed = Feed::with('category:id,name', 'purchases', 'purchases.company:id,company_name')->findOrFail($id);

        return view('inventorymanagement.feeds.feed_purcahses', compact('feed'));
    }

    public function edit($id)
    {
        $feed = Feed::find($id);
        if ($feed) {
            return response()->json([
                'message' => 'yes',
                'feed' => $feed->toArray(),
            ], 201);
        }
    }

    public function destroy($id, DestroyFeedAction $action)
    {
        try {
            $feed = Feed::findOrFail($id);
            $action->execute($feed);
            Session::flash('swal_notification', [
                'title' => 'Deleted',
                'icon_type' => 'success',
                'message' => 'Data Deleted Successfully!',
            ]);
        } catch (\Throwable $e) {
            Log::error($e);
            if (app()->environment('testing')) {
                throw $e;
            }
            Session::flash('swal_notification', [
                'title' => 'Error',
                'icon_type' => 'warning',
                'message' => 'Something went wrong',
            ]);
        }

        return redirect()->route('feed.index');
    }
}
