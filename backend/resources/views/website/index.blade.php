<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 font-sans">
    <nav class="bg-white shadow-sm border-b">
        <div class="max-w-6xl mx-auto px-4 py-4 flex items-center justify-between">
            <a href="/" class="text-xl font-bold text-indigo-600">{{ config('app.name') }}</a>
            <div class="flex items-center gap-4 text-sm">
                <a href="{{ route('website.index') }}" class="text-slate-600 hover:text-indigo-600">Blog</a>
                <a href="{{ route('login') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Login</a>
            </div>
        </div>
    </nav>

    <main class="max-w-6xl mx-auto px-4 py-10">
        <h1 class="text-3xl font-bold text-slate-900 mb-8">Blog</h1>

        <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
            @forelse($posts as $post)
            <article class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                @if($post->featured_image)
                <img src="{{ $post->featured_image }}" alt="{{ $post->title }}" class="w-full h-48 object-cover">
                @else
                <div class="w-full h-48 bg-gradient-to-br from-indigo-100 to-purple-100 flex items-center justify-center">
                    <i class="fas fa-newspaper text-4xl text-indigo-300"></i>
                </div>
                @endif
                <div class="p-5">
                    @if($post->category)
                    <span class="text-xs font-semibold text-indigo-600 bg-indigo-50 px-2 py-1 rounded-full">{{ $post->category }}</span>
                    @endif
                    <h2 class="text-lg font-bold text-slate-900 mt-2 mb-2">
                        <a href="{{ route('website.show', $post) }}" class="hover:text-indigo-600 transition-colors">{{ $post->title }}</a>
                    </h2>
                    @if($post->excerpt)
                    <p class="text-sm text-slate-600 mb-3">{{ $post->excerpt }}</p>
                    @endif
                    <div class="flex items-center justify-between text-xs text-slate-400">
                        <span><i class="far fa-user mr-1"></i>{{ $post->author->name ?? 'Admin' }}</span>
                        <span><i class="far fa-calendar mr-1"></i>{{ $post->created_at->format('d M Y') }}</span>
                    </div>
                </div>
            </article>
            @empty
            <div class="col-span-full text-center text-slate-500 py-16">
                <i class="fas fa-newspaper text-5xl text-slate-300 mb-4"></i>
                <p>Belum ada postingan</p>
            </div>
            @endforelse
        </div>

        @if($posts->hasPages())
        <div class="mt-8">{{ $posts->links() }}</div>
        @endif
    </main>

    <footer class="bg-white border-t mt-12 py-6 text-center text-sm text-slate-500">
        &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
    </footer>
</body>
</html>
