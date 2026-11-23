<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BlogApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $locale = $request->get('locale', app()->getLocale() ?: 'az');
        $categorySlug = $request->get('category');
        $perPage = (int) $request->get('per_page', 9);

        $query = BlogPost::query()
            ->with(['category', 'tags'])
            ->published();

        if ($categorySlug) {
            $query->whereHas('category', function ($q) use ($categorySlug, $locale) {
                $q->where("slug->{$locale}", $categorySlug)
                  ->orWhere('slug->az', $categorySlug);
            });
        }

        $posts = $query->paginate($perPage);

        $data = collect($posts->items())->map(function ($post) use ($locale) {
            return [
                'id' => $post->id,
                'slug' => $post->getTranslation('slug', $locale) ?: $post->slug,
                'title' => $post->getTranslation('title', $locale) ?: $post->title,
                'category' => $post->category ? ($post->category->getTranslation('name', $locale) ?: $post->category->name) : null,
                'excerpt' => $post->getTranslation('excerpt', $locale) ?: $post->excerpt,
                'image' => $post->image_url,
                'tags' => $post->tags->map(fn ($tag) => $tag->getTranslation('name', $locale) ?: (is_array($tag->name) ? ($tag->name[$locale] ?? reset($tag->name)) : $tag->name))->values()->all(),
                'publishedAt' => $post->published_at ? $post->published_at->format('d M Y') : null,
                'author' => $post->getTranslation('author', $locale) ?: ($post->author ?: 'CODEMARK'),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
            'meta' => [
                'currentPage' => $posts->currentPage(),
                'lastPage' => $posts->lastPage(),
                'perPage' => $posts->perPage(),
                'total' => $posts->total(),
            ],
        ]);
    }

    public function show(Request $request, string $slug): JsonResponse
    {
        $locale = $request->get('locale', app()->getLocale() ?: 'az');

        $post = BlogPost::query()
            ->with(['category', 'tags'])
            ->where(function ($q) use ($slug, $locale) {
                $q->where("slug->{$locale}", $slug)
                  ->orWhere('slug->az', $slug)
                  ->orWhere('slug->en', $slug)
                  ->orWhere('slug->ru', $slug);
            })
            ->firstOrFail();

        $post->increment('views_count');

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $post->id,
                'slug' => $post->getTranslation('slug', $locale) ?: $post->slug,
                'title' => $post->getTranslation('title', $locale) ?: $post->title,
                'category' => $post->category ? ($post->category->getTranslation('name', $locale) ?: $post->category->name) : null,
                'excerpt' => $post->getTranslation('excerpt', $locale) ?: $post->excerpt,
                'content' => $post->getTranslation('content', $locale) ?: $post->content,
                'image' => $post->image_url,
                'tags' => $post->tags->map(fn ($tag) => $tag->getTranslation('name', $locale) ?: (is_array($tag->name) ? ($tag->name[$locale] ?? reset($tag->name)) : $tag->name))->values()->all(),
                'publishedAt' => $post->published_at ? $post->published_at->format('d M Y') : null,
                'author' => $post->getTranslation('author', $locale) ?: ($post->author ?: 'CODEMARK'),
                'seo' => [
                    'metaTitle' => $post->getTranslation('meta_title', $locale) ?: $post->meta_title,
                    'metaDescription' => $post->getTranslation('meta_description', $locale) ?: $post->meta_description,
                ],
            ],
        ]);
    }

    public function categories(Request $request): JsonResponse
    {
        $locale = $request->get('locale', app()->getLocale() ?: 'az');

        $categories = BlogCategory::query()
            ->active()
            ->get()
            ->map(function ($cat) use ($locale) {
                return [
                    'id' => $cat->id,
                    'name' => $cat->getTranslation('name', $locale) ?: $cat->name,
                    'slug' => $cat->getTranslation('slug', $locale) ?: $cat->slug,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }
}
