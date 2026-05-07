<div class="shrink-0 w-56 sm:w-64 flex flex-col rounded-xl overflow-hidden">
    @isset($poster)
        <img src="{{ $poster }}" class="w-full h-full object-cover" alt="">
    @endisset
    @isset($title)
        <div class="flex text-sm mt-2"><span class="self-center mx-auto">{{ $title }}</span></div>
    @endisset
</div>