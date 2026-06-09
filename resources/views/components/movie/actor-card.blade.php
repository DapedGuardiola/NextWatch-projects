@props([
'image_url'=>null,
'name'=>null,
'actor_id'=>null,])
<a href="{{route('actor.detail',$actor_id)}}" class="shrink-0 block w-20 md:w-24 lg:w-36 relative group items-center">
    <div class="rounded-full aspect-[3/3] overflow-hidden">
    <img src="{{ $image_url }}" alt="{{ $name }}" loading="lazy" class="shrink-0 w-full h-full object-cover object-center">
    </div>
    <p class="mt-2 text-white text-center w-full font-bold truncate text-sm md:text-base lg:text-lg">{{ $name }}</p>
</a>