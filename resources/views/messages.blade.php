<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - All Messages</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen p-8">

    <div class="max-w-5xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Messages Inbox</h1>
                <p class="text-sm text-gray-500">All submitted contact form entries from the database.</p>
            </div>
            <a href="/contact" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-4 py-2 rounded-lg shadow">+ New Message</a>
        </div>

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 border border-green-300 text-green-700 rounded-lg text-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white shadow rounded-xl overflow-hidden border border-gray-200">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-100 text-gray-700 text-xs uppercase font-semibold">
                    <tr>
                        <th class="p-4">Status</th>
                        <th class="p-4">Sender</th>
                        <th class="p-4">Message</th>
                        <th class="p-4">Date</th>
                        <th class="p-4 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-sm">
                    @forelse($messages as $item)
                        <tr class="hover:bg-gray-50 {{ $item->status === 'unread' ? 'bg-indigo-50/40 font-medium' : '' }}">
                            <td class="p-4">
                                <form action="/messages/{{ $item->id }}/status" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-xs px-2.5 py-1 rounded-full font-semibold {{ $item->status === 'unread' ? 'bg-amber-100 text-amber-700 hover:bg-amber-200' : 'bg-green-100 text-green-700 hover:bg-green-200' }}">
                                        {{ ucfirst($item->status ?? 'unread') }}
                                    </button>
                                </form>
                            </td>
                            <td class="p-4">
                                <div class="text-gray-900 font-bold">{{ $item->name }}</div>
                                <div class="text-xs text-gray-500">{{ $item->email ?? 'No email' }}</div>
                            </td>
                            <td class="p-4 text-gray-600">{{ $item->message }}</td>
                            <td class="p-4 text-gray-400 text-xs">{{ $item->created_at->format('d M Y, h:i A') }}</td>
                            <td class="p-4 text-center">
                                <form action="/messages/{{ $item->id }}" method="POST" onsubmit="return confirm('Delete this message?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs bg-red-50 text-red-600 border border-red-200 px-3 py-1 rounded-md hover:bg-red-600 hover:text-white transition">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center p-8 text-gray-400 font-medium">Koi message mojood nahi hai.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>