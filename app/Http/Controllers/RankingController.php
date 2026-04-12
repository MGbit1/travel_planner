<?php

namespace App\Http\Controllers;

use App\Services\PlaceStatisticsService;

class RankingController extends Controller
{
    public function __construct(private PlaceStatisticsService $stats) {}

    public function index()
    {
        $topPlaces = $this->stats->getTopPlaces(15);
        return view('ranking.index', compact('topPlaces'));
    }
}