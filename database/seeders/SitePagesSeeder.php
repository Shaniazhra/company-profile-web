<?php

namespace Database\Seeders;

use App\Models\ContactPerson;
use App\Models\CrewMember;
use App\Models\Faq;
use App\Models\ProcessStage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class SitePagesSeeder extends Seeder
{
    public function run(): void
    {
        if (ProcessStage::count() === 0) {
            $rows = [
                ['Concept', null],
                ['Layout', 'Layout is done in 3D, complete with timing and voice.'],
                ['Animating', 'Directors guide actors live through motion capture.'],
                ['Lighting', 'Light is adjusted in real time and the result shows right away.'],
                ['Render', 'One shot renders in 1 minute on 1 computer, down from 675 minutes.'],
                ['Final Editing', null],
            ];
            foreach ($rows as $i => [$title, $summary]) {
                ProcessStage::create(['title' => $title, 'summary' => $summary, 'sort_order' => $i + 1]);
            }
        }

        if (Faq::count() === 0) {
            $rows = [
                ['What does Dream Ratio do?', 'We are an animation studio with a real-time pipeline. Directors guide actors live through motion capture, lighting is done in real time, and production has fewer stages.'],
                ['How did you cut ten stages to six?', 'Storyboard, animatic, compositing, and grading are no longer separate stages. Layout is done in 3D, complete with timing and voice.'],
                ['How fast is rendering?', 'One shot renders in 1 minute on 1 computer, down from 675 minutes before.'],
                ['How long does a rough compile take?', 'A rough compile of one episode is ready in 3 days.'],
                ['How do I start a project?', 'Call one of us using the numbers at the bottom of this page and tell us what you need.'],
            ];
            foreach ($rows as $i => [$q, $a]) {
                Faq::create(['question' => $q, 'answer' => $a, 'sort_order' => $i + 1]);
            }
        }

        if (CrewMember::count() === 0) {
            // Copies an existing photo from public/images/team into storage so the admin can replace it later.
            $copy = function (string $base) {
                foreach (['png', 'jpg', 'jpeg', 'webp'] as $ext) {
                    $src = public_path("images/team/{$base}.{$ext}");
                    if (file_exists($src)) {
                        $dest = "team/{$base}.{$ext}";
                        Storage::disk('public')->put($dest, file_get_contents($src));

                        return $dest;
                    }
                }

                return null;
            };

            $rows = [
                ["Dawa'i Fathulwally", 'Founder & CEO', 'dawai', ['Operating Man of Manara Animation Studio', 'Visual Effect', 'Supervisor Asset', 'Lumine Studio, Little Giantz']],
                ['Panjiasmoro', 'Founder & COO', 'panjiasmoro', ['Technical Director of Manara Animation Studio', 'Animation Studio Pipeline and Workflow', 'Latest System Development', 'Lumine Studio, Little Giantz']],
                ['Aditya Triantoro', 'Chairman', 'aditya', ['Creator of the animated films Nussa & Rarra and Trungtung', '20+ years in international animation: Oddbods, Rabbids, Lego Star Wars, Rob the Robot', 'Experience on Hollywood feature films such as X-Men and Thor']],
            ];
            foreach ($rows as $i => [$name, $role, $file, $credits]) {
                CrewMember::create([
                    'name' => $name, 'role' => $role, 'photo' => $copy($file),
                    'credits' => implode("\n", $credits), 'sort_order' => $i + 1,
                ]);
            }
        }

        if (ContactPerson::count() === 0) {
            ContactPerson::create(['name' => 'Fairra Tri Agustint', 'phone' => '+62 895 1060 5990', 'sort_order' => 1]);
            ContactPerson::create(['name' => 'Rikky Risdiansah', 'phone' => '+62 812 968 50555', 'sort_order' => 2]);
        }
    }
}