@props(['reply', 'movie', 'depth' => 0])

@php $indent = min($depth * 16, 80); @endphp

<div>
    <div class="flex gap-4" style="margin-left: {{ $indent }}px">

        <div class="flex-shrink-0">
            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-cyan-400 to-cyan-600
                        text-black font-bold flex items-center justify-center text-xs
                        shadow-[0_0_20px_rgba(34,211,238,0.35)]">
                {{ strtoupper(substr($reply->user->name ?? 'U', 0, 1)) }}
            </div>
        </div>

        <div class="flex-1">
            <div class="flex items-center gap-3 flex-wrap">
                <h3 class="font-semibold text-sm text-white">
                    {{ $reply->user->name ?? 'Unknown User' }}
                </h3>
                @if($reply->parent && $reply->parent->user)
                    <span class="text-cyan-400 text-xs font-medium">
                        reply to &#64;{{ $reply->parent->user->name }}
                    </span>
                @endif
                <span class="text-xs text-gray-500">
                    {{ $reply->created_at->diffForHumans() }}
                </span>
            </div>

            <p class="mt-1 text-gray-300 text-[15px] leading-relaxed">
                {{ $reply->content }}
            </p>

            <div class="flex gap-6 mt-2 text-xs text-gray-500">
                <button class="hover:text-cyan-300 transition">Like</button>
                @auth
                <button
                    data-reply-toggle="{{ $reply->id }}"
                    class="hover:text-cyan-300 transition">
                    Reply
                </button>
                @endauth
                <button class="hover:text-red-400 transition">Report</button>
            </div>
        </div>

    </div>

    {{-- FORM REPLY --}}
    @auth
    <div id="reply-form-{{ $reply->id }}" class="hidden mt-3" style="margin-left: {{ $indent + 32 }}px">
        <form action="{{ route('movie.comment') }}" method="POST" class="flex gap-2">
            @csrf
            <input type="hidden" name="movie_id"  value="{{ $movie->tmdb_movie_id }}">
            <input type="hidden" name="reply_id"  value="{{ $reply->id }}">
            <div class="flex-1 flex gap-2">
                <textarea
                    name="content"
                    rows="1"
                    placeholder="Reply to {{ $reply->user->name ?? '' }}…"
                    class="flex-1 resize-none bg-transparent border-b border-white/10
                           focus:border-cyan-400 outline-none text-gray-200
                           placeholder-gray-500 pb-2 text-sm transition"></textarea>
                <button type="submit"
                        class="self-end px-4 py-2 rounded-lg bg-cyan-500 hover:bg-cyan-400
                               text-black text-sm font-semibold transition">
                    Reply
                </button>
            </div>
        </form>
    </div>
    @endauth

    {{-- CHILDREN (rekursif) --}}
    @if($reply->replies->isNotEmpty())
    <div class="mt-3 space-y-3 border-l border-white/5 pl-2" style="margin-left: {{ $indent + 16 }}px">
        @foreach($reply->replies as $child)
            <x-movie.comment-reply
                :reply="$child"
                :movie="$movie"
                :depth="$depth + 1" />
        @endforeach
    </div>
    @endif

</div>