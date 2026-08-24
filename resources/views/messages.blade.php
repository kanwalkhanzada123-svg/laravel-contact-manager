<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LeadDesk - Admin Inbox</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-50 min-h-screen text-slate-800 antialiased p-6 md:p-10">

    <div class="max-w-6xl mx-auto space-y-6">

        <!-- Top Navigation / Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-200/80">
            <div>
                <div class="flex items-center gap-2">
                    <span class="bg-indigo-600 text-white p-2 rounded-lg text-sm font-bold shadow-sm">LD</span>
                    <h1 class="text-2xl font-bold text-slate-900">LeadDesk Inbox</h1>
                </div>
                <p class="text-xs text-slate-500 mt-1">
                    Logged in: <span class="font-medium text-slate-700">{{ Auth::user()->name }}</span> ({{ Auth::user()->email }})
                </p>
            </div>
            
            <div class="flex items-center gap-3">
                <a href="/contact" target="_blank" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition flex items-center gap-2">
                    <i class="fa-solid fa-arrow-up-right-from-square"></i> Open Live Form
                </a>
                <form action="/logout" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 text-xs font-semibold rounded-xl transition flex items-center gap-1.5">
                        <i class="fa-solid fa-right-from-bracket"></i> Logout
                    </button>
                </form>
            </div>
        </div>

        <!-- Metric Stat Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200/80 flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Leads</p>
                    <p class="text-2xl font-bold text-slate-800 mt-1">{{ $totalCount }}</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-inbox"></i>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200/80 flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Unread Messages</p>
                    <p class="text-2xl font-bold text-amber-600 mt-1">{{ $unreadCount }}</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-envelope-open-text"></i>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200/80 flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Resolved / Read</p>
                    <p class="text-2xl font-bold text-emerald-600 mt-1">{{ $readCount }}</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl text-xs font-semibold flex items-center justify-between shadow-sm">
                <span><i class="fa-solid fa-check mr-2"></i> {{ session('success') }}</span>
            </div>
        @endif

        <!-- Filter & Search Controls -->
        <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-200/80">
            <form action="/messages" method="GET" class="flex flex-col md:flex-row items-center gap-3">
                
                <!-- Search Box -->
                <div class="relative flex-1 w-full">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3 text-slate-400 text-xs"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, email, or message keyword..." class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
                </div>

                <!-- Status Filter Dropdown -->
                <div class="w-full md:w-44">
                    <select name="status" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition cursor-pointer">
                        <option value="">All Statuses</option>
                        <option value="unread" {{ request('status') === 'unread' ? 'selected' : '' }}>Unread Only</option>
                        <option value="read" {{ request('status') === 'read' ? 'selected' : '' }}>Read Only</option>
                    </select>
                </div>

                <!-- Submit / Reset Buttons -->
                <div class="flex items-center gap-2 w-full md:w-auto">
                    <button type="submit" class="w-full md:w-auto px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl transition shadow-sm">
                        Filter
                    </button>
                    @if(request('search') || request('status'))
                        <a href="/messages" class="w-full md:w-auto px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold rounded-xl transition text-center">
                            Clear
                        </a>
                    @endif
                </div>

            </form>
        </div>

        <!-- Table View -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200/80 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                            <th class="p-4">Status</th>
                            <th class="p-4">Sender Profile</th>
                            <th class="p-4">Client Message</th>
                            <th class="p-4">Received On</th>
                            <th class="p-4 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        @forelse ($messages as $msg)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="p-4 whitespace-nowrap">
                                    <form action="/messages/{{ $msg->id }}/status" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" title="Click to toggle status" class="px-2.5 py-1 text-[11px] font-semibold rounded-full transition {{ $msg->status === 'unread' ? 'bg-amber-100 text-amber-800 hover:bg-amber-200' : 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200' }}">
                                            ● {{ ucfirst($msg->status) }}
                                        </button>
                                    </form>
                                </td>
                                <td class="p-4 whitespace-nowrap">
                                    <div class="font-bold text-slate-800">{{ $msg->name }}</div>
                                    <div class="text-slate-400 text-[11px]">{{ $msg->email ?? 'No email provided' }}</div>
                                </td>
                                <td class="p-4 text-slate-600 max-w-sm leading-relaxed">
                                    {{ $msg->message }}
                                </td>
                                <td class="p-4 whitespace-nowrap text-slate-400 text-[11px]">
                                    {{ $msg->created_at ? $msg->created_at->format('d M Y, h:i A') : 'N/A' }}
                                </td>
                                <td class="p-4 whitespace-nowrap text-center space-x-2">
                                    <!-- Email Reply Action -->
                                    @if($msg->email)
                                        <a href="mailto:{{ $msg->email }}?subject=Reply to your message&body=Hi {{ $msg->name }}," class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition inline-block" title="Direct Email Reply">
                                            <i class="fa-solid fa-reply"></i>
                                        </a>
                                    @endif

                                    <!-- Delete Form -->
                                    <form action="/messages/{{ $msg->id }}" method="POST" class="inline" onsubmit="return confirm('Delete this record permanently?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-rose-500 hover:bg-rose-50 rounded-lg transition" title="Delete Message">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-12 text-slate-400">
                                    <i class="fa-regular fa-folder-open text-3xl mb-2 block"></i>
                                    No records matched your criteria.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Laravel Pagination Links -->
            @if($messages->hasPages())
                <div class="p-4 bg-slate-50 border-t border-slate-200/80">
                    {{ $messages->links() }}
                </div>
            @endif
        </div>

    </div>

</body>
</html>