<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
        <div class="text-center mb-6">
            <h2 class="text-3xl font-bold text-gray-800">Admin Login</h2>
            <p class="text-sm text-gray-500 mt-1">Sign in to access the messages inbox</p>
        </div>

        @if($errors->any())
            <div class="mb-4 p-3 bg-red-50 text-red-700 rounded-lg text-sm border border-red-200">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="admin@example.com" required 
                       class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" name="password" placeholder="••••••••" required 
                       class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            </div>

            <button type="submit" class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-colors shadow-md">
                Login to Dashboard
            </button>
        </form>

        <div class="text-center mt-6">
            <a href="/contact" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium transition">
                ← Back to Contact Form
            </a>
        </div>
    </div>
</body>
</html>