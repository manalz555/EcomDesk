import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// Scroll reveal: fades [data-reveal] elements up into place the first time they enter the
// viewport (used on the landing page). Reveals once and stays revealed — no re-hiding on scroll-out.
// Note: this is a Vite module script, which runs after the DOM is already parsed (like `defer`),
// so DOMContentLoaded may already have fired by the time this executes — don't wait for it.
// Instant feedback on every classic form submit: the button gets a spinner and
// is disabled so the user sees the app working (and can't double-submit) while
// the full page round-trip happens. Forms that manage their own state (Alpine
// @submit.prevent, e.g. the chat widget) opt out with data-no-spinner.
document.addEventListener('submit', (event) => {
    const form = event.target;
    if (event.defaultPrevented || form.hasAttribute('data-no-spinner')) return;

    const buttons = form.querySelectorAll('button[type="submit"], input[type="submit"]');
    // Disable on the next frame, after the browser has captured the form data —
    // a button disabled synchronously would be excluded from the submission.
    requestAnimationFrame(() => {
        buttons.forEach((btn) => {
            btn.disabled = true;
            btn.classList.add('btn-loading');
        });
    });
});

const revealTargets = document.querySelectorAll('[data-reveal]');

if (revealTargets.length) {
    const revealObserver = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    revealObserver.unobserve(entry.target);
                }
            });
        },
        { threshold: 0.15, rootMargin: '0px 0px -60px 0px' }
    );

    revealTargets.forEach((el) => revealObserver.observe(el));
}
