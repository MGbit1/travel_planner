<?php

namespace App\Http\Controllers;

use App\Models\Trip;

class LandingController extends Controller
{
    public function index()
    {
        $trips = Trip::all();
        $placeCounts = [];

        foreach ($trips as $trip) {
            $itinerary = is_string($trip->itinerary_data) ? json_decode($trip->itinerary_data, true) : $trip->itinerary_data;
            if (!$itinerary) continue;

            foreach ($itinerary as $day => $places) {
                foreach ($places as $place) {
                    $name = $place['name'] ?? '未知地點';
                    if (str_contains($name, '出發') || str_contains($name, '回家') || str_contains($name, '返程') || preg_match('/[0-9]+號|[0-9]+巷/', $name)) {
                        continue;
                    }
                    if (!isset($placeCounts[$name])) {
                        $placeCounts[$name] = [
                            'name'   => $name,
                            'count'  => 0,
                            'photo'  => $place['photo'] ?? null,
                            'rating' => $place['rating'] ?? 0,
                            'types'  => $place['types'] ?? [],
                        ];
                    }
                    $placeCounts[$name]['count']++;
                }
            }
        }

        usort($placeCounts, fn($a, $b) => $b['count'] <=> $a['count']);
        $featuredPlaces = array_slice($placeCounts, 0, 6);

        return view('landing', compact('featuredPlaces'));
    }
}
