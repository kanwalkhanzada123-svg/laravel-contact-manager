<?php

use Illuminate\Support\Facades\Route;
use App\Models\Contact;
use App\Http\Controllers\PageController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/about', [PageController::class, 'showAbout'])->name('about');

Route::get('/contact', function () {
    return view('contact');
})->name('contact');

Route::post('/contact-submit', function (\Illuminate\Http\Request $request) {
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'message' => 'required|string',
    ]);

    Contact::create([
        'name' => $request->name,
        'email' => $request->email,
        'message' => $request->message,
        'status' => 'pending',
    ]);

    return back()->with('success', 'Message successfully sent!');
})->name('contact.submit');

Route::get('/login', function () {
    return view('login');
})->name('login');

Route::post('/login-submit', function (\Illuminate\Http\Request $request) {
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (\Illuminate\Support\Facades\Auth::attempt($credentials)) {
        $request->session()->regenerate();
        return redirect()->intended('/messages');
    }

    return back()->withErrors([
        'email' => 'Invalid credentials provided.',
    ]);
})->name('login.submit');

Route::post('/logout', function (\Illuminate\Http\Request $request) {
    \Illuminate\Support\Facades\Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login');
})->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/messages', function (\Illuminate\Http\Request $request) {
        $query = Contact::query();

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('email', 'like', "%{$searchTerm}%")
                  ->orWhere('message', 'like', "%{$searchTerm}%");
            });
        }

        if ($request->filled('status') && in_array($request->status, ['pending', 'replied'])) {
            $query->where('status', $request->status);
        }

        $stats = [
            'total'   => Contact::count(),
            'pending' => Contact::where('status', 'pending')->orWhereNull('status')->count(),
            'replied' => Contact::where('status', 'replied')->count(),
            'today'   => Contact::whereDate('created_at', \Carbon\Carbon::today())->count(),
        ];

        $contacts = $query->latest()->paginate(10)->withQueryString();
        return view('messages', compact('contacts', 'stats'));
    })->name('messages.index');

    Route::get('/messages/export/csv', function () {
        $contacts = Contact::latest()->get();
        $csvFileName = 'leads_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$csvFileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function () use ($contacts) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Name', 'Email', 'Message', 'Status', 'Created At']);

            foreach ($contacts as $contact) {
                fputcsv($file, [
                    $contact->id,
                    $contact->name,
                    $contact->email,
                    $contact->message,
                    $contact->status ?? 'pending',
                    $contact->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    })->name('messages.export.csv');

    Route::delete('/messages/{id}', function ($id) {
        Contact::findOrFail($id)->delete();
        return back()->with('success', 'Message delete ho gaya!');
    })->name('messages.destroy');

    Route::post('/messages/{id}/reply', [PageController::class, 'reply'])->name('messages.reply');
});