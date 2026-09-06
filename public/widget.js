/**
 * EcomDesk — widget de chat embarquable.
 * Usage sur n'importe quel site :
 *   <script src="https://VOTRE-DOMAINE/widget.js" defer></script>
 * Le script injecte une bulle flottante ; le chat lui-même vit dans une
 * iframe pointant vers /widget/frame, totalement isolée du site hôte
 * (aucun conflit de CSS ni de JS possible).
 */
(function () {
    if (window.__ecomdeskWidgetLoaded) return;
    window.__ecomdeskWidgetLoaded = true;

    // L'origine d'EcomDesk est déduite de l'URL du script lui-même,
    // pour que le même fichier fonctionne quel que soit le domaine hôte.
    var script = document.currentScript || (function () {
        var s = document.getElementsByTagName('script');
        return s[s.length - 1];
    })();
    var origin = new URL(script.src).origin;

    var open = false;

    // --- Bulle flottante ---
    var button = document.createElement('button');
    button.setAttribute('aria-label', 'Ouvrir le chat');
    button.style.cssText = [
        'position:fixed', 'bottom:24px', 'right:24px', 'z-index:2147483000',
        'width:56px', 'height:56px', 'border:none', 'border-radius:50%',
        'background:#0e0d0b', 'color:#f8f4ec', 'cursor:pointer',
        'box-shadow:0 8px 24px rgba(14,13,11,.35)',
        'display:flex', 'align-items:center', 'justify-content:center',
        'transition:transform .2s ease, box-shadow .2s ease',
    ].join(';');
    button.innerHTML = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>';
    button.onmouseenter = function () { button.style.transform = 'scale(1.08)'; };
    button.onmouseleave = function () { button.style.transform = 'scale(1)'; };

    // --- Panneau (iframe) ---
    var frame = document.createElement('iframe');
    frame.title = 'Chat EcomDesk';
    frame.style.cssText = [
        'position:fixed', 'bottom:92px', 'right:24px', 'z-index:2147483000',
        'width:min(380px, calc(100vw - 32px))', 'height:min(560px, calc(100vh - 120px))',
        'border:none', 'border-radius:16px',
        'box-shadow:0 12px 40px rgba(14,13,11,.3)',
        'opacity:0', 'transform:translateY(8px)', 'pointer-events:none',
        'transition:opacity .25s ease, transform .25s ease',
    ].join(';');

    function toggle() {
        open = !open;
        if (open && !frame.src) frame.src = origin + '/widget/frame';
        frame.style.opacity = open ? '1' : '0';
        frame.style.transform = open ? 'translateY(0)' : 'translateY(8px)';
        frame.style.pointerEvents = open ? 'auto' : 'none';
        button.setAttribute('aria-label', open ? 'Fermer le chat' : 'Ouvrir le chat');
    }

    button.addEventListener('click', toggle);

    function mount() {
        document.body.appendChild(frame);
        document.body.appendChild(button);
    }

    if (document.body) mount();
    else document.addEventListener('DOMContentLoaded', mount);
})();
