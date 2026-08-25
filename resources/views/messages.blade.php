<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PipelinePro CRM - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-[#0f172a] text-gray-200 font-sans min-h-screen p-4 md:p-8">

    <div class="max-w-7xl mx-auto space-y-6">

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="p-3 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 rounded-xl text-xs font-semibold flex items-center gap-2 shadow-sm">
                <span>✅</span> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="p-3 bg-rose-500/10 border border-rose-500/30 text-rose-400 rounded-xl text-xs font-semibold flex items-center gap-2 shadow-sm">
                <span>⚠️</span> {{ session('error') }}
            </div>
        @endif

        <!-- Top Header Navigation -->
        <header class="flex flex-col md:flex-row justify-between items-center gap-4 bg-[#1e293b] p-4 rounded-2xl border border-slate-800 shadow-md">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-indigo-600 flex items-center justify-center font-bold text-white shadow-lg shadow-indigo-500/30">
                    PP
                </div>
                <div>
                    <h1 class="text-xl font-bold text-white tracking-wide">PipelinePro CRM</h1>
                    <p class="text-xs text-slate-400">Smart Sales & Lead Automation</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <div class="flex items-center bg-slate-900 p-1 rounded-xl border border-slate-700">
                    <button onclick="toggleView('table')" id="btn-table" class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-indigo-600 text-white transition">📋 Table</button>
                    <button onclick="toggleView('pipeline')" id="btn-pipeline" class="px-3 py-1.5 text-xs font-semibold rounded-lg text-slate-400 hover:text-white transition">📌 Pipeline</button>
                </div>

                <a href="{{ route('messages.export.csv') }}" class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 transition">
                    📥 Export CSV
                </a>

                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="px-3 py-1.5 text-xs font-semibold rounded-xl bg-rose-600/20 hover:bg-rose-600 text-rose-300 hover:text-white border border-rose-500/30 transition">
                        Logout
                    </button>
                </form>
            </div>
        </header>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4">
            <div class="bg-[#1e293b] p-4 rounded-2xl border border-slate-800 flex justify-between items-center shadow-sm">
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase">Total Leads</p>
                    <h2 class="text-2xl font-black text-white mt-1">{{ $stats['total'] }}</h2>
                </div>
                <div class="text-2xl">📋</div>
            </div>

            <div class="bg-[#1e293b] p-4 rounded-2xl border border-slate-800 flex justify-between items-center shadow-sm">
                <div>
                    <p class="text-xs font-semibold text-amber-400 uppercase">Pending</p>
                    <h2 class="text-2xl font-black text-amber-400 mt-1">{{ $stats['pending'] }}</h2>
                </div>
                <div class="text-2xl">⏳</div>
            </div>

            <div class="bg-[#1e293b] p-4 rounded-2xl border border-slate-800 flex justify-between items-center shadow-sm">
                <div>
                    <p class="text-xs font-semibold text-blue-400 uppercase">Discussion</p>
                    <h2 class="text-2xl font-black text-blue-400 mt-1">{{ $stats['replied'] }}</h2>
                </div>
                <div class="text-2xl">💬</div>
            </div>

            <div class="bg-[#1e293b] p-4 rounded-2xl border border-slate-800 flex justify-between items-center shadow-sm">
                <div>
                    <p class="text-xs font-semibold text-emerald-400 uppercase">Won Deals</p>
                    <h2 class="text-2xl font-black text-emerald-400 mt-1">{{ $stats['won'] }}</h2>
                </div>
                <div class="text-2xl">🏆</div>
            </div>

            <div class="bg-[#1e293b] p-4 rounded-2xl border border-slate-800 flex justify-between items-center shadow-sm col-span-2 sm:col-span-1">
                <div>
                    <p class="text-xs font-semibold text-sky-400 uppercase">Today's</p>
                    <h2 class="text-2xl font-black text-sky-400 mt-1">{{ $stats['today'] }}</h2>
                </div>
                <div class="text-2xl">⚡</div>
            </div>
        </div>

        <!-- Chart Section -->
        <div class="bg-[#1e293b] p-5 rounded-2xl border border-slate-800 shadow-lg">
            <div class="flex justify-between items-center mb-3">
                <div>
                    <h3 class="font-bold text-white text-sm">📈 Lead Volume & Inquiry Trends</h3>
                    <p class="text-[11px] text-slate-400">Incoming inquiries over the last 7 days</p>
                </div>
                <span class="text-[10px] font-semibold text-emerald-400 bg-emerald-500/10 px-2.5 py-0.5 rounded-full border border-emerald-500/20">
                    ● Real-Time Activity
                </span>
            </div>
            <div class="h-44 w-full">
                <canvas id="leadsAnalyticsChart"></canvas>
            </div>
        </div>

        <!-- Priority Filters & Search Toolbar -->
        <div class="flex flex-wrap items-center justify-between gap-3 bg-[#1e293b] p-3 rounded-2xl border border-slate-800">
            <div class="flex items-center gap-2">
                <span class="text-xs text-slate-400 font-semibold ml-2">Priority:</span>
                <button onclick="filterPriority('all')" class="priority-btn active px-3 py-1 text-xs font-bold rounded-lg bg-indigo-600 text-white transition" data-prio="all">All</button>
                <button onclick="filterPriority('High')" class="priority-btn px-3 py-1 text-xs font-bold rounded-lg bg-slate-800 text-rose-400 border border-rose-500/30 hover:bg-rose-500/20 transition" data-prio="High">🔥 High</button>
                <button onclick="filterPriority('Medium')" class="priority-btn px-3 py-1 text-xs font-bold rounded-lg bg-slate-800 text-amber-400 border border-amber-500/30 hover:bg-amber-500/20 transition" data-prio="Medium">⚡ Medium</button>
                <button onclick="filterPriority('Low')" class="priority-btn px-3 py-1 text-xs font-bold rounded-lg bg-slate-800 text-slate-300 border border-slate-700 hover:bg-slate-700 transition" data-prio="Low">☕ Low</button>
            </div>
            <input type="text" id="searchInput" onkeyup="searchTable()" placeholder="Search leads, notes..." 
                   class="w-full sm:w-64 px-3.5 py-1.5 text-xs bg-slate-900 border border-slate-700 rounded-xl text-slate-200 focus:outline-none focus:border-indigo-500">
        </div>

        <!-- TABLE VIEW -->
        <div id="table-view" class="bg-[#1e293b] rounded-2xl border border-slate-800 overflow-hidden shadow-lg">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-300">
                    <thead class="bg-slate-900/60 text-slate-400 uppercase text-[10px] tracking-wider border-b border-slate-800">
                        <tr>
                            <th class="p-3.5">Name</th>
                            <th class="p-3.5">Contact</th>
                            <th class="p-3.5">Priority</th>
                            <th class="p-3.5">Message</th>
                            <th class="p-3.5">Deal Value</th>
                            <th class="p-3.5">Status</th>
                            <th class="p-3.5">Date</th>
                            <th class="p-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="leadsTableBody" class="divide-y divide-slate-800/60">
                        @forelse($contacts as $contact)
                            <tr class="lead-item-row hover:bg-slate-800/60 transition cursor-pointer" data-priority="{{ $contact->priority ?? 'Medium' }}" onclick="openLeadModal({{ json_encode($contact) }})">
                                <td class="p-3.5 font-semibold text-white">{{ $contact->name }}</td>
                                <td class="p-3.5">
                                    <div>{{ $contact->email }}</div>
                                    <div class="text-[11px] text-slate-400">{{ $contact->phone ?? 'N/A' }}</div>
                                </td>
                                <td class="p-3.5">
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase
                                        {{ ($contact->priority ?? 'Medium') == 'High' ? 'bg-rose-500/10 text-rose-400 border border-rose-500/30' : '' }}
                                        {{ ($contact->priority ?? 'Medium') == 'Medium' ? 'bg-amber-500/10 text-amber-400 border border-amber-500/30' : '' }}
                                        {{ ($contact->priority ?? 'Medium') == 'Low' ? 'bg-slate-700 text-slate-300 border border-slate-600' : '' }}">
                                        {{ $contact->priority ?? 'Medium' }}
                                    </span>
                                </td>
                                <td class="p-3.5 max-w-xs truncate text-slate-300">{{ $contact->message }}</td>
                                <td class="p-3.5 text-indigo-400 font-bold">${{ number_format($contact->deal_value ?? 0) }}</td>
                                <td class="p-3.5">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase
                                        {{ $contact->status == 'pending' ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' : '' }}
                                        {{ $contact->status == 'replied' ? 'bg-blue-500/10 text-blue-400 border border-blue-500/20' : '' }}
                                        {{ $contact->status == 'won' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : '' }}
                                        {{ $contact->status == 'lost' ? 'bg-rose-500/10 text-rose-400 border border-rose-500/20' : '' }}">
                                        {{ $contact->status }}
                                    </span>
                                </td>
                                <td class="p-3.5 text-slate-400">{{ $contact->created_at ? $contact->created_at->format('M d, Y') : '-' }}</td>
                                <td class="p-3.5 text-right" onclick="event.stopPropagation()">
                                    <form action="{{ route('messages.destroy', $contact->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete message?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-400 hover:text-rose-300 font-semibold text-xs">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="p-6 text-center text-slate-500">No leads found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($contacts, 'hasPages') && $contacts->hasPages())
                <div class="p-4 border-t border-slate-800">
                    {{ $contacts->links() }}
                </div>
            @endif
        </div>

        <!-- PIPELINE KANBAN VIEW -->
        <div id="pipeline-view" class="hidden grid grid-cols-1 md:grid-cols-4 gap-4">
            
            <!-- Pending -->
            <div class="bg-[#1e293b] p-4 rounded-2xl border border-slate-800">
                <div class="flex justify-between items-center mb-3">
                    <h3 class="font-bold text-amber-400 text-sm">⏳ Pending</h3>
                    <span class="text-xs bg-amber-400/20 text-amber-300 px-2 py-0.5 rounded-full font-bold">{{ $pipeline['pending']->count() }}</span>
                </div>
                <div id="pending" class="kanban-column min-h-[380px] space-y-2.5 p-1 rounded-xl bg-slate-900/40" data-status="pending">
                    @foreach($pipeline['pending'] as $lead)
                        <div class="kanban-card lead-item-row bg-slate-800 p-3.5 rounded-xl border border-slate-700 shadow cursor-grab active:cursor-grabbing hover:border-indigo-500/50 transition" data-id="{{ $lead->id }}" data-priority="{{ $lead->priority ?? 'Medium' }}" onclick="openLeadModal({{ json_encode($lead) }})">
                            <div class="flex justify-between items-start gap-2">
                                <div class="text-xs font-bold text-white">{{ $lead->name }}</div>
                                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded
                                    {{ ($lead->priority ?? 'Medium') == 'High' ? 'bg-rose-500/20 text-rose-400' : '' }}
                                    {{ ($lead->priority ?? 'Medium') == 'Medium' ? 'bg-amber-500/20 text-amber-400' : '' }}
                                    {{ ($lead->priority ?? 'Medium') == 'Low' ? 'bg-slate-700 text-slate-300' : '' }}">
                                    {{ $lead->priority ?? 'Medium' }}
                                </span>
                            </div>
                            <div class="text-[11px] text-slate-400 truncate mt-0.5">{{ $lead->email }}</div>
                            <div class="mt-2.5 flex justify-between items-center text-[11px]">
                                <span class="text-indigo-400 font-bold">${{ number_format($lead->deal_value ?? 0) }}</span>
                                <span class="text-slate-500">{{ $lead->created_at ? $lead->created_at->format('M d') : '' }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Replied -->
            <div class="bg-[#1e293b] p-4 rounded-2xl border border-slate-800">
                <div class="flex justify-between items-center mb-3">
                    <h3 class="font-bold text-blue-400 text-sm">💬 Discussion</h3>
                    <span class="text-xs bg-blue-400/20 text-blue-300 px-2 py-0.5 rounded-full font-bold">{{ $pipeline['replied']->count() }}</span>
                </div>
                <div id="replied" class="kanban-column min-h-[380px] space-y-2.5 p-1 rounded-xl bg-slate-900/40" data-status="replied">
                    @foreach($pipeline['replied'] as $lead)
                        <div class="kanban-card lead-item-row bg-slate-800 p-3.5 rounded-xl border border-slate-700 shadow cursor-grab active:cursor-grabbing hover:border-indigo-500/50 transition" data-id="{{ $lead->id }}" data-priority="{{ $lead->priority ?? 'Medium' }}" onclick="openLeadModal({{ json_encode($lead) }})">
                            <div class="flex justify-between items-start gap-2">
                                <div class="text-xs font-bold text-white">{{ $lead->name }}</div>
                                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded
                                    {{ ($lead->priority ?? 'Medium') == 'High' ? 'bg-rose-500/20 text-rose-400' : '' }}
                                    {{ ($lead->priority ?? 'Medium') == 'Medium' ? 'bg-amber-500/20 text-amber-400' : '' }}
                                    {{ ($lead->priority ?? 'Medium') == 'Low' ? 'bg-slate-700 text-slate-300' : '' }}">
                                    {{ $lead->priority ?? 'Medium' }}
                                </span>
                            </div>
                            <div class="text-[11px] text-slate-400 truncate mt-0.5">{{ $lead->email }}</div>
                            <div class="mt-2.5 flex justify-between items-center text-[11px]">
                                <span class="text-indigo-400 font-bold">${{ number_format($lead->deal_value ?? 0) }}</span>
                                <span class="text-slate-500">{{ $lead->created_at ? $lead->created_at->format('M d') : '' }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Won -->
            <div class="bg-[#1e293b] p-4 rounded-2xl border border-slate-800">
                <div class="flex justify-between items-center mb-3">
                    <h3 class="font-bold text-emerald-400 text-sm">🏆 Won Deals</h3>
                    <span class="text-xs bg-emerald-400/20 text-emerald-300 px-2 py-0.5 rounded-full font-bold">{{ $pipeline['won']->count() }}</span>
                </div>
                <div id="won" class="kanban-column min-h-[380px] space-y-2.5 p-1 rounded-xl bg-slate-900/40" data-status="won">
                    @foreach($pipeline['won'] as $lead)
                        <div class="kanban-card lead-item-row bg-slate-800 p-3.5 rounded-xl border-l-4 border-l-emerald-500 border-slate-700 shadow cursor-grab active:cursor-grabbing hover:border-emerald-400/50 transition" data-id="{{ $lead->id }}" data-priority="{{ $lead->priority ?? 'Medium' }}" onclick="openLeadModal({{ json_encode($lead) }})">
                            <div class="flex justify-between items-start gap-2">
                                <div class="text-xs font-bold text-white">{{ $lead->name }}</div>
                                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded
                                    {{ ($lead->priority ?? 'Medium') == 'High' ? 'bg-rose-500/20 text-rose-400' : '' }}
                                    {{ ($lead->priority ?? 'Medium') == 'Medium' ? 'bg-amber-500/20 text-amber-400' : '' }}
                                    {{ ($lead->priority ?? 'Medium') == 'Low' ? 'bg-slate-700 text-slate-300' : '' }}">
                                    {{ $lead->priority ?? 'Medium' }}
                                </span>
                            </div>
                            <div class="text-[11px] text-slate-400 truncate mt-0.5">{{ $lead->email }}</div>
                            <div class="mt-2.5 flex justify-between items-center text-[11px]">
                                <span class="text-emerald-400 font-bold">${{ number_format($lead->deal_value ?? 0) }}</span>
                                <span class="text-slate-500">{{ $lead->created_at ? $lead->created_at->format('M d') : '' }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Lost -->
            <div class="bg-[#1e293b] p-4 rounded-2xl border border-slate-800">
                <div class="flex justify-between items-center mb-3">
                    <h3 class="font-bold text-rose-400 text-sm">❌ Lost</h3>
                    <span class="text-xs bg-rose-400/20 text-rose-300 px-2 py-0.5 rounded-full font-bold">{{ $pipeline['lost']->count() }}</span>
                </div>
                <div id="lost" class="kanban-column min-h-[380px] space-y-2.5 p-1 rounded-xl bg-slate-900/40" data-status="lost">
                    @foreach($pipeline['lost'] as $lead)
                        <div class="kanban-card lead-item-row bg-slate-800/60 p-3.5 rounded-xl border-l-4 border-l-rose-500 border-slate-700 shadow opacity-75 cursor-grab active:cursor-grabbing hover:border-rose-400/50 transition" data-id="{{ $lead->id }}" data-priority="{{ $lead->priority ?? 'Medium' }}" onclick="openLeadModal({{ json_encode($lead) }})">
                            <div class="flex justify-between items-start gap-2">
                                <div class="text-xs font-bold text-slate-300">{{ $lead->name }}</div>
                                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded
                                    {{ ($lead->priority ?? 'Medium') == 'High' ? 'bg-rose-500/20 text-rose-400' : '' }}
                                    {{ ($lead->priority ?? 'Medium') == 'Medium' ? 'bg-amber-500/20 text-amber-400' : '' }}
                                    {{ ($lead->priority ?? 'Medium') == 'Low' ? 'bg-slate-700 text-slate-300' : '' }}">
                                    {{ $lead->priority ?? 'Medium' }}
                                </span>
                            </div>
                            <div class="text-[11px] text-slate-500 truncate mt-0.5">{{ $lead->email }}</div>
                            <div class="mt-2.5 flex justify-between items-center text-[11px]">
                                <span class="text-slate-400 font-bold">${{ number_format($lead->deal_value ?? 0) }}</span>
                                <span class="text-slate-500">{{ $lead->created_at ? $lead->created_at->format('M d') : '' }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>

    </div>

    <!-- POPUP MODAL: Tabs for Details & Direct Email -->
    <div id="leadModal" class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-[#1e293b] border border-slate-700 w-full max-w-lg rounded-2xl shadow-2xl overflow-hidden">
            
            <!-- Modal Header -->
            <div class="p-5 border-b border-slate-800 flex justify-between items-center bg-slate-900/50">
                <div>
                    <h3 id="modalName" class="text-lg font-bold text-white">Lead Details</h3>
                    <p id="modalEmail" class="text-xs text-slate-400"></p>
                </div>
                <button onclick="closeLeadModal()" class="text-slate-400 hover:text-white text-xl font-bold px-2 py-1">✕</button>
            </div>

            <!-- Modal Nav Tabs -->
            <div class="flex border-b border-slate-800 bg-slate-900/30 px-5 pt-3 gap-4 text-xs font-semibold">
                <button type="button" onclick="switchModalTab('tab-details')" id="nav-tab-details" class="pb-2 border-b-2 border-indigo-500 text-white transition">📝 Notes & Details</button>
                <button type="button" onclick="switchModalTab('tab-email')" id="nav-tab-email" class="pb-2 border-b-2 border-transparent text-slate-400 hover:text-white transition">✉️ Quick Email Reply</button>
            </div>

            <!-- TAB 1: Lead Details Form -->
            <div id="tab-details-content" class="p-5 space-y-4">
                <form id="modalUpdateForm" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-[11px] uppercase tracking-wider font-semibold text-slate-400 mb-1">Customer Inquiry</label>
                        <div id="modalMessage" class="p-3 bg-slate-900 rounded-xl border border-slate-800 text-xs text-slate-200 whitespace-pre-wrap max-h-28 overflow-y-auto"></div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] uppercase tracking-wider font-semibold text-slate-400 mb-1">Deal Value ($)</label>
                            <input type="number" name="deal_value" id="modalDealValue" step="10" placeholder="0"
                                   class="w-full px-3 py-2 text-xs bg-slate-900 border border-slate-700 rounded-xl text-indigo-400 font-bold focus:outline-none focus:border-indigo-500">
                        </div>

                        <div>
                            <label class="block text-[11px] uppercase tracking-wider font-semibold text-slate-400 mb-1">Priority</label>
                            <select name="priority" id="modalPriority" class="w-full px-3 py-2 text-xs bg-slate-900 border border-slate-700 rounded-xl text-slate-200 focus:outline-none focus:border-indigo-500">
                                <option value="Low">Low</option>
                                <option value="Medium">Medium</option>
                                <option value="High">High</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] uppercase tracking-wider font-semibold text-amber-400 mb-1">📝 Internal Staff Notes</label>
                        <textarea name="internal_notes" id="modalNotes" rows="3" placeholder="Add private team notes..."
                                  class="w-full p-3 text-xs bg-slate-900 border border-slate-700 rounded-xl text-slate-200 placeholder-slate-500 focus:outline-none focus:border-amber-500"></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-2 border-t border-slate-800">
                        <button type="button" onclick="closeLeadModal()" class="px-4 py-2 text-xs font-semibold rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 transition">Cancel</button>
                        <button type="submit" class="px-4 py-2 text-xs font-semibold rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white transition shadow-md">Save Changes</button>
                    </div>
                </form>
            </div>

            <!-- TAB 2: Direct Email Reply Form -->
            <div id="tab-email-content" class="hidden p-5 space-y-4">
                <form id="modalEmailForm" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-[11px] uppercase tracking-wider font-semibold text-slate-400 mb-1">Subject</label>
                        <input type="text" name="reply_subject" id="modalEmailSubject" value="Re: Inquiry via PipelinePro CRM" required
                               class="w-full px-3 py-2 text-xs bg-slate-900 border border-slate-700 rounded-xl text-slate-200 focus:outline-none focus:border-indigo-500">
                    </div>

                    <div>
                        <label class="block text-[11px] uppercase tracking-wider font-semibold text-indigo-400 mb-1">Email Body Message</label>
                        <textarea name="reply_message" id="modalEmailMessage" rows="5" required placeholder="Type your response to customer..."
                                  class="w-full p-3 text-xs bg-slate-900 border border-slate-700 rounded-xl text-slate-200 placeholder-slate-500 focus:outline-none focus:border-indigo-500"></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-2 border-t border-slate-800">
                        <button type="button" onclick="closeLeadModal()" class="px-4 py-2 text-xs font-semibold rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 transition">Cancel</button>
                        <button type="submit" class="px-4 py-2 text-xs font-semibold rounded-xl bg-blue-600 hover:bg-blue-700 text-white transition shadow-md flex items-center gap-1.5">
                            🚀 Send Email Now
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <!-- Scripts -->
    <script>
        function toggleView(view) {
            const tableView = document.getElementById('table-view');
            const pipelineView = document.getElementById('pipeline-view');
            const btnTable = document.getElementById('btn-table');
            const btnPipeline = document.getElementById('btn-pipeline');

            if (view === 'pipeline') {
                tableView.classList.add('hidden');
                pipelineView.classList.remove('hidden');
                btnPipeline.className = "px-3 py-1.5 text-xs font-semibold rounded-lg bg-indigo-600 text-white transition";
                btnTable.className = "px-3 py-1.5 text-xs font-semibold rounded-lg text-slate-400 hover:text-white transition";
            } else {
                tableView.classList.remove('hidden');
                pipelineView.classList.add('hidden');
                btnTable.className = "px-3 py-1.5 text-xs font-semibold rounded-lg bg-indigo-600 text-white transition";
                btnPipeline.className = "px-3 py-1.5 text-xs font-semibold rounded-lg text-slate-400 hover:text-white transition";
            }
        }

        function switchModalTab(tab) {
            const detailsContent = document.getElementById('tab-details-content');
            const emailContent = document.getElementById('tab-email-content');
            const navDetails = document.getElementById('nav-tab-details');
            const navEmail = document.getElementById('nav-tab-email');

            if (tab === 'tab-email') {
                detailsContent.classList.add('hidden');
                emailContent.classList.remove('hidden');
                navEmail.className = "pb-2 border-b-2 border-indigo-500 text-white transition";
                navDetails.className = "pb-2 border-b-2 border-transparent text-slate-400 hover:text-white transition";
            } else {
                emailContent.classList.add('hidden');
                detailsContent.classList.remove('hidden');
                navDetails.className = "pb-2 border-b-2 border-indigo-500 text-white transition";
                navEmail.className = "pb-2 border-b-2 border-transparent text-slate-400 hover:text-white transition";
            }
        }

        function filterPriority(prio) {
            document.querySelectorAll('.priority-btn').forEach(btn => {
                if (btn.getAttribute('data-prio') === prio) {
                    btn.className = "priority-btn px-3 py-1 text-xs font-bold rounded-lg bg-indigo-600 text-white transition shadow-sm";
                } else {
                    btn.className = "priority-btn px-3 py-1 text-xs font-bold rounded-lg bg-slate-800 text-slate-400 border border-slate-700 hover:bg-slate-700 transition";
                }
            });

            const rows = document.querySelectorAll('.lead-item-row');
            rows.forEach(item => {
                const itemPrio = item.getAttribute('data-priority');
                if (prio === 'all' || itemPrio === prio) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        }

        function searchTable() {
            let input = document.getElementById('searchInput').value.toLowerCase();
            let rows = document.querySelectorAll('#leadsTableBody tr');
            rows.forEach(row => {
                let text = row.innerText.toLowerCase();
                row.style.display = text.includes(input) ? '' : 'none';
            });
        }

        function openLeadModal(lead) {
            document.getElementById('modalName').innerText = lead.name;
            document.getElementById('modalEmail').innerText = lead.email + (lead.phone ? ' • ' + lead.phone : '');
            document.getElementById('modalMessage').innerText = lead.message;
            document.getElementById('modalDealValue').value = lead.deal_value || 0;
            document.getElementById('modalNotes').value = lead.internal_notes || '';
            document.getElementById('modalPriority').value = lead.priority || 'Medium';

            document.getElementById('modalUpdateForm').action = `/messages/${lead.id}/update-details`;
            document.getElementById('modalEmailForm').action = `/messages/${lead.id}/reply-email`;
            
            switchModalTab('tab-details');
            document.getElementById('leadModal').classList.remove('hidden');
        }

        function closeLeadModal() {
            document.getElementById('leadModal').classList.add('hidden');
        }

        // Chart.js Setup
        document.addEventListener('DOMContentLoaded', () => {
            const ctx = document.getElementById('leadsAnalyticsChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($chartDates) !!},
                    datasets: [{
                        label: 'Inquiries Received',
                        data: {!! json_encode($chartCounts) !!},
                        borderColor: '#6366f1',
                        borderWidth: 2,
                        backgroundColor: 'rgba(99, 102, 241, 0.15)',
                        fill: true,
                        tension: 0.35,
                        pointBackgroundColor: '#818cf8',
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1, color: '#94a3b8' },
                            grid: { color: 'rgba(51, 65, 85, 0.5)' }
                        },
                        x: {
                            ticks: { color: '#94a3b8' },
                            grid: { display: false }
                        }
                    },
                    plugins: {
                        legend: { display: false }
                    }
                }
            });

            // SortableJS Drag & Drop
            document.querySelectorAll('.kanban-column').forEach(column => {
                new Sortable(column, {
                    group: 'leads-pipeline',
                    animation: 150,
                    ghostClass: 'opacity-30',
                    onEnd: function (evt) {
                        const leadId = evt.item.getAttribute('data-id');
                        const targetStatus = evt.to.getAttribute('data-status');

                        fetch(`/messages/${leadId}/update-status`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ status: targetStatus })
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>