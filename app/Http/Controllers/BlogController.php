<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(): View
    {
        $posts = BlogPost::query()
            ->published()
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate(9);

        return view('blog.index', [
            'posts' => $posts,
            'solidHeader' => true,
        ]);
    }

    public function show(string $slug): View
    {
        $post = BlogPost::query()
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        $related = BlogPost::query()
            ->published()
            ->where('id', '!=', $post->id)
            ->orderByDesc('published_at')
            ->limit(5)
            ->get();

        return view('blog.show', [
            'post' => $post,
            'related' => $related,
            'solidHeader' => true,
        ]);
    }
}
