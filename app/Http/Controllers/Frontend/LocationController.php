<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\District;
use App\Models\Division;
use App\Models\Thana;
use Illuminate\Http\JsonResponse;

class LocationController extends Controller
{
    public function districts(Division $division): JsonResponse
    {
        return response()->json(
            $division->districts()->orderBy('name')->get(['id', 'name'])
        );
    }

    public function thanas(District $district): JsonResponse
    {
        return response()->json(
            $district->thanas()->orderBy('name')->get(['id', 'name'])
        );
    }

    public function unions(Thana $thana): JsonResponse
    {
        return response()->json(
            $thana->unions()->orderBy('name')->get(['id', 'name'])
        );
    }
}
