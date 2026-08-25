<div data-id="{{ $lead->id }}" class="bg-slate-800 border border-slate-700 hover:border-slate-500 transition-all rounded-xl p-4 mb-3 shadow-md">
    <!-- Badges Row -->
    <div class="flex items-center justify-between gap-2 mb-2">
        <span class="text-xs px-2.5 py-0.5 rounded-full font-semibold
            @if(($lead->priority ?? '') == 'High') bg-rose-500/20 text-rose-400 border border-rose-500/30
            @elseif(($lead->priority ?? '') == 'Low') bg-slate-600/30 text-slate-400
            @else bg-amber-500/20 text-amber-400 border border-amber-500/30 @endif">
            ● {{ $lead->priority ?? 'Medium' }}
        </span>

        <span class="text-xs px-2 py-0.5 rounded bg-blue-500/20 text-blue-400 border border-blue-500/30 font-medium">
            {{ $lead->source ?? 'Website' }}
        </span>
    </div>

    <!-- Contact Info -->
    <h4 class="text-base font-bold text-white mb-0.5">{{ $lead->name }}</h4>
    <p class="text-xs text-slate-400 truncate mb-2">{{ $lead->email }}</p>

    <!-- Deal Value & Date -->
    <div class="flex items-center justify-between text-xs py-2 border-y border-slate-700/60 my-2">
        <span class="text-emerald-400 font-bold">
            ${{ number_format($lead->deal_value ?? 0, 2) }}
        </span>
        <span class="text-slate-400">
            📅 {{ $lead->created_at ? $lead->created_at->format('M d') : 'Recent' }}
        </span>
    </div>

    <!-- Actions -->
    <div class="flex items-center gap-2 pt-1">
        <button type="button" 
                onclick="openReplyModal('{{ $lead->id }}', '{{ addslashes($lead->name) }}', '{{ $lead->email }}', '{{ addslashes($lead->internal_notes ?? '') }}')" 
                class="flex-1 text-xs py-1.5 px-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-lg text-center font-medium transition">
            Reply & Notes
        </button>

        @if(!empty($lead->phone))
        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $lead->phone) }}" target="_blank" 
           class="p-1.5 bg-emerald-600/20 hover:bg-emerald-600 text-emerald-400 hover:text-white rounded-lg border border-emerald-500/30 transition">
            💬
        </a>
        @endif
    </div>
</div>