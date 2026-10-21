<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\CrewMember;
use App\Models\Faq;
use App\Models\Project;
use App\Models\Service;
use Illuminate\Support\Facades\Route;

class HomeController extends Controller
{
    public function index()
    {
        $services = Service::active()->ordered()->get()->map(fn ($s) => [
            'title' => $s->title,
            'text'  => $s->summary,
            'img'   => $s->image ? asset('storage/'.$s->image) : null,
            'url'   => Route::has('services.show') ? route('services.show', $s->id) : null,
        ])->all();

        $projects = Project::published()->ordered()->get();
        $first    = $projects->first();

        // Projects no longer have a text title. The home page shows the title logo (logo_url);
        // "title" is only the readable name used as the logo's alt text.
        $featured = $first ? [
            'title'    => $first->name,
            'logo_url' => $first->logo_url,
            'tag'      => $first->tag,
            'synopsis' => $first->synopsis,
            'url'      => Route::has('projects.show') ? route('projects.show', $first->slug) : null,
        ] : null;

        $projectsRoute = collect(['projects', 'projects.index'])->first(fn ($name) => Route::has($name));

        $team = CrewMember::active()->ordered()->get()->map(fn ($m) => [
            'name'      => $m->name,
            'role'      => $m->role,
            'photo_url' => $m->photo_url,
        ])->all();

        $clients = Client::active()->ordered()->get()->map(fn ($c) => [
            $c->name,
            $c->logo ? asset('storage/'.$c->logo) : null,
        ])->all();

        $faqs = Faq::active()->ordered()->get()->map(fn ($f) => [
            'q' => $f->question,
            'a' => $f->answer,
        ])->all();

        return view('welcome', [
            'services'      => $services,
            'featured'      => $featured,
            'featuredImg'   => $first?->image_url,
            'others'        => $projects->slice(1)->all(),
            'projectsRoute' => $projectsRoute,
            'projectsUrl'   => $projectsRoute ? route($projectsRoute, ['locale' => app()->getLocale()]) : null,
            'team'          => $team,
            'clients'       => $clients,
            'faqs'          => $faqs,
        ]);
    }
}