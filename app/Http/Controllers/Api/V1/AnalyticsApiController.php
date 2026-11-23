<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\Project;
use App\Models\SiteVisit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnalyticsApiController extends Controller
{
    public function track(Request $request): JsonResponse
    {
        $url = $request->input('url') ?: '/';
        $path = parse_url($url, PHP_URL_PATH) ?: '/';

        $visitable = null;

        // Automatically match blog post or project if applicable
        if (preg_match('#^/blog/([^/?]+)#', $path, $matches)) {
            $slug = $matches[1];
            $post = BlogPost::query()
                ->where('slug->az', $slug)
                ->orWhere('slug->en', $slug)
                ->orWhere('slug->ru', $slug)
                ->first();

            if ($post) {
                $visitable = $post;
                $post->increment('views_count');
            }
        } elseif (preg_match('#^/projects/([^/?]+)#', $path, $matches)) {
            $code = $matches[1];
            $project = Project::query()
                ->where('project_code', $code)
                ->first();

            if ($project) {
                $visitable = $project;
                $project->increment('views_count');
            }
        }

        SiteVisit::recordVisit($request, $visitable);

        return response()->json([
            'success' => true,
            'message' => 'Visit recorded',
        ]);
    }
}
