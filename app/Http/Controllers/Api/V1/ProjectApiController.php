<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $locale = $request->get('locale', app()->getLocale() ?: 'az');
        $categorySlug = $request->get('category');
        $featuredOnly = $request->boolean('featured');

        $query = Project::query()
            ->with(['category', 'tags'])
            ->published();

        if ($featuredOnly) {
            $query->where('is_featured', true);
        }

        if ($categorySlug) {
            $query->whereHas('category', function ($q) use ($categorySlug, $locale) {
                $q->where("slug->{$locale}", $categorySlug)
                  ->orWhere('slug->az', $categorySlug);
            });
        }

        $projects = $query->get()->map(function ($project) use ($locale) {
            return [
                'id' => $project->project_code,
                'title' => $project->getTranslation('title', $locale) ?: $project->title,
                'client' => $project->client,
                'category' => $project->category ? ($project->category->getTranslation('name', $locale) ?: $project->category->name) : null,
                'description' => $project->getTranslation('description', $locale) ?: $project->description,
                'image' => $project->image_url,
                'tags' => $project->tags->map(fn ($tag) => $tag->getTranslation('name', $locale) ?: (is_array($tag->name) ? ($tag->name[$locale] ?? reset($tag->name)) : $tag->name))->values()->all(),
                'bgLight' => $project->bg_light,
                'bgDark' => $project->bg_dark,
                'year' => $project->year,
                'specs' => $project->getTranslation('specs', $locale) ?: ($project->specs ?? []),
                'challenge' => $project->getTranslation('challenge', $locale) ?: $project->challenge,
                'solution' => $project->getTranslation('solution', $locale) ?: $project->solution,
                'overview' => $project->getTranslation('overview', $locale) ?: ($project->overview ?? []),
                'features' => $project->getTranslation('features', $locale) ?: ($project->features ?? []),
                'results' => $project->getTranslation('results', $locale) ?: ($project->results ?? []),
                'viewsCount' => $project->views_count,
                'websiteUrl' => $project->website_url,
                'gallery' => $project->gallery_urls,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $projects,
        ]);
    }

    public function show(Request $request, string $code): JsonResponse
    {
        $locale = $request->get('locale', app()->getLocale() ?: 'az');

        $project = Project::query()
            ->with(['category', 'tags'])
            ->where('project_code', $code)
            ->firstOrFail();

        $project->increment('views_count');
        \App\Models\SiteVisit::recordVisit($request, $project);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $project->project_code,
                'title' => $project->getTranslation('title', $locale) ?: $project->title,
                'client' => $project->client,
                'category' => $project->category ? ($project->category->getTranslation('name', $locale) ?: $project->category->name) : null,
                'description' => $project->getTranslation('description', $locale) ?: $project->description,
                'image' => $project->image_url,
                'tags' => $project->tags->map(fn ($tag) => $tag->getTranslation('name', $locale) ?: (is_array($tag->name) ? ($tag->name[$locale] ?? reset($tag->name)) : $tag->name))->values()->all(),
                'bgLight' => $project->bg_light,
                'bgDark' => $project->bg_dark,
                'year' => $project->year,
                'specs' => $project->getTranslation('specs', $locale) ?: ($project->specs ?? []),
                'challenge' => $project->getTranslation('challenge', $locale) ?: $project->challenge,
                'solution' => $project->getTranslation('solution', $locale) ?: $project->solution,
                'overview' => $project->getTranslation('overview', $locale) ?: ($project->overview ?? []),
                'features' => $project->getTranslation('features', $locale) ?: ($project->features ?? []),
                'results' => $project->getTranslation('results', $locale) ?: ($project->results ?? []),
                'viewsCount' => $project->views_count,
                'websiteUrl' => $project->website_url,
                'gallery' => $project->gallery_urls,
                'seo' => [
                    'metaTitle' => $project->getTranslation('meta_title', $locale) ?: $project->meta_title,
                    'metaDescription' => $project->getTranslation('meta_description', $locale) ?: $project->meta_description,
                ],
            ],
        ]);
    }

    public function categories(Request $request): JsonResponse
    {
        $locale = $request->get('locale', app()->getLocale() ?: 'az');

        $categories = ProjectCategory::query()
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
