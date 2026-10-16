<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Division;
use App\Models\Project;
use App\Models\Service;
use App\Models\TeamMember;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index()
    {
        // Aman dipakai walau tabel modul belum ada: hasilnya kosong/0.
        $has = fn (string $model): bool => Schema::hasTable((new $model)->getTable());

        $count = function (string $model, ?string $flag = null) use ($has): int {
            if (! $has($model)) {
                return 0;
            }

            return $flag ? $model::where($flag, true)->count() : $model::count();
        };

        $counts = [
            'services'  => ['total' => $count(Service::class),    'live' => $count(Service::class, 'is_active')],
            'projects'  => ['total' => $count(Project::class),    'live' => $count(Project::class, 'is_published')],
            'clients'   => ['total' => $count(Client::class),     'live' => $count(Client::class, 'is_active')],
            'members'   => ['total' => $count(TeamMember::class), 'live' => $count(TeamMember::class, 'is_active')],
            'divisions' => $count(Division::class),
        ];

        $projects = $has(Project::class) ? Project::latest('id')->take(6)->get() : collect();
        $clients  = $has(Client::class) ? Client::latest('id')->take(6)->get() : collect();

        return view('admin.dashboard', compact('counts', 'projects', 'clients'));
    }
}