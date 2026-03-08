@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm py-3 px-4 w-full transition-all text-sm font-medium text-slate-700 outline-none']) !!}>