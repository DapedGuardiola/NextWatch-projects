@props([
'image_url'=>null,
'name'=>null,
'actor_id'=>null,])
<a href="{{route('actor.detail',$actor_id)}}" class="shrink-0 block sm:w-[150px] relative group items-center">
    <div class="rounded-full aspect-[3/3] overflow-hidden">
    <img src="{{ $image_url }}" alt="{{ $name }}" loading="lazy" class="shrink-0 w-full h-full object-cover object-center">
    </div>
    <p class="mt-2 text-white font-bold truncate">{{ $name }}</p>
</a>