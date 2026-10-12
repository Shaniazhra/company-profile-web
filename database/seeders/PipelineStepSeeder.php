<?php

namespace Database\Seeders;

use App\Models\PipelineStep;
use Illuminate\Database\Seeder;

class PipelineStepSeeder extends Seeder
{
    public function run(): void
    {
        // Kosongkan dulu supaya seeder aman dijalankan berulang kali.
        PipelineStep::truncate();

        $traditional = [
            ['id' => 'Konsep', 'en' => 'Concept', 'points' => [
                'id' => ['Desain karakter', 'Properti', 'Aset 3D', 'Dll'],
                'en' => ['Character design', 'Props', '3D Assets', 'ETC'],
            ]],
            ['id' => 'Storyboard', 'en' => 'Storyboard'],
            ['id' => 'Animatic', 'en' => 'Animatic', 'points' => [
                'id' => ['2D', 'Timing', 'Suara'],
                'en' => ['2D', 'Timing', 'Voice'],
            ]],
            ['id' => 'Layout', 'en' => 'Layout', 'points' => [
                'id' => ['3D', 'Timing', 'Suara'],
                'en' => ['3D', 'Timing', 'Voice'],
            ]],
            ['id' => 'Animasi', 'en' => 'Animating'],
            ['id' => 'Pencahayaan', 'en' => 'Lighting'],
            ['id' => 'Render', 'en' => 'Render'],
            ['id' => 'Compositing', 'en' => 'Compositing'],
            ['id' => 'Color Grading', 'en' => 'Grading'],
            ['id' => 'Penyuntingan Akhir', 'en' => 'Final Editing'],
        ];

        $dreamRatio = [
            ['id' => 'Konsep', 'en' => 'Concept'],
            ['id' => 'Layout', 'en' => 'Layout', 'points' => [
                'id' => ['3D', 'Timing', 'Suara'],
                'en' => ['3D', 'Timing', 'Voice'],
            ]],
            ['id' => 'Animasi', 'en' => 'Animating'],
            ['id' => 'Pencahayaan', 'en' => 'Lighting', 'points' => [
                'id' => ['Pencahayaan real time'],
                'en' => ['Lighting in real time'],
            ]],
            ['id' => 'Render', 'en' => 'Render'],
            ['id' => 'Penyuntingan Akhir', 'en' => 'Final Editing'],
        ];

        $this->insertSteps('traditional', $traditional);
        $this->insertSteps('dream_ratio', $dreamRatio);
    }

    private function insertSteps(string $type, array $steps): void
    {
        foreach ($steps as $index => $step) {
            PipelineStep::create([
                'type' => $type,
                'title' => ['id' => $step['id'], 'en' => $step['en']],
                'sub_points' => $step['points'] ?? ['id' => [], 'en' => []],
                'sort_order' => $index + 1,
                'is_published' => true,
            ]);
        }
    }
}