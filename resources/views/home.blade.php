@extends('layouts.app')

@section('title', __('Beranda'))

@section('content')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        .hm {
            --hm-ink: #14213d;
            --hm-ink-soft: #3d4a66;
            --hm-paper: #fbf7f0;
            --hm-paper-2: #f3ece0;
            --hm-accent: #ff6b4a;
            --hm-accent-2: #ffb703;
            --hm-teal: #2a9d8f;
            --hm-line: rgba(20, 33, 61, .12);
            --hm-radius: 20px;
            --hm-font-display: 'Fraunces', Georgia, serif;
            --hm-font-body: 'Plus Jakarta Sans', system-ui, -apple-system, 'Segoe UI', sans-serif;

            font-family: var(--hm-font-body);
            color: var(--hm-ink);
            background: var(--hm-paper);
            line-height: 1.7;
            overflow-x: clip;
        }
        .hm *, .hm *::before, .hm *::after { box-sizing: border-box; }
        .hm h1, .hm h2, .hm h3 { font-family: var(--hm-font-display); line-height: 1.12; margin: 0; letter-spacing: -.015em; }
        .hm p { margin: 0; }
        .hm-wrap { width: min(1160px, 100% - 48px); margin-inline: auto; }

        /* ---------- Eyebrow ---------- */
        .hm-eyebrow {
            display: inline-flex; align-items: center; gap: .6rem;
            font-size: .78rem; font-weight: 700; letter-spacing: .16em; text-transform: uppercase;
            color: var(--hm-accent);
        }
        .hm-eyebrow::before { content: ""; width: 34px; height: 2px; background: currentColor; border-radius: 2px; }

        /* ---------- Buttons ---------- */
        .hm-btn {
            display: inline-flex; align-items: center; gap: .6rem;
            padding: .95rem 1.6rem; border-radius: 999px; text-decoration: none;
            font-weight: 600; font-size: .98rem; border: 2px solid transparent;
            transition: transform .2s ease, box-shadow .2s ease, background .2s ease, color .2s ease;
        }
        .hm-btn svg { width: 18px; height: 18px; transition: transform .2s ease; }
        .hm-btn:hover svg { transform: translateX(4px); }
        .hm-btn:focus-visible { outline: 3px solid var(--hm-accent-2); outline-offset: 3px; }
        .hm-btn-primary { background: var(--hm-ink); color: #fff; box-shadow: 0 10px 24px -10px rgba(20,33,61,.6); }
        .hm-btn-primary:hover { transform: translateY(-2px); background: #0d1730; color: #fff; }
        .hm-btn-outline { border-color: var(--hm-ink); color: var(--hm-ink); background: transparent; }
        .hm-btn-outline:hover { background: var(--hm-ink); color: #fff; transform: translateY(-2px); }
        .hm-btn-light { background: #fff; color: var(--hm-ink); }
        .hm-btn-light:hover { transform: translateY(-2px); box-shadow: 0 12px 28px -12px rgba(0,0,0,.5); color: var(--hm-ink); }
        .hm-btn-ghost { border-color: rgba(255,255,255,.55); color: #fff; }
        .hm-btn-ghost:hover { background: rgba(255,255,255,.14); color: #fff; transform: translateY(-2px); }

        /* ---------- Hero ---------- */
        .hm-hero { position: relative; padding: clamp(56px, 9vw, 120px) 0 clamp(56px, 8vw, 100px); }
        .hm-hero::before {
            content: ""; position: absolute; inset: 0; pointer-events: none;
            background:
                radial-gradient(circle at 85% 10%, rgba(255,183,3,.22), transparent 42%),
                radial-gradient(circle at 8% 90%, rgba(42,157,143,.16), transparent 40%);
        }
        .hm-hero-grid { position: relative; display: grid; grid-template-columns: 1.05fr .95fr; gap: clamp(32px, 6vw, 80px); align-items: center; }
        .hm-hero h1 { font-size: clamp(2.4rem, 5.4vw, 4.4rem); font-weight: 700; margin: 1.1rem 0 1.4rem; }
        .hm-hero h1 .hm-underline { background: linear-gradient(transparent 68%, rgba(255,183,3,.55) 68% 92%, transparent 92%); }
        .hm-lead { font-size: clamp(1.02rem, 1.6vw, 1.2rem); color: var(--hm-ink-soft); max-width: 56ch; }
        .hm-actions { display: flex; flex-wrap: wrap; gap: 14px; margin-top: 2rem; }

        /* Hero visual: bingkai film 16:9 */
        .hm-visual { position: relative; aspect-ratio: 1 / 1; max-width: 520px; margin-inline: auto; width: 100%; }
        .hm-frame {
            position: absolute; inset: 9% 0 9% 6%;
            border-radius: 28px; overflow: hidden;
            background: linear-gradient(135deg, #14213d 0%, #26386a 55%, #3b2f6b 100%);
            box-shadow: 0 40px 70px -30px rgba(20,33,61,.7);
            transform: rotate(-3deg);
        }
        .hm-frame::before { /* lubang film */
            content: ""; position: absolute; inset: 14px auto 14px 14px; width: 12px;
            background: repeating-linear-gradient(to bottom, rgba(255,255,255,.35) 0 14px, transparent 14px 28px);
            border-radius: 4px;
        }
        .hm-frame::after {
            content: ""; position: absolute; inset: 14px 14px 14px auto; width: 12px;
            background: repeating-linear-gradient(to bottom, rgba(255,255,255,.35) 0 14px, transparent 14px 28px);
            border-radius: 4px;
        }
        .hm-scene { position: absolute; inset: 0 40px; display: grid; place-items: center; }
        .hm-sun { width: 34%; aspect-ratio: 1; border-radius: 50%; background: radial-gradient(circle at 35% 35%, #ffd166, var(--hm-accent)); box-shadow: 0 0 60px 10px rgba(255,107,74,.55); animation: hm-float 6s ease-in-out infinite; }
        .hm-hill { position: absolute; left: -10%; right: -10%; bottom: -20%; height: 46%; background: #1b2d57; border-radius: 50% 50% 0 0 / 100% 100% 0 0; }
        .hm-hill.two { bottom: -28%; height: 44%; background: #101c3a; left: 10%; right: -30%; }
        .hm-star { position: absolute; width: 6px; height: 6px; border-radius: 50%; background: #fff; opacity: .8; animation: hm-twinkle 3s ease-in-out infinite; }
        .hm-star:nth-child(1) { top: 18%; left: 20%; }
        .hm-star:nth-child(2) { top: 28%; right: 18%; animation-delay: .8s; }
        .hm-star:nth-child(3) { top: 12%; right: 42%; animation-delay: 1.6s; }
        .hm-star:nth-child(4) { top: 40%; left: 12%; animation-delay: 2.2s; }

        .hm-chip {
            position: absolute; display: inline-flex; align-items: center; gap: .55rem;
            padding: .6rem 1rem; border-radius: 999px; background: #fff; color: var(--hm-ink);
            font-size: .82rem; font-weight: 700; box-shadow: 0 16px 30px -14px rgba(20,33,61,.45);
            animation: hm-float 7s ease-in-out infinite;
        }
        .hm-chip i { width: 10px; height: 10px; border-radius: 50%; display: block; }
        .hm-chip.a { top: 2%; left: 0; }
        .hm-chip.a i { background: var(--hm-accent); }
        .hm-chip.b { bottom: 6%; right: 0; animation-delay: 1.2s; }
        .hm-chip.b i { background: var(--hm-teal); }
        .hm-chip.c { top: 46%; left: -4%; animation-delay: 2.4s; }
        .hm-chip.c i { background: var(--hm-accent-2); }

        @keyframes hm-float { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }
        @keyframes hm-twinkle { 0%,100% { opacity: .25; transform: scale(.8); } 50% { opacity: 1; transform: scale(1.3); } }

        /* ---------- Marquee ---------- */
        .hm-marquee { background: var(--hm-ink); color: #fff; overflow: hidden; padding: 1.05rem 0; transform: rotate(-1deg); margin: 0 -2%; }
        .hm-track { display: flex; gap: 3rem; width: max-content; animation: hm-scroll 28s linear infinite; }
        .hm-track span { font-family: var(--hm-font-display); font-size: 1.25rem; white-space: nowrap; display: inline-flex; align-items: center; gap: 3rem; }
        .hm-track span::after { content: "✦"; color: var(--hm-accent-2); font-size: 1rem; }
        @keyframes hm-scroll { to { transform: translateX(-50%); } }

        /* ---------- Section dasar ---------- */
        .hm-section { padding: clamp(64px, 9vw, 120px) 0; }
        .hm-head { max-width: 680px; margin-bottom: clamp(32px, 5vw, 56px); }
        .hm-head h2 { font-size: clamp(1.9rem, 3.8vw, 3rem); margin: .9rem 0 1rem; font-weight: 600; }
        .hm-head p { color: var(--hm-ink-soft); font-size: 1.05rem; }

        /* ---------- Heart statement ---------- */
        .hm-heart { background: var(--hm-paper-2); position: relative; }
        .hm-heart-grid { display: grid; grid-template-columns: .8fr 1.2fr; gap: clamp(28px, 6vw, 80px); align-items: center; }
        .hm-quote-mark { font-family: var(--hm-font-display); font-size: clamp(7rem, 14vw, 11rem); line-height: .7; color: var(--hm-accent); opacity: .9; }
        .hm-heart blockquote { margin: 0; font-family: var(--hm-font-display); font-size: clamp(1.4rem, 2.8vw, 2.1rem); line-height: 1.4; font-weight: 500; }
        .hm-heart-by { margin-top: 1.4rem; display: flex; align-items: center; gap: .8rem; font-size: .9rem; font-weight: 600; color: var(--hm-ink-soft); }
        .hm-heart-by::before { content: ""; width: 44px; height: 2px; background: var(--hm-accent); }

        /* ---------- Pilar ---------- */
        .hm-pillars { display: grid; grid-template-columns: repeat(3, 1fr); gap: 22px; }
        .hm-card {
            position: relative; padding: 2rem 1.8rem 2.1rem; border-radius: var(--hm-radius);
            background: #fff; border: 1px solid var(--hm-line);
            transition: transform .25s ease, box-shadow .25s ease;
        }
        .hm-card:hover { transform: translateY(-6px); box-shadow: 0 26px 44px -26px rgba(20,33,61,.45); }
        .hm-icon { width: 56px; height: 56px; border-radius: 16px; display: grid; place-items: center; margin-bottom: 1.3rem; }
        .hm-icon svg { width: 28px; height: 28px; }
        .hm-card:nth-child(1) .hm-icon { background: rgba(255,107,74,.14); color: var(--hm-accent); }
        .hm-card:nth-child(2) .hm-icon { background: rgba(42,157,143,.14); color: var(--hm-teal); }
        .hm-card:nth-child(3) .hm-icon { background: rgba(255,183,3,.2); color: #b57f00; }
        .hm-card h3 { font-size: 1.35rem; margin-bottom: .6rem; }
        .hm-card p { color: var(--hm-ink-soft); font-size: .98rem; }

        /* ---------- Alur produksi ---------- */
        .hm-flow { background: #fff; border-block: 1px solid var(--hm-line); }
        .hm-steps { list-style: none; margin: 0; padding: 0; display: grid; grid-template-columns: repeat(4, 1fr); gap: 22px; position: relative; counter-reset: s; }
        .hm-steps::before { content: ""; position: absolute; top: 27px; left: 5%; right: 5%; border-top: 2px dashed rgba(20,33,61,.22); }
        .hm-step { position: relative; counter-increment: s; }
        .hm-step::before {
            content: counter(s, decimal-leading-zero);
            display: grid; place-items: center; width: 56px; height: 56px; border-radius: 50%;
            background: var(--hm-paper); border: 2px solid var(--hm-ink); font-family: var(--hm-font-display); font-weight: 700;
            position: relative; z-index: 1; margin-bottom: 1.2rem; transition: background .25s ease, color .25s ease, transform .25s ease;
        }
        .hm-step:hover::before { background: var(--hm-accent); border-color: var(--hm-accent); color: #fff; transform: scale(1.08); }
        .hm-step h3 { font-size: 1.15rem; margin-bottom: .4rem; }
        .hm-step p { font-size: .94rem; color: var(--hm-ink-soft); }
        .hm-flow-cta { margin-top: clamp(32px, 5vw, 52px); }

        /* ---------- CTA ---------- */
        .hm-cta-wrap { padding: clamp(48px, 7vw, 90px) 0 clamp(64px, 9vw, 120px); }
        .hm-cta {
            position: relative; overflow: hidden; border-radius: 32px; color: #fff;
            padding: clamp(40px, 7vw, 84px) clamp(24px, 6vw, 80px);
            background: linear-gradient(120deg, #14213d 0%, #2c2a6b 60%, #5a2d7a 100%);
        }
        .hm-cta::before, .hm-cta::after { content: ""; position: absolute; border-radius: 50%; pointer-events: none; }
        .hm-cta::before { width: 340px; height: 340px; right: -90px; top: -120px; background: radial-gradient(circle, rgba(255,107,74,.7), transparent 70%); }
        .hm-cta::after { width: 280px; height: 280px; left: -80px; bottom: -130px; background: radial-gradient(circle, rgba(255,183,3,.45), transparent 70%); }
        .hm-cta > * { position: relative; z-index: 1; }
        .hm-cta h2 { font-size: clamp(1.8rem, 3.8vw, 3rem); max-width: 18ch; }
        .hm-cta p { margin-top: 1rem; max-width: 54ch; color: rgba(255,255,255,.82); font-size: 1.05rem; }
        .hm-cta .hm-actions { margin-top: 1.8rem; }

        /* ---------- Reveal on scroll ---------- */
        .hm-reveal { opacity: 0; transform: translateY(24px); transition: opacity .7s ease, transform .7s ease; }
        .hm-reveal.is-in { opacity: 1; transform: none; }

        /* ---------- Responsive ---------- */
        @media (max-width: 960px) {
            .hm-hero-grid, .hm-heart-grid { grid-template-columns: 1fr; }
            .hm-visual { max-width: 420px; order: -1; }
            .hm-pillars { grid-template-columns: 1fr; }
            .hm-steps { grid-template-columns: repeat(2, 1fr); row-gap: 36px; }
            .hm-steps::before { display: none; }
            .hm-quote-mark { height: 80px; }
        }
        @media (max-width: 560px) {
            .hm-wrap { width: calc(100% - 32px); }
            .hm-steps { grid-template-columns: 1fr; }
            .hm-actions .hm-btn { width: 100%; justify-content: center; }
            .hm-chip.c { display: none; }
        }
        @media (prefers-reduced-motion: reduce) {
            .hm *, .hm *::before, .hm *::after { animation: none !important; transition: none !important; }
            .hm-reveal { opacity: 1; transform: none; }
        }
    </style>

    <div class="hm">

        {{-- ================= HERO ================= --}}
        <section class="hm-hero">
            <div class="hm-wrap hm-hero-grid">
                <div>
                    <span class="hm-eyebrow">{{ __('Studio Animasi') }}</span>
                    <h1><span class="hm-underline">{{ $site->tagline ?: $site->site_name }}</span></h1>
                    @if (!empty($site->heart_statement))
                        <p class="hm-lead">{{ $site->heart_statement }}</p>
                    @endif

                    <div class="hm-actions">
                        <a href="{{ route('pipeline') }}" class="hm-btn hm-btn-primary">
                            {{ __('Alur Produksi') }}
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                        </a>
                        <a href="{{ route('about') }}" class="hm-btn hm-btn-outline">{{ __('Tentang Kami') }}</a>
                    </div>
                </div>

                <div class="hm-visual" aria-hidden="true">
                    <div class="hm-frame">
                        <div class="hm-scene">
                            <span class="hm-star"></span><span class="hm-star"></span><span class="hm-star"></span><span class="hm-star"></span>
                            <div class="hm-sun"></div>
                            <div class="hm-hill"></div>
                            <div class="hm-hill two"></div>
                        </div>
                    </div>
                    <span class="hm-chip a"><i></i>{{ __('Cerita') }}</span>
                    <span class="hm-chip c"><i></i>{{ __('Karakter') }}</span>
                    <span class="hm-chip b"><i></i>{{ __('Dunia') }}</span>
                </div>
            </div>
        </section>

        {{-- ================= MARQUEE ================= --}}
        <div class="hm-marquee" aria-hidden="true">
            <div class="hm-track">
                @for ($i = 0; $i < 2; $i++)
                    <span>{{ __('Naskah & Cerita') }}</span>
                    <span>{{ __('Desain Karakter') }}</span>
                    <span>{{ __('Storyboard') }}</span>
                    <span>{{ __('Animasi') }}</span>
                    <span>{{ __('Warna & Suara') }}</span>
                    <span>{{ __('Pasca Produksi') }}</span>
                @endfor
            </div>
        </div>

        {{-- ================= HEART STATEMENT ================= --}}
        @if (!empty($site->heart_statement))
            <section class="hm-section hm-heart">
                <div class="hm-wrap hm-heart-grid">
                    <div class="hm-quote-mark hm-reveal" aria-hidden="true">“</div>
                    <div class="hm-reveal">
                        <span class="hm-eyebrow">{{ __('Hati Kami') }}</span>
                        <blockquote style="margin-top:1.1rem">{{ $site->heart_statement }}</blockquote>
                        <div class="hm-heart-by">{{ $site->site_name }}</div>
                    </div>
                </div>
            </section>
        @endif

        {{-- ================= PILAR ================= --}}
        <section class="hm-section">
            <div class="hm-wrap">
                <div class="hm-head hm-reveal">
                    <span class="hm-eyebrow">{{ __('Cara Kami Bekerja') }}</span>
                    <h2>{{ __('Kreatif dalam gagasan, rapi dalam proses.') }}</h2>
                    <p>{{ __('Setiap karya lahir dari imajinasi yang dikawal dengan disiplin produksi, supaya hasil akhirnya indah sekaligus tepat waktu.') }}</p>
                </div>

                <div class="hm-pillars">
                    <article class="hm-card hm-reveal">
                        <div class="hm-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3l2.6 5.6 6.1.7-4.5 4.2 1.2 6L12 16.5 6.6 19.5l1.2-6L3.3 9.3l6.1-.7L12 3z"/></svg>
                        </div>
                        <h3>{{ __('Cerita yang Bermakna') }}</h3>
                        <p>{{ __('Kami memulai dari gagasan dan pesan, lalu membentuknya menjadi alur yang menyentuh penonton.') }}</p>
                    </article>

                    <article class="hm-card hm-reveal">
                        <div class="hm-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4 21c1-4.2 4-6 8-6s7 1.8 8 6"/></svg>
                        </div>
                        <h3>{{ __('Karakter yang Hidup') }}</h3>
                        <p>{{ __('Setiap tokoh dirancang dengan kepribadian, gerak, dan ekspresi yang khas serta mudah diingat.') }}</p>
                    </article>

                    <article class="hm-card hm-reveal">
                        <div class="hm-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 17l5-7 4 5 3-4 6 6H3z"/><circle cx="17" cy="6" r="2"/></svg>
                        </div>
                        <h3>{{ __('Dunia yang Utuh') }}</h3>
                        <p>{{ __('Latar, warna, dan suasana disusun konsisten agar penonton benar-benar masuk ke dalam dunia ceritanya.') }}</p>
                    </article>
                </div>
            </div>
        </section>

        {{-- ================= ALUR PRODUKSI ================= --}}
        <section class="hm-section hm-flow">
            <div class="hm-wrap">
                <div class="hm-head hm-reveal">
                    <span class="hm-eyebrow">{{ __('Alur Produksi') }}</span>
                    <h2>{{ __('Dari ide sampai layar, langkahnya jelas.') }}</h2>
                    <p>{{ __('Empat tahap utama yang membuat kolaborasi transparan dan hasil dapat dipertanggungjawabkan.') }}</p>
                </div>

                <ol class="hm-steps">
                    <li class="hm-step hm-reveal">
                        <h3>{{ __('Ide & Naskah') }}</h3>
                        <p>{{ __('Menggali konsep, menyusun cerita, dan menyepakati arah kreatif.') }}</p>
                    </li>
                    <li class="hm-step hm-reveal">
                        <h3>{{ __('Desain & Storyboard') }}</h3>
                        <p>{{ __('Merancang karakter, dunia, dan urutan visual setiap adegan.') }}</p>
                    </li>
                    <li class="hm-step hm-reveal">
                        <h3>{{ __('Produksi Animasi') }}</h3>
                        <p>{{ __('Menghidupkan gambar menjadi gerak dengan kontrol kualitas berkala.') }}</p>
                    </li>
                    <li class="hm-step hm-reveal">
                        <h3>{{ __('Pasca Produksi') }}</h3>
                        <p>{{ __('Penyempurnaan warna, suara, dan finalisasi untuk dirilis.') }}</p>
                    </li>
                </ol>

                <div class="hm-flow-cta hm-reveal">
                    <a href="{{ route('pipeline') }}" class="hm-btn hm-btn-outline">
                        {{ __('Lihat Alur Selengkapnya') }}
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                    </a>
                </div>
            </div>
        </section>

        {{-- ================= CTA ================= --}}
        <section class="hm-cta-wrap">
            <div class="hm-wrap">
                <div class="hm-cta hm-reveal">
                    <h2>{{ __('Mari wujudkan cerita Anda bersama kami.') }}</h2>
                    <p>{{ __('Kenali lebih dekat siapa kami, bagaimana kami bekerja, dan nilai yang kami pegang dalam setiap karya.') }}</p>
                    <div class="hm-actions">
                        <a href="{{ route('about') }}" class="hm-btn hm-btn-light">
                            {{ __('Tentang Kami') }}
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                        </a>
                        <a href="{{ route('pipeline') }}" class="hm-btn hm-btn-ghost">{{ __('Alur Produksi') }}</a>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script>
        (function () {
            var items = document.querySelectorAll('.hm-reveal');
            if (!('IntersectionObserver' in window)) {
                items.forEach(function (el) { el.classList.add('is-in'); });
                return;
            }
            var io = new IntersectionObserver(function (entries) {
                entries.forEach(function (e) {
                    if (e.isIntersecting) { e.target.classList.add('is-in'); io.unobserve(e.target); }
                });
            }, { threshold: 0.12 });
            items.forEach(function (el, i) {
                el.style.transitionDelay = (i % 3) * 90 + 'ms';
                io.observe(el);
            });
        })();
    </script>
@endsection