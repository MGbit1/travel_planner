<?php

namespace App\Http\Controllers;

use App\Services\PlaceStatisticsService;

class LandingController extends Controller
{
    public function __construct(private PlaceStatisticsService $stats) {}

    public function index()
    {
        $featuredPlaces = $this->stats->getTopPlaces(6);
        return view('landing', compact('featuredPlaces'));
    }
}
