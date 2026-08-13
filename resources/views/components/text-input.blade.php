@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-brand-200 focus:border-brand-400 focus:ring-brand-300 rounded-md shadow-sm']) }}>
