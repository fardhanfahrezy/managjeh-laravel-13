<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-4 py-2.5 bg-red-600 hover:bg-red-700 active:bg-red-800 text-white font-bold text-xs rounded-xl shadow-md shadow-red-600/20 focus:outline-none focus:ring-2 focus:ring-red-600/50 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>

