<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingSeeder extends Seeder
{
    public function run(): void
    {
        $s = SiteSetting::current();

        $s->site_name = 'Dream Ratio';
        $s->instagram_url = 'https://www.instagram.com/dream_ratio';

        $s->setTranslations('tagline', [
            'id' => 'We Animate Dreams',
            'en' => 'We Animate Dreams',
        ]);

        $s->setTranslations('heart_statement', [
            'id' => 'Kami percaya masa depan animasi adalah lebih cepat dan lebih cerdas, digerakkan oleh inovasi, ditenagai kreativitas, dan tidak pernah dibatasi oleh konvensi.',
            'en' => 'We believe the future of animation is faster and smarter, driven by innovation, powered by creativity, and never limited by convention.',
        ]);

        $s->setTranslations('impact_statement', [
            'id' => 'Dream Ratio membantu mewujudkan visi Anda menjadi nyata, lebih cepat dan lebih cerdas. Dengan alur produksi yang inovatif, kami membantu menghemat waktu, menekan biaya, dan menghasilkan animasi berstandar dunia, sehingga Anda bisa fokus pada cerita sementara kami menangani kecepatan dan ketelitian.',
            'en' => 'Dream Ratio empowers you to bring your vision to life, faster and smarter. With a groundbreaking production pipeline, we help you save valuable time, reduce costs, and deliver animation that meets world-class standards, so you can focus on storytelling while we handle the speed and precision.',
        ]);

        $s->setTranslations('about_text', [
            'id' => 'Dream Ratio adalah studio berbasis di Jakarta, Indonesia, sekelompok kreator R&D dengan pengalaman puluhan tahun di industri animasi, yang digerakkan oleh kekuatan kreativitas dan inovasi. Dengan memadukan teknologi dan seni, kami melampaui batas tradisional untuk menghasilkan animasi yang berkualitas tinggi sekaligus sangat efisien.',
            'en' => 'Dream Ratio is a studio based in Jakarta, Indonesia, a group of R&D creatives with decades of experience in the animation industry, driven by the transformative power of creativity and innovation. By merging technology with art, we break traditional boundaries to craft animation that is high in quality and also high in efficiency.',
        ]);

        $s->save();
    }
}