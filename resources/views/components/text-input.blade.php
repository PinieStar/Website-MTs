@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'rounded-lg border-line bg-bone text-ink shadow-sm focus:border-teal-700 focus:ring-teal-700']) }}>
