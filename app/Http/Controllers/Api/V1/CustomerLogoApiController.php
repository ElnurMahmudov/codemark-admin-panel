<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\CustomerLogo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerLogoApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $logos = CustomerLogo::query()
            ->active()
            ->get()
            ->map(function ($logo) {
                return [
                    'id' => $logo->id,
                    'name' => $logo->name,
                    'lightSrc' => $logo->light_logo_url,
                    'darkSrc' => $logo->dark_logo_url,
                    'websiteUrl' => $logo->website_url,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $logos,
        ]);
    }
}
