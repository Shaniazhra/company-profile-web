<?php

namespace App\Http\Controllers;

use App\Models\ContactPerson;
use App\Models\CrewMember;
use App\Models\Faq;
use App\Models\Inquiry;
use App\Models\ProcessStage;
use App\Models\Project;
use Illuminate\Http\Request;

/*
 * Methods read route values from the request instead of type-hinted parameters,
 * because every public route carries the {locale} prefix parameter.
 */
class SitePagesController extends Controller
{
    public function projects()
    {
        return view('pages.projects', ['projects' => Project::published()->ordered()->get()]);
    }

    public function project(Request $request)
    {
        $project = Project::published()->where('slug', $request->route('project'))->firstOrFail();

        return view('pages.project', [
            'project' => $project,
            'others' => Project::published()->ordered()->where('id', '!=', $project->id)->take(3)->get(),
        ]);
    }

    public function process()
    {
        return view('pages.process', ['stages' => ProcessStage::active()->ordered()->get()]);
    }

    public function team()
    {
        return view('pages.team', ['members' => CrewMember::active()->ordered()->get()]);
    }

    public function faq()
    {
        return view('pages.faq', ['faqs' => Faq::active()->ordered()->get()]);
    }

    public function contact()
    {
        return view('pages.contact', ['people' => ContactPerson::active()->ordered()->get()]);
    }

    public function contactStore(Request $request)
    {
        $loc = $request->route('locale');

        // Hidden field that real visitors never fill in
        if ($request->filled('website')) {
            return redirect()->route('contact', ['locale' => $loc]);
        }

        $data = $request->validate([
            'name'    => ['required', 'string', 'max:120'],
            'contact' => ['required', 'string', 'max:190'],
            'message' => ['required', 'string', 'min:10', 'max:3000'],
        ]);

        Inquiry::create($data);

        return redirect()->to(route('contact', ['locale' => $loc]).'#form')->with('sent', true);
    }
}