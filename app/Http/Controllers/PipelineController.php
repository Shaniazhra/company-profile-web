<?php

namespace App\Http\Controllers;

use App\Models\PipelineStep;

class PipelineController extends Controller
{
    public function index()
    {
        $steps = PipelineStep::query()
            ->where('is_published', true)
            ->orderBy('sort_order')
            ->get()
            ->groupBy('type');

        return view('pipeline', [
            'traditional' => $steps->get('traditional', collect()),
            'dreamRatio' => $steps->get('dream_ratio', collect()),
        ]);
    }
}