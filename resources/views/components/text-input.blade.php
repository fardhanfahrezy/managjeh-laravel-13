@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-white rounded-xl focus:border-blue-600 focus:ring-2 focus:ring-blue-600 shadow-xs text-sm']) }}>

