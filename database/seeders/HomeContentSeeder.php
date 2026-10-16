<?php

namespace Database\Seeders;

use App\Models\FocusShot;
use App\Models\Highlight;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class HomeContentSeeder extends Seeder
{
    public function run(): void
    {
        if (Highlight::count() === 0) {
            $reasons = [
                ['Fewer stages', 'Ten traditional stages are cut to six, so work moves faster.', 'layers'],
                ['Real-time lighting', 'Light is adjusted and the result shows instantly, so there is less to render.', 'sun'],
                ['Fast rendering', 'One shot in 1 minute on 1 computer, down from 675 minutes.', 'bolt'],
                ['Experienced team', 'Our chairman has 20+ years in international animation.', 'users'],
            ];
            foreach ($reasons as $i => [$t, $x, $ic]) {
                Highlight::create(['group' => 'reason', 'title' => $t, 'text' => $x, 'icon' => $ic, 'sort_order' => $i + 1]);
            }

            $results = [
                'Lighting is done in real time, so rendering is much lighter.',
                'A rough compile of one episode is ready in 3 days.',
                'One shot renders in 1 minute on 1 computer.',
            ];
            foreach ($results as $i => $x) {
                Highlight::create(['group' => 'result', 'title' => null, 'text' => $x, 'icon' => 'check', 'sort_order' => $i + 1]);
            }
        }

        if (FocusShot::count() === 0) {
            foreach (['Lighting' => 'lighting', 'Set dress' => 'set-dress', 'Render' => 'render'] as $title => $base) {
                $stored = null;
                foreach (['png', 'jpg', 'jpeg', 'webp'] as $ext) {
                    $src = public_path("images/focus/{$base}.{$ext}");
                    if (file_exists($src)) {
                        $stored = "focus/{$base}.{$ext}";
                        Storage::disk('public')->put($stored, file_get_contents($src));
                        break;
                    }
                }
                FocusShot::create(['title' => $title, 'image' => $stored, 'sort_order' => count(FocusShot::all()) + 1]);
            }
        }
    }
}