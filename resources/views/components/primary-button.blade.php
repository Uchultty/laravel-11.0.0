<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-brand-600 to-cyan-500 px-4 py-2 text-sm font-semibold text-white shadow transition hover:from-brand-700 hover:to-cyan-600 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2']) }}>
    {{ $slot }}
</button>
