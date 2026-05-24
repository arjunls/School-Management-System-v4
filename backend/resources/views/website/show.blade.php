<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $post->title }} - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 font-sans">
    <nav class="bg-white shadow-sm border-b">
        <div class="max-w-4xl mx-auto px-4 py-4 flex items-center justify-between">
            <a href="/" class="text-xl font-bold text-indigo-600">{{ config('app.name') }}</a>
            <div class="flex items-center gap-4 text-sm">
                <a href="{{ route('website.index') }}" class="text-slate-600 hover:text-indigo-600">Blog</a>
                <a href="{{ route('login') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Login</a>
            </div>
        </div>
    </nav>

    <main class="max-w-4xl mx-auto px-4 py-10">
        <a href="{{ route('website.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 mb-6 inline-block">
            <i class="fas fa-arrow-left mr-1"></i>Kembali ke Blog
        </a>

        <article class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            @if($post->featured_image)
            <img src="{{ $post->featured_image }}" alt="{{ $post->title }}" class="w-full h-64 md:h-80 object-cover">
            @endif
            <div class="p-6 md:p-10">
                @if($post->category)
                <span class="text-xs font-semibold text-indigo-600 bg-indigo-50 px-2 py-1 rounded-full">{{ $post->category }}</span>
                @endif
                <h1 class="text-3xl md:text-4xl font-bold text-slate-900 mt-3 mb-4">{{ $post->title }}</h1>
                <div class="flex items-center gap-4 text-sm text-slate-400 mb-6">
                    <span><i class="far fa-user mr-1"></i>{{ $post->author->name ?? 'Admin' }}</span>
                    <span><i class="far fa-calendar mr-1"></i>{{ $post->created_at->format('d M Y') }}</span>
                </div>
                <div class="prose prose-slate max-w-none text-slate-700 leading-relaxed">
                    {!! nl2br(e($post->content)) !!}
                </div>
            </div>
        </article>
    </main>

    <footer class="bg-white border-t mt-12 py-6 text-center text-sm text-slate-500">
        &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
    </footer>
</body>
</html>
