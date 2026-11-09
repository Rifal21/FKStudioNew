@if (session('success') || session('error'))
    <div @class(['mb-6 flex items-start gap-3 rounded-lg border-2 border-edge p-4 text-small font-semibold', 'bg-mint-50 text-mint-800 dark:bg-mint-400/15 dark:text-mint-300' => session('success'), 'bg-danger-50 text-danger-700 dark:bg-danger-500/15 dark:text-red-300' => session('error')]) role="status">
        <i class="fa-solid {{ session('success') ? 'fa-circle-check' : 'fa-circle-exclamation' }} mt-0.5" aria-hidden="true"></i>
        <span>{{ session('success') ?? session('error') }}</span>
    </div>
@endif
