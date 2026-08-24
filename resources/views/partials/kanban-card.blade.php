<div data-id="{{ $lead->id }}" class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm hover:shadow-md transition cursor-grab active:cursor-grabbing space-y-3">
    <div class="flex items-start justify-between">
        <div>
            <h4 class="font-bold text-slate-900 dark:text-white text-sm leading-tight">{{ $lead->name }}</h4>
            <a href="mailto:{{ $lead->email }}" class="text-[11px] text-indigo-600 dark:text-indigo-400 hover:underline">{{ $lead->email }}</a>
        </div>
        <span class="text-xs">{{ $lead->is_starred ? '⭐' : '' }}</span>
    </div>

    <p class="text-xs text-slate-600 dark:text-slate-300 line-clamp-3 bg-slate-50 dark:bg-slate-900/50 p-2 rounded-lg border border-slate-100 dark:border-slate-700/50">
        {{ $lead->message }}
    </p>

    @if($lead->admin_notes)
        <div class="text-[11px] text-slate-500 dark:text-slate-400 italic">
            <span class="font-semibold text-slate-700 dark:text-slate-300">Note:</span> {{ $lead->admin_notes }}
        </div>
    @endif

    <div class="flex items-center justify-between pt-2 border-t border-slate-100 dark:border-slate-700/60 text-[11px] text-slate-400">
        <span>{{ $lead->created_at->format('M d') }}</span>
        <button type="button" onclick="openReplyModal('{{ $lead->id }}', '{{ addslashes($lead->name) }}', '{{ addslashes($lead->email) }}')" class="font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
            Reply
        </button>
    </div>
</div>