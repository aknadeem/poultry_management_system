<?php

namespace App\Http\Controllers\Inertia\InventoryManagement;

use App\Actions\InventoryManagement\DestroyFeedAction;
use App\Actions\InventoryManagement\StoreFeedPurchaseAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\InventoryManagement\StoreFeedPurchaseRequest;
use App\Http\Requests\InventoryManagement\UpdateFeedRequest;
use App\Models\Feed;
use App\Queries\FeedQuery;
use App\Support\InventoryLookups;
use App\Support\InventoryPresenter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class FeedController extends Controller
{
    public function index(FeedQuery $query): Response
    {
        $this->authorize('viewAny', Feed::class);

        $feeds = $query->paginate();

        return Inertia::render('Feeds/Index', array_merge(InventoryLookups::feedOptions(), [
            'feeds' => $feeds->through(fn (Feed $feed): array => InventoryPresenter::feed($feed)),
            'filters' => $query->filters(),
        ]));
    }

    public function create(): Response
    {
        $this->authorize('create', Feed::class);

        return Inertia::render('Feeds/Create', InventoryLookups::feedOptions());
    }

    public function store(StoreFeedPurchaseRequest $request, StoreFeedPurchaseAction $action): RedirectResponse
    {
        $this->authorize('create', Feed::class);

        $action->execute(
            $request->validated(),
            $request->file('image_file'),
            (int) Auth::id(),
        );

        return redirect()
            ->route('inertia.feeds.index')
            ->with('swal_notification', [
                'title' => 'Success',
                'icon_type' => 'success',
                'message' => 'New Feed entry created successfully!',
            ]);
    }

    public function show(Feed $feed): Response
    {
        $this->authorize('view', $feed);

        return Inertia::render('Feeds/Show', [
            'feed' => InventoryPresenter::feed($feed, includePurchases: true),
        ]);
    }

    public function update(UpdateFeedRequest $request, Feed $feed): RedirectResponse
    {
        $this->authorize('update', $feed);

        $feed->update([
            'feed_name' => $request->validated('feed_name'),
            'feed_category_id' => $request->validated('feed_category_id'),
            'updatedby' => Auth::id(),
        ]);

        return redirect()
            ->route('inertia.feeds.index')
            ->with('swal_notification', [
                'title' => 'Updated',
                'icon_type' => 'success',
                'message' => 'Feed Data Updated successfully!',
            ]);
    }

    public function destroy(Feed $feed, DestroyFeedAction $action): RedirectResponse
    {
        $this->authorize('delete', $feed);
        $action->execute($feed);

        return redirect()
            ->route('inertia.feeds.index')
            ->with('swal_notification', [
                'title' => 'Deleted',
                'icon_type' => 'success',
                'message' => 'Data Deleted Successfully!',
            ]);
    }
}
