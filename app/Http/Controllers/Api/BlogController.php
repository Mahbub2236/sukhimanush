<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = BlogPost::published();

        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('excerpt', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $posts = $query->paginate(6);
        $categories = BlogPost::select('category')->distinct()->pluck('category');
        $recentPosts = BlogPost::published()->take(4)->get();

        return response()->json([
            'posts' => $posts,
            'categories' => $categories,
            'recent_posts' => $recentPosts,
        ]);
    }

    public function show(string $slug): JsonResponse
    {
        $post = BlogPost::where('slug', $slug)->firstOrFail();
        $post->increment('views_count');

        $recentPosts = BlogPost::published()->where('id', '!=', $post->id)->take(3)->get();
        $categories = BlogPost::select('category')->distinct()->pluck('category');

        return response()->json([
            'post' => $post,
            'recent_posts' => $recentPosts,
            'categories' => $categories,
        ]);
    }
}
