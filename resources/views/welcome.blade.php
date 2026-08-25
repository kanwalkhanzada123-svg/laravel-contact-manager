<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 flex flex-col items-center justify-center min-h-screen text-center p-4">
    
    <div class="max-w-md w-full bg-white p-8 rounded-2xl shadow-lg border border-gray-100">
        <div class="text-4xl mb-3">🚀</div>
        <h1 class="text-2xl font-bold text-gray-800 mb-2">
            Welcome to Portal
        </h1>
        <p class="text-gray-500 text-sm mb-6">
            Apna option select karein:
        </p>

        <div class="flex flex-col gap-3">
            <!-- 1. Contact Us Page -->
            <a href="/contact" class="w-full py-3 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl shadow transition duration-200">
                Contact Us Form →
            </a>

            <!-- 2. Admin Login (Jo Dashboard le jayega) -->
            <a href="/login" class="w-full py-3 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl shadow transition duration-200">
                Admin Login / Dashboard →
            </a>
        </div>
    </div>

</body>
</html>