<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LeadDesk - CRM Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
        }
    </script>
</head>
<body class="bg-slate-50 dark:bg-slate-900 min-h-screen text-slate-800 dark:text-slate-100 transition-colors duration-200">

    <header class="bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 sticky top-0 z-30 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-9 h-9 bg-indigo-600 rounded-xl flex items-center justify-center text-white font-bold shadow-md shadow-indigo-200 dark:shadow-none">
                    LD
                </div>
                <div>
                    <h1 class="text-base font-bold text-slate-900 dark:text-white leading-none">LeadDesk CRM</h1>
                    <span class="text-xs text-slate-400 font-medium">Inquiry Management</span>
                </div>
            </div>
            
            <div class="flex items-center space-x-3">
                <button onclick="toggleDarkMode()" class="p-2 text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg transition" title="Toggle Dark Mode">
                    <span id="themeIcon">🌙</span>
                </button>
                <a href="{{ route('messages.export.csv') }}" class="inline-flex items-center px-3.5 py-2 border border-slate-300 dark:border-slate-600 text-xs font-semibold rounded-lg text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-700 hover:bg-slate-50 dark:hover:bg-slate-600 transition shadow-sm">
                    <svg class="w-4 h-4 mr-1.5 text-slate-500 dark:text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Export CSV
                </a>
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-3.5 py-2 bg-slate-100 dark:bg-slate-700 hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-900/40 text-xs font-semibold text-slate-600 dark:text-slate-300 rounded-lg transition">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

        @if(session('success'))
            <div class="p-4 bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 rounded-xl flex items-center shadow-sm">
                <svg class="w-5 h-5 mr-3 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="text-sm font-medium">{{ session('success') }}</span>
            </div>
        @endif

        <!-- Stat Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Leads</p>
                    <h3 class="text-2xl font-bold text-slate-900 dark:text-white mt-1">{{ $stats['total'] }}</h3>
                </div>
                <div class="w-10 h-10 bg-indigo-50 dark:bg-indigo-950/60 rounded-lg flex items-center justify-center text-indigo-600 dark:text-indigo-400 text-lg font-bold">📋</div>
            </div>

            <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Pending</p>
                    <h3 class="text-2xl font-bold text-amber-600 dark:text-amber-400 mt-1">{{ $stats['pending'] }}</h3>
                </div>
                <div class="w-10 h-10 bg-amber-50 dark:bg-amber-950/60 rounded-lg flex items-center justify-center text-amber-600 dark:text-amber-400 text-lg font-bold">⏳</div>
            </div>

            <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Replied</p>
                    <h3 class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">{{ $stats['replied'] }}</h3>
                </div>
                <div class="w-10 h-10 bg-emerald-50 dark:bg-emerald-950/60 rounded-lg flex items-center justify-center text-emerald-600 dark:text-emerald-400 text-lg font-bold">✅</div>
            </div>

            <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Starred</p>
                    <h3 class="text-2xl font-bold text-yellow-500 mt-1">{{ $stats['starred'] }}</h3>
                </div>
                <div class="w-10 h-10 bg-yellow-50 dark:bg-yellow-950/60 rounded-lg flex items-center justify-center text-yellow-500 text-lg font-bold">⭐</div>
            </div>

            <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Today</p>
                    <h3 class="text-2xl font-bold text-blue-600 dark:text-blue-400 mt-1">{{ $stats['today'] }}</h3>
                </div>
                <div class="w-10 h-10 bg-blue-50 dark:bg-blue-950/60 rounded-lg flex items-center justify-center text-blue-600 dark:text-blue-400 text-lg font-bold">⚡</div>
            </div>
        </div>

        <!-- Filter & Search Controls -->
        <div class="flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center space-x-2 bg-slate-200/70 dark:bg-slate-800 p-1 rounded-xl">
                <a href="{{ route('messages.index') }}" 
                   class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition {{ !request('status') ? 'bg-white dark:bg-slate-700 text-slate-900 dark:text-white shadow-sm' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900' }}">
                    All ({{ $stats['total'] }})
                </a>
                <a href="{{ route('messages.index', ['status' => 'pending']) }}" 
                   class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition {{ request('status') === 'pending' ? 'bg-white dark:bg-slate-700 text-slate-900 dark:text-white shadow-sm' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900' }}">
                    Pending ({{ $stats['pending'] }})
                </a>
                <a href="{{ route('messages.index', ['status' => 'replied']) }}" 
                   class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition {{ request('status') === 'replied' ? 'bg-white dark:bg-slate-700 text-slate-900 dark:text-white shadow-sm' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900' }}">
                    Replied ({{ $stats['replied'] }})
                </a>
                <a href="{{ route('messages.index', ['status' => 'starred']) }}" 
                   class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition {{ request('status') === 'starred' ? 'bg-white dark:bg-slate-700 text-slate-900 dark:text-white shadow-sm' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900' }}">
                    ⭐ Starred ({{ $stats['starred'] }})
                </a>
            </div>

            <div class="flex items-center space-x-3 w-full md:w-auto">
                <button type="button" id="bulkDeleteBtn" onclick="submitBulkDelete()" class="hidden px-3 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                    Delete Selected (<span id="selectedCount">0</span>)
                </button>

                <form action="{{ route('messages.index') }}" method="GET" class="w-full md:w-80 flex items-center">
                    @if(request('status'))
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif
                    <div class="relative w-full">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search inquiries & notes..." 
                            class="w-full pl-9 pr-3 py-2 border border-slate-300 dark:border-slate-700 rounded-lg text-xs bg-white dark:bg-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none shadow-sm">
                        <svg class="h-4 w-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    @if(request('search'))
                        <a href="{{ route('messages.index', request()->only('status')) }}" class="ml-2 text-xs text-slate-500 hover:underline">Clear</a>
                    @endif
                </form>
            </div>
        </div>

        <!-- Table with Notes & Star -->
        <form id="bulkForm" action="{{ route('messages.bulkDelete') }}" method="POST">
            @csrf
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-800/80 border-b border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                            <th class="py-3.5 px-4 w-10 text-center">
                                <input type="checkbox" id="selectAll" onchange="toggleSelectAll(this)" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                            </th>
                            <th class="py-3.5 px-2 w-8 text-center"></th>
                            <th class="py-3.5 px-4">Contact</th>
                            <th class="py-3.5 px-4">Status</th>
                            <th class="py-3.5 px-4">Message & Admin Notes</th>
                            <th class="py-3.5 px-4">Date</th>
                            <th class="py-3.5 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60">
                        @forelse($contacts as $contact)
                            <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-700/40 transition">
                                <td class="py-4 px-4 text-center align-top">
                                    <input type="checkbox" name="ids[]" value="{{ $contact->id }}" onchange="updateSelectedCount()" class="row-checkbox rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                </td>
                                <td class="py-4 px-2 text-center align-top">
                                    <button type="button" onclick="event.preventDefault(); document.getElementById('star-form-{{ $contact->id }}').submit();" class="text-base transition hover:scale-125">
                                        {{ $contact->is_starred ? '⭐' : '☆' }}
                                    </button>
                                </td>
                                <td class="py-4 px-4 align-top">
                                    <div class="font-semibold text-slate-900 dark:text-white">{{ $contact->name }}</div>
                                    <a href="mailto:{{ $contact->email }}" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">{{ $contact->email }}</a>
                                </td>
                                <td class="py-4 px-4 align-top">
                                    @if($contact->status === 'replied')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                                            ● Replied
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300">
                                            ● Pending
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-4 align-top text-slate-600 dark:text-slate-300 max-w-sm">
                                    <p class="line-clamp-2 text-xs text-slate-700 dark:text-slate-200">{{ $contact->message }}</p>
                                    
                                    <div class="mt-2 bg-slate-100 dark:bg-slate-900/60 p-2 rounded-lg border border-slate-200 dark:border-slate-700/60 flex items-start justify-between gap-2">
                                        <p class="text-xs text-slate-500 dark:text-slate-400 italic">
                                            <span class="font-semibold not-italic text-slate-700 dark:text-slate-300">Note:</span> {{ $contact->admin_notes ?: 'No internal notes added.' }}
                                        </p>
                                        <button type="button" onclick="openNoteModal('{{ $contact->id }}', '{{ addslashes($contact->admin_notes) }}')" class="text-[11px] font-semibold text-indigo-600 dark:text-indigo-400 hover:underline shrink-0">
                                            Edit
                                        </button>
                                    </div>
                                </td>
                                <td class="py-4 px-4 align-top text-xs text-slate-400 whitespace-nowrap">
                                    {{ $contact->created_at->format('M d, Y h:i A') }}
                                </td>
                                <td class="py-4 px-4 align-top text-right whitespace-nowrap space-x-2">
                                    <button type="button" onclick="openReplyModal('{{ $contact->id }}', '{{ addslashes($contact->name) }}', '{{ addslashes($contact->email) }}')" 
                                        class="inline-flex items-center px-2.5 py-1.5 bg-indigo-50 dark:bg-indigo-950/60 hover:bg-indigo-100 dark:hover:bg-indigo-900 text-indigo-700 dark:text-indigo-300 text-xs font-semibold rounded-md transition">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                                        Reply
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-slate-400 text-sm">
                                    Koi inquiries nahi milein.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                @if($contacts->hasPages())
                    <div class="p-4 border-t border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50">
                        {{ $contacts->links() }}
                    </div>
                @endif
            </div>
        </form>

        @foreach($contacts as $contact)
            <form id="star-form-{{ $contact->id }}" action="{{ route('messages.toggleStar', $contact->id) }}" method="POST" class="hidden">
                @csrf
            </form>
        @endforeach
    </main>

    <!-- Reply Modal -->
    <div id="replyModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-800 w-full max-w-lg rounded-2xl shadow-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-900 dark:text-white text-base">Send Reply</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">To: <span id="modalCustomerName" class="font-medium text-slate-700 dark:text-slate-200"></span> (<span id="modalCustomerEmail" class="text-indigo-600 dark:text-indigo-400"></span>)</p>
                </div>
                <button onclick="closeReplyModal()" class="text-slate-400 hover:text-slate-600 text-xl leading-none">&times;</button>
            </div>

            <form id="replyForm" method="POST" action="" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Subject</label>
                    <input type="text" name="subject" id="modalSubject" required 
                        class="w-full px-3 py-2 border border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-white rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Your Response</label>
                    <textarea name="message" rows="5" required placeholder="Write reply..." 
                        class="w-full px-3 py-2 border border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-white rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 outline-none"></textarea>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-2">
                    <button type="button" onclick="closeReplyModal()" class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg transition">
                        Cancel
                    </button>
                    <button type="submit" class="px-5 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-sm transition">
                        Send Email Reply
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Note Modal -->
    <div id="noteModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-800 w-full max-w-md rounded-2xl shadow-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between">
                <h3 class="font-bold text-slate-900 dark:text-white text-base">Internal Admin Note</h3>
                <button onclick="closeNoteModal()" class="text-slate-400 hover:text-slate-600 text-xl leading-none">&times;</button>
            </div>

            <form id="noteForm" method="POST" action="" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Private Note / Status Remarks</label>
                    <textarea name="admin_notes" id="noteInput" rows="4" placeholder="e.g. Discussed pricing on call, follow up next week..." 
                        class="w-full px-3 py-2 border border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-white rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 outline-none"></textarea>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-2">
                    <button type="button" onclick="closeNoteModal()" class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg transition">
                        Cancel
                    </button>
                    <button type="submit" class="px-5 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-sm transition">
                        Save Note
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openReplyModal(leadId, name, email) {
            document.getElementById('modalCustomerName').textContent = name;
            document.getElementById('modalCustomerEmail').textContent = email;
            document.getElementById('modalSubject').value = 'Re: Regarding your inquiry on LeadDesk';
            document.getElementById('replyForm').action = '/messages/' + leadId + '/reply';
            document.getElementById('replyModal').classList.remove('hidden');
        }

        function closeReplyModal() {
            document.getElementById('replyModal').classList.add('hidden');
        }

        function openNoteModal(leadId, noteText) {
            document.getElementById('noteInput').value = noteText;
            document.getElementById('noteForm').action = '/messages/' + leadId + '/notes';
            document.getElementById('noteModal').classList.remove('hidden');
        }

        function closeNoteModal() {
            document.getElementById('noteModal').classList.add('hidden');
        }

        function toggleSelectAll(master) {
            const checkboxes = document.querySelectorAll('.row-checkbox');
            checkboxes.forEach(cb => cb.checked = master.checked);
            updateSelectedCount();
        }

        function updateSelectedCount() {
            const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
            const count = checkedBoxes.length;
            const btn = document.getElementById('bulkDeleteBtn');
            const countSpan = document.getElementById('selectedCount');
            
            countSpan.textContent = count;
            if (count > 0) {
                btn.classList.remove('hidden');
            } else {
                btn.classList.add('hidden');
            }
        }

        function submitBulkDelete() {
            if (confirm('Selected leads ko delete karna chahte hain?')) {
                document.getElementById('bulkForm').submit();
            }
        }

        function toggleDarkMode() {
            const html = document.documentElement;
            const icon = document.getElementById('themeIcon');
            if (html.classList.contains('dark')) {
                html.classList.remove('dark');
                localStorage.theme = 'light';
                icon.textContent = '🌙';
            } else {
                html.classList.add('dark');
                localStorage.theme = 'dark';
                icon.textContent = '☀️';
            }
        }

        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
            document.getElementById('themeIcon').textContent = '☀️';
        } else {
            document.documentElement.classList.remove('dark');
            document.getElementById('themeIcon').textContent = '🌙';
        }
    </script>
</body>
</html>