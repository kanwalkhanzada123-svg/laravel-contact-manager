<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen p-4">

    <div class="bg-white p-8 rounded-2xl shadow-lg w-full max-w-md border border-gray-200">
        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Admin Login</h2>
            <p class="text-sm text-gray-500 mt-1">Sign in to access the messages inbox</p>
        </div>

        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-100 border border-red-300 text-red-700 rounded-lg text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="/login-submit" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="admin@example.com" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" name="password" placeholder="••••••••" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none" required>
            </div>

            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 rounded-lg shadow-md transition duration-200">Login to Dashboard</button>
        </form>

        <div class="mt-4 text-center">
            <a href="/contact" class="text-xs text-gray-500 hover:text-indigo-600">← Back to Contact Form</a>
        </div>
    </div>

</body>
</html>