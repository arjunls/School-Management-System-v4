<?php

namespace App\Modules\Communication\Announcement\Controllers;

use App\Kernel\Http\Controllers\Controller;
use App\Modules\Communication\Announcement\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementWebController extends Controller
{
    public function index()
    {
        $announcements = Announcement::orderBy('created_at', 'desc')->paginate(25);
        return view('pengumuman.index', compact('announcements'));
    }

    public function create()
    {
        return view('pengumuman.form');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'target_role' => 'nullable|in:all,admin,teacher,student,parent',
            'is_active' => 'nullable|boolean',
        ]);

        $data['user_id'] = auth()->id();
        $data['is_active'] ??= true;

        Announcement::create($data);

        return redirect()->route('pengumuman.index')->with('success', 'Pengumuman berhasil dibuat');
    }

    public function destroy(Announcement $pengumuman)
    {
        $pengumuman->delete();
        return redirect()->route('pengumuman.index')->with('success', 'Pengumuman berhasil dihapus');
    }
}
