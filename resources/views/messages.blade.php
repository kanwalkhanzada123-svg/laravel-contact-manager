<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LeadDesk - CRM Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        darkBg: '#0f172a',
                        cardBg: '#1e293b',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-[#0f172a] text-gray-200 font-sans min-h-screen p-4 md:p-8">

    <div class="max-w-7xl mx-auto space-y-6">

        <!-- Top Header Navigation -->
        <header class="flex flex-col md:flex-row justify-between items-center gap-4 bg-[#1e293b] p-4 rounded-2xl border border-slate-800 shadow-md">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-indigo-600 flex items-center justify-center font-bold text-white shadow-lg shadow-indigo-500/30">
                    LD
                </div>
                <div>
                    <h1 class="text-xl font-bold text-white tracking-wide">LeadDesk CRM</h1>
                    <p class="text-xs text-slate-400">Inquiry & Pipeline Management</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <!-- View Switcher -->
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

        <!-- Stats Overview Cards -->
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
                    <p class="text-xs font-semibold text-emerald-400 uppercase">Replied</p>
                    <h2 class="text-2xl font-black text-emerald-400 mt-1">{{ $stats['replied'] }}</h2>
                </div>
                <div class="text-2xl">✅</div>
            </div>

            <div class="bg-[#1e293b] p-4 rounded-2xl border border-slate-800 flex justify-between items-center shadow-sm">
                <div>
                    <p class="text-xs font-semibold text-indigo-400 uppercase">Won Deals</p>
                    <h2 class="text-2xl font-black text-indigo-400 mt-1">{{ $stats['won'] }}</h2>
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

        <!-- 1. TABLE VIEW (Default) -->
        <div id="table-view" class="bg-[#1e293b] rounded-2xl border border-slate-800 overflow-hidden shadow-lg">
            <div class="p-4 border-b border-slate-800 flex flex-col md:flex-row justify-between items-center gap-3">
                <h2 class="font-bold text-white text-base">Inquiries Inbox</h2>
                <input type="text" id="searchInput" onkeyup="searchTable()" placeholder="Search inquiries & notes..." 
                       class="w-full md:w-64 px-3.5 py-1.5 text-xs bg-slate-900 border border-slate-700 rounded-xl text-slate-200 focus:outline-none focus:border-indigo-500">
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-300">
                    <thead class="bg-slate-900/60 text-slate-400 uppercase text-[10px] tracking-wider border-b border-slate-800">
                        <tr>
                            <th class="p-3.5">Name</th>
                            <th class="p-3.5">Contact</th>
                            <th class="p-3.5">Message</th>
                            <th class="p-3.5">Status</th>
                            <th class="p-3.5">Date</th>
                            <th class="p-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="leadsTableBody" class="divide-y divide-slate-800/60">
                        @forelse($contacts as $contact)
                            <tr class="hover:bg-slate-800/40 transition">
                                <td class="p-3.5 font-semibold text-white">{{ $contact->name }}</td>
                                <td class="p-3.5">
                                    <div>{{ $contact->email }}</div>
                                    <div class="text-[11px] text-slate-400">{{ $contact->phone ?? 'N/A' }}</div>
                                </td>
                                <td class="p-3.5 max-w-xs truncate text-slate-300">{{ $contact->message }}</td>
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
                                <td class="p-3.5 text-right">
                                    <form action="{{ route('messages.destroy', $contact->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete message?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-400 hover:text-rose-300 font-semibold text-xs">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-6 text-center text-slate-500">No leads found.</td>
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

        <!-- 2. KANBAN PIPELINE VIEW (Drag & Drop) -->
        <div id="pipeline-view" class="hidden grid grid-cols-1 md:grid-cols-4 gap-4">
            
            <!-- Pending -->
            <div class="bg-[#1e293b] p-4 rounded-2xl border border-slate-800">
                <div class="flex justify-between items-center mb-3">
                    <h3 class="font-bold text-amber-400 text-sm flex items-center gap-1.5">⏳ Pending</h3>
                    <span class="text-xs bg-amber-400/20 text-amber-300 px-2 py-0.5 rounded-full font-bold">{{ $pipeline['pending']->count() }}</span>
                </div>
                <div id="pending" class="kanban-column min-h-[380px] space-y-2.5 p-1 rounded-xl bg-slate-900/40" data-status="pending">
                    @foreach($pipeline['pending'] as $lead)
                        <div class="kanban-card bg-slate-800 p-3.5 rounded-xl border border-slate-700 shadow cursor-grab active:cursor-grabbing hover:border-indigo-500/50 transition" data-id="{{ $lead->id }}">
                            <div class="text-xs font-bold text-white">{{ $lead->name }}</div>
                            <div class="text-[11px] text-slate-400 truncate mt-0.5">{{ $lead->email }}</div>
                            <div class="mt-2.5 flex justify-between items-center text-[11px]">
                                <span class="text-indigo-400 font-bold">${{ number_format($lead->deal_value ?? 0) }}</span>
                                <span class="text-slate-500">{{ $lead->created_at ? $lead->created_at->format('M d') : '' }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Replied / In Discussion -->
            <div class="bg-[#1e293b] p-4 rounded-2xl border border-slate-800">
                <div class="flex justify-between items-center mb-3">
                    <h3 class="font-bold text-blue-400 text-sm flex items-center gap-1.5">💬 Discussion</h3>
                    <span class="text-xs bg-blue-400/20 text-blue-300 px-2 py-0.5 rounded-full font-bold">{{ $pipeline['replied']->count() }}</span>
                </div>
                <div id="replied" class="kanban-column min-h-[380px] space-y-2.5 p-1 rounded-xl bg-slate-900/40" data-status="replied">
                    @foreach($pipeline['replied'] as $lead)
                        <div class="kanban-card bg-slate-800 p-3.5 rounded-xl border border-slate-700 shadow cursor-grab active:cursor-grabbing hover:border-indigo-500/50 transition" data-id="{{ $lead->id }}">
                            <div class="text-xs font-bold text-white">{{ $lead->name }}</div>
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
                    <h3 class="font-bold text-emerald-400 text-sm flex items-center gap-1.5">🏆 Won Deals</h3>
                    <span class="text-xs bg-emerald-400/20 text-emerald-300 px-2 py-0.5 rounded-full font-bold">{{ $pipeline['won']->count() }}</span>
                </div>
                <div id="won" class="kanban-column min-h-[380px] space-y-2.5 p-1 rounded-xl bg-slate-900/40" data-status="won">
                    @foreach($pipeline['won'] as $lead)
                        <div class="kanban-card bg-slate-800 p-3.5 rounded-xl border-l-4 border-l-emerald-500 border-slate-700 shadow cursor-grab active:cursor-grabbing hover:border-emerald-400/50 transition" data-id="{{ $lead->id }}">
                            <div class="text-xs font-bold text-white">{{ $lead->name }}</div>
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
                    <h3 class="font-bold text-rose-400 text-sm flex items-center gap-1.5">❌ Lost</h3>
                    <span class="text-xs bg-rose-400/20 text-rose-300 px-2 py-0.5 rounded-full font-bold">{{ $pipeline['lost']->count() }}</span>
                </div>
                <div id="lost" class="kanban-column min-h-[380px] space-y-2.5 p-1 rounded-xl bg-slate-900/40" data-status="lost">
                    @foreach($pipeline['lost'] as $lead)
                        <div class="kanban-card bg-slate-800/60 p-3.5 rounded-xl border-l-4 border-l-rose-500 border-slate-700 shadow opacity-75 cursor-grab active:cursor-grabbing hover:border-rose-400/50 transition" data-id="{{ $lead->id }}">
                            <div class="text-xs font-bold text-slate-300">{{ $lead->name }}</div>
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

    <!-- Scripts -->
    <script>
        // Switch between Table and Pipeline
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

        // Live Table Search
        function searchTable() {
            let input = document.getElementById('searchInput').value.toLowerCase();
            let rows = document.querySelectorAll('#leadsTableBody tr');
            rows.forEach(row => {
                let text = row.innerText.toLowerCase();
                row.style.display = text.includes(input) ? '' : 'none';
            });
        }

        // Initialize SortableJS for Drag & Drop
        document.addEventListener('DOMContentLoaded', () => {
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
                        }).then(res => res.json()).then(data => {
                            console.log('Status updated successfully:', data);
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>