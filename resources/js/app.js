import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

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
