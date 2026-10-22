{{-- Shared <head> assets: theme bootstrap (no flash), fonts/icons/Alpine via Vite. --}}
<script>
    (function () {
        try {
            var saved = localStorage.getItem('theme');
            var dark = saved === 'dark' || (!saved && window.matchMedia('(prefers-color-scheme: dark)').matches);
            document.documentElement.classList.toggle('dark', dark);
        } catch (e) {}
    })();
</script>
@vite(array_filter(['resources/css/app.css', 'resources/js/app.js', ($landing ?? false) ? 'resources/js/landing.js' : null]))
