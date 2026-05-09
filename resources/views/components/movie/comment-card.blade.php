@props(['comment'])

<div class="bg-[#111827] rounded-xl p-4 border border-white/5">

    <div class="flex gap-4">

        <div class="w-12 h-12 rounded-full bg-cyan-500 flex items-center justify-center font-bold text-black">
            {{ strtoupper(substr($comment->user->name ?? 'U', 0, 1)) }}
        </div>

        <div class="flex-1">

            <div class="flex justify-between items-center">

                <h3 class="font-semibold">
                    {{ $comment->user->name ?? 'Unknown User' }}
                </h3>

                <span class="text-gray-500 text-sm">
                    {{ $comment->created_at ?? 'now' }}
                </span>

            </div>

            <p class="text-gray-300 mt-2 leading-relaxed">
                {{ $comment->body ?? 'No comment text' }}
            </p>

        </div>

    </div>

</div>