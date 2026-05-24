<?php

namespace App\Modules\Website\Controllers;

use App\Kernel\Http\Controllers\Controller;
use App\Modules\Website\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WebsiteWebController extends Controller
{
    public function index()
    {
        $posts = Post::published()
            ->with('author')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('website.index', compact('posts'));
    }

    public function show(Post $post)
    {
        if (!$post->is_published) {
            abort(404);
        }

        return view('website.show', compact('post'));
    }

    public function adminIndex()
    {
        $posts = Post::with('author')
            ->orderBy('created_at', 'desc')
            ->paginate(25);

        return view('website.posts.index', compact('posts'));
    }

    public function create()
    {
        return view('website.posts.form');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:posts,slug',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'featured_image' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:100',
            'is_published' => 'nullable|boolean',
        ]);

        $data['slug'] = $data['slug'] ?? Str::slug($data['title']);
        $data['author_id'] = auth()->id();
        $data['is_published'] ??= false;

        Post::create($data);

        return redirect()->route('website.admin.index')
            ->with('success', 'Postingan berhasil dibuat');
    }

    public function edit(Post $post)
    {
        return view('website.posts.form', compact('post'));
    }

    public function update(Request $request, Post $post)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:posts,slug,' . $post->id,
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'featured_image' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:100',
            'is_published' => 'nullable|boolean',
        ]);

        $data['slug'] = $data['slug'] ?? Str::slug($data['title']);

        $post->update($data);

        return redirect()->route('website.admin.index')
            ->with('success', 'Postingan berhasil diperbarui');
    }

    public function destroy(Post $post)
    {
        $post->delete();

        return redirect()->route('website.admin.index')
            ->with('success', 'Postingan berhasil dihapus');
    }
}
