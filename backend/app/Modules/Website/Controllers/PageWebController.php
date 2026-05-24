<?php

namespace App\Modules\Website\Controllers;

use App\Kernel\Http\Controllers\Controller;
use App\Modules\Website\Models\Page;
use Illuminate\Http\Request;

class PageWebController extends Controller
{
    public function index()
    {
        $pages = Page::ordered()->paginate(25);

        return view('website.pages.index', compact('pages'));
    }

    public function edit(Page $page)
    {
        return view('website.pages.form', compact('page'));
    }

    public function update(Request $request, Page $page)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'is_published' => 'nullable|boolean',
            'order' => 'nullable|integer|min:0',
        ]);

        $data['is_published'] ??= false;

        $page->update($data);

        return redirect()->route('website.pages.index')
            ->with('success', 'Halaman berhasil diperbarui');
    }
}
