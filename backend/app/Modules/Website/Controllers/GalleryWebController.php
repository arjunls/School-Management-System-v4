<?php

namespace App\Modules\Website\Controllers;

use App\Kernel\Http\Controllers\Controller;
use App\Modules\Website\Models\Gallery;
use Illuminate\Http\Request;

class GalleryWebController extends Controller
{
    public function index()
    {
        $galleries = Gallery::orderBy('created_at', 'desc')->paginate(25);

        return view('website.galleries.index', compact('galleries'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image_path' => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
        ]);

        Gallery::create($data);

        return redirect()->route('website.galleries.index')
            ->with('success', 'Galeri berhasil ditambahkan');
    }

    public function destroy(Gallery $gallery)
    {
        $gallery->delete();

        return redirect()->route('website.galleries.index')
            ->with('success', 'Galeri berhasil dihapus');
    }
}
