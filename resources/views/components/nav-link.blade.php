@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-4 py-1.5 rounded-full bg-yellow-400/30 text-sm font-medium leading-5 text-yellow-900 focus:outline-none transition duration-150 ease-in-out'
            : 'inline-flex items-center px-4 py-1.5 rounded-full bg-black/30 text-sm font-medium leading-5 text-white-500 hover:bg-black/40 focus:outline-none transition duration-150 ease-in-out';
@endphp
<a {{ $attributes->merge(['class' => $classes]) }} 
   style="{{ ($active ?? false) ? 'background-color: rgba(234, 179, 8, 0.3); color: #713f12; margin:2' : 'background-color: rgba(0, 0, 0, 0.3); color: #e5e7eb;' }}">
    {{ $slot }}
</a>