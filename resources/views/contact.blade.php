<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen p-4">

    <div class="bg-white p-8 rounded-2xl shadow-lg w-full max-w-md border border-gray-200">
        <!-- Header with Admin Login Button -->
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Contact Us</h2>
            
            @auth
                <!-- Agar Login ho to Dashboard ka link -->
                <a href="/messages" class="text-xs bg-indigo-50 text-indigo-600 px-3 py-1.5 rounded-full font-medium hover:bg-indigo-100 transition">
                    Dashboard →
                </a>
            @else
                <!-- Agar Login na ho to Login ka button -->
                <a href="/login" class="text-xs bg-gray-100 text-gray-700 px-3 py-1.5 rounded-full font-medium hover:bg-indigo-50 hover:text-indigo-600 transition">
                    Admin Login 🔐
                </a>
            @endauth
        </div>

        @if (session('success'))
            <div class="mb-4 p-3 bg-green-100 border border-green-300 text-green-700 rounded-lg text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-100 border border-red-300 text-red-700 rounded-lg text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="/contact-submit" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Your Name</label>
                <input type="text" name="name" value="{{ old('name') }}" placeholder="Enter full name" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Your Email</label>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="name@example.com" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Your Message</label>
                <textarea name="message" rows="4" placeholder="Write your message here..." class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none" required>{{ old('message') }}</textarea>
            </div>

            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 rounded-lg shadow-md transition duration-200">Send Message</button>
        </form>
    </div>

</body>
</html>