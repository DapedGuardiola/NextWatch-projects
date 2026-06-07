@props([
'image_url'=>null,
'name'=>null,
'actor_id'=>null,])
<a href="{{route('actor.detail',$actor_id)}}" class="shrink-0 block w-[90px] relative group items-center">
    <div class="rounded-full w-full overflow-hidden">
    <img src="{{ $image_url }}" alt="{{ $name }}" class="shrink-0 aspect-[3/3] w-full object-cover object-center">
    </div>
    <p class="mt-2 text-white text-center w-full font-bold truncate">{{ $name }}</p>
</a>