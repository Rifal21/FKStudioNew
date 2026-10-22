import './bootstrap';

import '@fontsource-variable/plus-jakarta-sans';
import '@fontsource-variable/bricolage-grotesque';
import '@fortawesome/fontawesome-free/css/all.min.css';
import 'aos/dist/aos.css';

import Alpine from 'alpinejs';
import AOS from 'aos';
import Swal from 'sweetalert2';

window.Alpine = Alpine;
window.AOS = AOS;
window.Swal = Swal;

/* ---------------------------------------------------------------------------
 * Theme (light / dark). The initial class is applied by an inline script in
 * the layout <head> to avoid a flash; this store only handles toggling.
 * ------------------------------------------------------------------------- */
Alpine.store('theme', {
    dark: document.documentElement.classList.contains('dark'),

    toggle() {
        this.dark = !this.dark;
        document.documentElement.classList.toggle('dark', this.dark);
        try {
            localStorage.setItem('theme', this.dark ? 'dark' : 'light');
        } catch (e) {
            /* storage unavailable (private mode) — the choice just won't persist */
        }
    },
});

/* ---------------------------------------------------------------------------
 * Toasts: Alpine.store('toast').push('success' | 'error' | 'info', 'message')
 * ------------------------------------------------------------------------- */
Alpine.store('toast', {
    items: [],
    seq: 0,

    push(type, message, timeout = 4000) {
        const id = ++this.seq;
        this.items.push({ id, type, message });
        if (timeout) setTimeout(() => this.remove(id), timeout);
    },

    remove(id) {
        this.items = this.items.filter((t) => t.id !== id);
    },
});

window.toast = (type, message, timeout) => Alpine.store('toast').push(type, message, timeout);

/* ---------------------------------------------------------------------------
 * Confirm dialogs: <form data-confirm="Hapus item ini?"> — submits only after
 * the user confirms.
 * ------------------------------------------------------------------------- */
document.addEventListener(
    'submit',
    (event) => {
        const form = event.target;
        if (!(form instanceof HTMLFormElement) || !form.dataset.confirm || form.dataset.confirmed) return;

        event.preventDefault();
        Swal.fire({
            title: form.dataset.confirmTitle || 'Yakin?',
            text: form.dataset.confirm,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: form.dataset.confirmOk || 'Ya, lanjutkan',
            cancelButtonText: 'Batal',
            reverseButtons: true,
        }).then((result) => {
            if (result.isConfirmed) {
                form.dataset.confirmed = '1';
                form.requestSubmit(event.submitter || undefined);
            }
        });
    },
    true,
);

/* ---------------------------------------------------------------------------
 * Scroll animations — skipped when the visitor prefers reduced motion.
 * ------------------------------------------------------------------------- */
document.addEventListener('DOMContentLoaded', () => {
    AOS.init({
        duration: 600,
        once: true,
        offset: 40,
        disable: () => window.matchMedia('(prefers-reduced-motion: reduce)').matches,
    });
});

Alpine.start();
