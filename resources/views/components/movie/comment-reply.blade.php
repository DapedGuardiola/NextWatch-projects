@props(['reply', 'movie', 'depth' => 0])

<div class="flex gap-3">

    {{-- AVATAR + GARIS VERTIKAL --}}
    <div class="flex flex-col items-center flex-shrink-0">
        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-cyan-400 to-cyan-600
                    text-black font-bold flex items-center justify-center text-xs
                    shadow-[0_0_20px_rgba(34,211,238,0.35)]">
            {{ strtoupper(substr($reply->user->name ?? 'U', 0, 1)) }}
        </div>
        @if($reply->replies->isNotEmpty())
        <div class="w-px flex-1 mt-2 bg-white/10"></div>
        @endif
    </div>

    {{-- KONTEN + CHILDREN --}}
    <div class="flex-1 min-w-0 pb-2">

        {{-- HEADER --}}
        <div class="flex items-center justify-between gap-2">
            <div class="flex items-center gap-2 flex-wrap">
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
                    @if($reply->updated_at->gt($reply->created_at->addSecond()))
                        <span class="italic">(edited)</span>
                    @endif
                </span>
            </div>

            {{-- DROPDOWN MENU (hanya pemilik) --}}
            @auth
            @if(auth()->id() === $reply->user_id)
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open"
                        class="p-1 rounded-lg text-gray-500 hover:text-white hover:bg-white/10 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <circle cx="5" cy="12" r="2"/><circle cx="12" cy="12" r="2"/><circle cx="19" cy="12" r="2"/>
                    </svg>
                </button>
                <div x-show="open"
                     @click.outside="open = false"
                     x-transition
                     class="absolute right-0 mt-1 w-32 rounded-xl bg-[#0d1424] border border-white/10
                            shadow-xl z-50 overflow-hidden">
                    <button
                        @click="open = false; toggleEdit({{ $reply->id }})"
                        class="w-full text-left px-4 py-2 text-sm text-gray-300
                               hover:bg-white/10 hover:text-yellow-300 transition flex items-center gap-2">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5
                                     m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit
                    </button>
                    <button
                        @click="open = false; deleteComment({{ $reply->id }})"
                        class="w-full text-left px-4 py-2 text-sm text-gray-300
                               hover:bg-white/10 hover:text-red-400 transition flex items-center gap-2">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7
                                     m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Delete
                    </button>
                </div>
            </div>
            @endif
            @endauth
        </div>

        {{-- TEKS KOMENTAR --}}
        <div id="comment-text-{{ $reply->id }}">
            <p class="mt-1 text-gray-300 text-[15px] leading-relaxed break-words">
                {{ $reply->content }}
            </p>
        </div>

        {{-- FORM EDIT (hidden) --}}
        @auth
        @if(auth()->id() === $reply->user_id)
        <div id="edit-form-{{ $reply->id }}" class="hidden mt-2">
            <form action="{{ route('movie.comment.update', $reply->id) }}" method="POST">
                @csrf
                @method('PUT')
                <textarea
                    name="content"
                    rows="2"
                    class="w-full resize-none bg-white/5 border border-white/10 rounded-lg
                           focus:border-cyan-400 outline-none text-gray-200
                           p-3 text-sm transition">{{ $reply->content }}</textarea>
                <div class="flex gap-2 mt-2">
                    <button type="submit"
                            class="px-4 py-1.5 rounded-lg bg-cyan-500 hover:bg-cyan-400
                                   text-black text-xs font-semibold transition">
                        Save
                    </button>
                    <button type="button"
                            onclick="toggleEdit({{ $reply->id }})"
                            class="px-4 py-1.5 rounded-lg bg-white/10 hover:bg-white/20
                                   text-gray-300 text-xs transition">
                        Cancel
                    </button>
                </div>
            </form>
        </div>

        {{-- FORM DELETE (hidden, submit via JS) --}}
        <form id="delete-form-{{ $reply->id }}"
              action="{{ route('movie.comment.destroy', $reply->id) }}"
              method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>
        @endif
        @endauth

        {{-- ACTIONS --}}
        <div class="flex gap-6 mt-2 text-xs text-gray-500">
            <button class="hover:text-cyan-300 transition">Like</button>
            @auth
            <button data-reply-toggle="{{ $reply->id }}" class="hover:text-cyan-300 transition">
                Reply
            </button>
            @endauth
            <button class="hover:text-red-400 transition">Report</button>
        </div>

        {{-- FORM REPLY (hidden) --}}
        @auth
        <div id="reply-form-{{ $reply->id }}" class="hidden mt-3">
            <form action="{{ route('movie.comment') }}" method="POST" class="flex gap-2">
                @csrf
                <input type="hidden" name="movie_id" value="{{ $movie->tmdb_movie_id }}">
                <input type="hidden" name="reply_id" value="{{ $reply->id }}">
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
            </form>
        </div>
        @endauth

        {{-- CHILDREN --}}
        @if($reply->replies->isNotEmpty())
        <div class="mt-3 space-y-3">
            @foreach($reply->replies as $child)
                <x-movie.comment-reply
                    :reply="$child"
                    :movie="$movie"
                    :depth="$depth + 1" />
            @endforeach
        </div>
        @endif

    </div>
</div>