@once
<style>
    .pg-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 17rem), 1fr)); gap: 1.25rem; }
    .pg-card { display: flex; flex-direction: column; gap: 0.6rem; padding: 1.5rem; background: var(--color-surface); border: 1px solid var(--color-border); border-radius: var(--radius-lg); color: var(--color-text); text-decoration: none; transition: border-color 0.2s; }
    a.pg-card:hover { border-color: var(--color-primary); }
    .pg-card h2, .pg-card h3 { margin: 0; font-size: var(--fs-h3); line-height: 1.25; }
    .pg-card p { margin: 0; font-size: var(--fs-small); color: var(--color-muted); }
    .pg-more { margin-top: auto; padding-top: 0.5rem; font-weight: 600; color: var(--color-primary); }
    .pg-media { position: relative; aspect-ratio: 16 / 9; overflow: hidden; border-radius: var(--radius); background: var(--color-bg); }
    .pg-media img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; }
    .pg-split { display: grid; grid-template-columns: minmax(0, 1.2fr) minmax(0, 1fr); gap: clamp(1.5rem, 4vw, 3rem); align-items: start; }
    .pg-prose { max-width: 62ch; line-height: 1.7; }
    .pg-prose p { margin: 0 0 1rem; }
    .pg-back { display: inline-flex; align-items: center; min-height: 2.75rem; margin-bottom: 0.75rem; color: var(--color-muted); text-decoration: none; }
    .pg-back:hover { color: var(--color-text); }
    .pg-actions { display: flex; flex-wrap: wrap; gap: 0.75rem 1rem; margin-top: 1.5rem; }
    .pg-video { display: block; width: 100%; aspect-ratio: 16 / 9; border: 0; border-radius: var(--radius); }
    .pg-empty { padding: 2rem 0; text-align: center; color: var(--color-muted); }
    .pg-sub { margin: clamp(2.5rem, 6vw, 4rem) 0 1.25rem; font-size: var(--fs-h3); }
    .pg-list { margin: 0; padding: 0; list-style: none; }
    .pg-list li { padding: 0.55rem 0; border-top: 1px solid var(--color-border); font-size: var(--fs-small); color: var(--color-muted); }
    .pg-photo { position: relative; display: grid; place-items: center; aspect-ratio: 4 / 5; overflow: hidden; border-radius: var(--radius-lg); background: var(--color-bg); font-family: var(--font-heading); font-size: var(--fs-h2); font-weight: 600; }
    .pg-photo img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; object-position: center top; }
    .pg-steps { display: grid; gap: 1rem; margin: 0; padding: 0; list-style: none; counter-reset: step; max-width: 52rem; }
    .pg-step { display: grid; grid-template-columns: 3.5rem minmax(0, 1fr); gap: 1rem; padding: 1.25rem; background: var(--color-surface); border: 1px solid var(--color-border); border-radius: var(--radius-lg); counter-increment: step; }
    .pg-step::before { content: counter(step, decimal-leading-zero); display: grid; place-items: center; width: 3.5rem; height: 3.5rem; border-radius: 50%; background: var(--color-primary); color: #fff; font-family: var(--font-heading); font-weight: 600; }
    .pg-step h2 { margin: 0 0 0.35rem; font-size: var(--fs-h4); }
    .pg-step p { margin: 0 0 0.5rem; color: var(--color-muted); }
    .pg-faq { display: grid; gap: 0.75rem; max-width: 48rem; }
    .pg-faq details { background: var(--color-surface); border: 1px solid var(--color-border); border-radius: var(--radius); }
    .pg-faq summary { display: flex; align-items: center; justify-content: space-between; gap: 1rem; min-height: 3.25rem; padding: 0.9rem 1.25rem; font-weight: 600; cursor: pointer; list-style: none; }
    .pg-faq summary::-webkit-details-marker { display: none; }
    .pg-faq summary::after { content: "+"; font-size: var(--fs-h3); line-height: 1; }
    .pg-faq details[open] summary::after { content: "\2212"; }
    .pg-faq details p { margin: 0; padding: 0 1.25rem 1.25rem; color: var(--color-muted); }
    .pg-form { display: grid; gap: 1rem; }
    .pg-field label { display: block; margin-bottom: 0.35rem; font-size: var(--fs-small); font-weight: 600; }
    .pg-field input, .pg-field textarea { display: block; width: 100%; min-height: 2.75rem; padding: 0.65rem 0.85rem; background: var(--color-bg); color: var(--color-text); border: 1px solid var(--color-border); border-radius: var(--radius); font: inherit; }
    .pg-field textarea { min-height: 8rem; resize: vertical; }
    .pg-field [aria-invalid="true"] { border-color: #e5484d; }
    .pg-err { margin: 0.35rem 0 0; font-size: var(--fs-small); color: #ff8a8d; }
    .pg-alert { margin-bottom: 1rem; padding: 0.85rem 1rem; background: var(--color-surface); border: 1px solid var(--color-primary); border-radius: var(--radius); }
    .pg-hp { position: absolute; left: -9999px; width: 1px; height: 1px; overflow: hidden; }
    .pg-contact { display: grid; gap: 0.75rem; margin: 0; padding: 0; list-style: none; }
    a.pg-card:focus-visible, .pg-field input:focus-visible, .pg-field textarea:focus-visible, .pg-faq summary:focus-visible { outline: 3px solid var(--color-primary); outline-offset: 2px; }
    @media (max-width: 800px) { .pg-split { grid-template-columns: 1fr; } }
    @media (prefers-reduced-motion: reduce) { .pg-card { transition: none; } }
</style>
@endonce