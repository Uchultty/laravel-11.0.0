<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center rounded-xl border border-ink-300 bg-white px-4 py-2 text-sm font-semibold text-ink-700 transition hover:bg-ink-100 focus:outline-none focus:ring-2 focus:ring-ink-400 focus:ring-offset-2 disabled:opacity-25']) }}>
    {{ $slot }}
</button>
