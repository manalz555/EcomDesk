<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Chat — {{ config('app.name', 'EcomDesk') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
{{-- Standalone page loaded inside the iframe injected by /widget.js — it must
     never assume anything about the host site (its CSS, its scripts). --}}
<body class="h-screen overflow-hidden bg-white font-sans antialiased">
<div
    class="flex h-full flex-col"
    x-data="{
        token: localStorage.getItem('ecomdesk_widget_token'),
        messages: [],
        statut: null,
        draft: '',
        sending: false,
        polling: null,

        init() {
            if (this.token) this.fetchMessages();
            // Poll only while the widget is open (this page only exists when open).
            this.polling = setInterval(() => { if (this.token) this.fetchMessages(); }, 3500);
        },

        async fetchMessages() {
            try {
                const res = await fetch(`{{ route('widget.messages') }}?token=${encodeURIComponent(this.token)}`, { headers: { 'Accept': 'application/json' } });
                if (!res.ok) return;
                const data = await res.json();
                const grew = data.messages.length > this.messages.length;
                this.messages = data.messages;
                this.statut = data.statut ?? null;
                if (grew) this.$nextTick(() => this.scrollDown());
            } catch (e) { /* offline: silently retry on next tick */ }
        },

        async send() {
            const text = this.draft.trim();
            if (!text || this.sending) return;
            this.sending = true;
            // Optimistic append so the visitor sees their message instantly.
            this.messages.push({ from: 'client', text, at: new Date().toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' }) });
            this.draft = '';
            this.$nextTick(() => this.scrollDown());
            try {
                const res = await fetch(`{{ route('widget.send') }}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ token: this.token, message: text }),
                });
                if (res.ok) {
                    const data = await res.json();
                    this.token = data.token;
                    localStorage.setItem('ecomdesk_widget_token', data.token);
                }
            } finally {
                this.sending = false;
            }
        },

        scrollDown() { this.$refs.list.scrollTop = this.$refs.list.scrollHeight; },
    }"
>
    <header class="flex shrink-0 items-center gap-3 bg-ink-900 px-4 py-3.5">
        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-sand-100 text-sm font-bold text-ink-900">E</span>
        <div class="min-w-0 flex-1">
            <p class="truncate text-sm font-semibold text-sand-50">{{ \App\Models\WorkspaceSetting::current()->company_name ?? 'EcomDesk' }}</p>
            <p class="flex items-center gap-1.5 text-xs text-sand-400">
                <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                Nous répondons en quelques minutes
            </p>
        </div>
    </header>

    <div x-ref="list" class="flex-1 space-y-3 overflow-y-auto overscroll-contain scrollbar-gold bg-sand-50 p-4">
        <template x-if="messages.length === 0">
            <div class="rounded-2xl rounded-tl-sm bg-white p-3.5 text-sm text-ink-700 shadow-sm">
                Bonjour&nbsp;! Posez-nous votre question, un membre de l'équipe vous répond ici même.
            </div>
        </template>

        <template x-for="(m, i) in messages" :key="i">
            <div :class="m.from === 'client' ? 'flex justify-end' : 'flex justify-start'">
                <div class="max-w-[85%]">
                    <template x-if="m.from === 'agent' && m.author">
                        <p class="mb-0.5 pl-1 text-[11px] font-medium text-ink-400" x-text="m.author"></p>
                    </template>
                    <div
                        :class="m.from === 'client'
                            ? 'rounded-2xl rounded-br-sm bg-ink-900 text-sand-50'
                            : 'rounded-2xl rounded-tl-sm bg-white text-ink-800 shadow-sm'"
                        class="px-3.5 py-2.5 text-sm leading-relaxed"
                    >
                        <span x-text="m.text"></span>
                    </div>
                    <p class="mt-0.5 px-1 text-[10px] text-ink-300" :class="m.from === 'client' ? 'text-right' : ''" x-text="m.at"></p>
                </div>
            </div>
        </template>

        <template x-if="statut === 'resolu'">
            <p class="pt-1 text-center text-xs text-ink-400">Conversation résolue — un nouveau message ouvrira une nouvelle demande.</p>
        </template>
    </div>

    <form @submit.prevent="send" data-no-spinner class="flex shrink-0 items-end gap-2 border-t border-sand-200 bg-white p-3">
        <textarea
            x-model="draft"
            @keydown.enter.prevent="send"
            rows="1"
            placeholder="Écrivez votre message…"
            class="max-h-24 flex-1 resize-none rounded-xl border-sand-200 bg-sand-50 px-3.5 py-2.5 text-sm text-ink-900 placeholder:text-ink-400 focus:border-ink-400 focus:ring-ink-400"
        ></textarea>
        <button
            type="submit"
            :disabled="sending || !draft.trim()"
            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-ink-900 text-sand-50 transition hover:bg-ink-700 disabled:opacity-40"
            aria-label="Envoyer"
        >
            <svg class="h-4.5 w-4.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.998 12Zm0 0h7.5" />
            </svg>
        </button>
    </form>
</div>
</body>
</html>
