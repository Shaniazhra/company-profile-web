<?php

namespace App\Http\Controllers;

use App\Models\PipelineStep;

class HomeController extends Controller
{
    public function index()
    {
        $steps = PipelineStep::query()
            ->where('is_published', true)
            ->orderBy('sort_order')
            ->get()
            ->groupBy('type');

        return view('home', [
            'traditional' => $steps->get('traditional', collect()),
            'dreamRatio' => $steps->get('dream_ratio', collect()),
        ]);
    }
}