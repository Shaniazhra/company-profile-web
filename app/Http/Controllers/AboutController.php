<?php

namespace App\Http\Controllers;

use App\Models\AboutPhoto;
use App\Models\Client;
use App\Models\CrewMember;
use App\Models\Highlight;
use App\Models\ProcessStage;
use App\Models\Project;
use Illuminate\Support\Facades\Route;

class AboutController extends Controller
{
    public function index()
    {
        $reasons = Highlight::reasons()->get()->map(fn ($r) => [
            'title' => $r->title,
            'text'  => $r->text,
            'icon'  => $r->icon_paths,
        ])->all();

        $ours    = ProcessStage::active()->ordered()->pluck('title')->all();
        $results = Highlight::results()->pluck('text')->all();

        $clients = Client::active()->ordered()->get()->map(fn ($c) => [
            $c->name,
            $c->logo ? asset('storage/'.$c->logo) : null,
        ])->all();

        // Numbers in the stats strip are counted from the admin data. A zero count hides its tile.
        $stats = collect([
            [Project::published()->count(),   __('Projects')],
            [Client::active()->count(),       __('Clients')],
            [CrewMember::active()->count(),   __('Team members')],
        ])->filter(fn ($s) => $s[0] > 0)->values()->all();

        // Gallery photos that are switched on, in the order set in the admin. Hidden when the whole section is switched off.
        $site = \App\Models\SiteSetting::first();
        $galleryPhotos = ($site?->gallery_enabled ?? true)
            ? AboutPhoto::active()->ordered()->get()
            : collect();

        $routeUrl = fn (array $names) => ($n = collect($names)->first(fn ($name) => Route::has($name)))
            ? route($n, ['locale' => app()->getLocale()])
            : null;

        return view('about', [
            'reasons'     => $reasons,
            'ours'        => $ours,
            'results'     => $results,
            'clients'     => $clients,
            'stats'       => $stats,
            'galleryPhotos' => $galleryPhotos,
            'teamUrl'     => $routeUrl(['team', 'team.index', 'crew', 'crew.index']),
            'projectsUrl' => $routeUrl(['projects', 'projects.index']),
        ]);
    }
}