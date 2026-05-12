@props(['comment'])

<div class="flex gap-4 group">

    <!-- AVATAR -->
    <div class="flex-shrink-0">

        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-cyan-400 to-cyan-600
                    text-black font-bold flex items-center justify-center
                    shadow-[0_0_20px_rgba(34,211,238,0.35)]">

            {{ strtoupper(substr($comment->user->name ?? 'U', 0, 1)) }}

        </div>

    </div>

    <!-- CONTENT -->
    <div class="flex-1">

        <div class="flex items-center gap-3">

            <h3 class="font-semibold text-sm text-white">
                {{ $comment->user->name ?? 'Unknown User' }}
            </h3>

            <span class="text-xs text-gray-500">
                {{ $comment->created_at ?? 'now' }}
            </span>

        </div>

        <p class="mt-1 text-gray-300 text-[15px] leading-relaxed">
            {{ $comment->body ?? 'No comment text' }}
        </p>

        <div class="flex gap-6 mt-2 text-xs text-gray-500">

            <button class="hover:text-cyan-300 transition">
                Like
            </button>

            <button class="hover:text-cyan-300 transition">
                Reply
            </button>

            <button class="hover:text-red-400 transition">
                Report
            </button>

        </div>

    </div>

</div>