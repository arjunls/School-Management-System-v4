<?php

namespace App\Modules\Announcement\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Announcement\Models\Announcement;
use Illuminate\Http\Request;
use App\Modules\Announcement\Requests\StoreAnnouncementRequest;

/**
 * @group Announcements
 *
 * APIs for managing announcements
 */
class AnnouncementController extends Controller
{
    /**
     * List all announcements
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Announcement::with('author:id,name');

        $query->where(function ($q) use ($user) {
            $q->where('target_role', 'all')->orWhere('target_role', $user->role);
        });

        $query->where(function ($q) {
            $q->whereNull('publish_at')->orWhere('publish_at', '<=', now());
        });

        return $this->paginated($query->orderByDesc('created_at')->paginate($request->per_page ?? 20));
    }

    /**
     * Create a new announcement
     */
    public function store(StoreAnnouncementRequest $request)
    {
        $data = $request->validated();
        $data['author_id'] = $request->user()->id;
        $ann = Announcement::create($data);
        $ann->load('author:id,name');
        return $this->created($ann, 'Announcement created');
    }

    /**
     * Update an announcement
     */
    public function update(Request $request, int $id)
    {
        $ann = Announcement::findOrFail($id);
        $ann->update($request->only(['title', 'content', 'target_role', 'publish_at', 'expires_at']));
        return $this->success($ann, 'Updated');
    }

    /**
     * Delete an announcement
     */
    public function destroy(int $id)
    {
        Announcement::findOrFail($id)->delete();
        return $this->deleted('Deleted');
    }
}
