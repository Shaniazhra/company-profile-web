<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\ContactPerson;
use App\Models\CrewMember;
use App\Models\Faq;
use App\Models\FocusShot;
use App\Models\Highlight;
use App\Models\Inquiry;
use App\Models\ProcessStage;
use App\Models\Project;
use App\Models\Service;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index()
    {
        // Safe even if a module table does not exist yet: the result is 0.
        $has = fn (string $model): bool => Schema::hasTable((new $model)->getTable());

        $tally = function (string $model, string $flag = 'is_active') use ($has): array {
            if (! $has($model)) {
                return ['total' => 0, 'live' => 0];
            }

            return ['total' => $model::count(), 'live' => $model::where($flag, true)->count()];
        };

        $counts = [
            'services'   => $tally(Service::class),
            'projects'   => $tally(Project::class, 'is_published'),
            'clients'    => $tally(Client::class),
            'team'       => $tally(CrewMember::class),
            'process'    => $tally(ProcessStage::class),
            'highlights' => $tally(Highlight::class),
            'focus'      => $tally(FocusShot::class),
            'faqs'       => $tally(Faq::class),
            'contacts'   => $tally(ContactPerson::class),
            'inquiries'  => $has(Inquiry::class)
                ? ['total' => Inquiry::count(), 'unread' => Inquiry::where('is_read', false)->count()]
                : ['total' => 0, 'unread' => 0],
        ];

        $projects = $has(Project::class) ? Project::latest('id')->take(6)->get() : collect();
        $clients  = $has(Client::class) ? Client::latest('id')->take(6)->get() : collect();

        return view('admin.dashboard', compact('counts', 'projects', 'clients'));
    }
}