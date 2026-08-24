<?php

use Illuminate\Support\Facades\Route;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return redirect('/contact');
});

// Public Contact Routes
Route::get('/contact', function () {
    return view('contact');
});

Route::post('/contact-submit', function (Request $request) {
    $request->validate([
        'name' => 'required|min:3',
        'email' => 'required|email',
        'message' => 'required|min:5',
    ]);

    Contact::create([
        'name' => $request->name,
        'email' => $request->email,
        'message' => $request->message,
        'status' => 'unread',
    ]);

    return redirect('/contact')->with('success', 'Aapka message kamyabi se bhej diya gaya hai!');
});

// Authentication Routes
Route::get('/login', function () {
    if (Auth::check()) {
        return redirect('/messages');
    }
    return view('login');
})->name('login');

Route::post('/login-submit', function (Request $request) {
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        return redirect()->intended('/messages');
    }

    return back()->withErrors([
        'email' => 'Invalid email or password.',
    ])->onlyInput('email');
});

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login');
});

// Protected Admin Routes
Route::middleware('auth')->group(function () {
    Route::get('/messages', function (Request $request) {
        $query = Contact::query();

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('email', 'like', "%{$searchTerm}%")
                  ->orWhere('message', 'like', "%{$searchTerm}%");
            });
        }

        if ($request->filled('status') && in_array($request->status, ['read', 'unread'])) {
            $query->where('status', $request->status);
        }

        $messages = $query->latest()->paginate(5)->withQueryString();

        $totalCount = Contact::count();
        $unreadCount = Contact::where('status', 'unread')->count();
        $readCount = Contact::where('status', 'read')->count();

        return view('messages', compact('messages', 'totalCount', 'unreadCount', 'readCount'));
    });

    // One-click Direct Status Toggle Route
    Route::post('/messages/{id}/toggle-status', function ($id) {
        $contact = Contact::findOrFail($id);
        $contact->status = ($contact->status === 'unread') ? 'read' : 'unread';
        $contact->save();
        return back()->with('success', 'Lead status successfully updated!');
    });

    // Export Leads as CSV
    Route::get('/messages/export/csv', function () {
        $contacts = Contact::latest()->get();
        $csvFileName = 'leads_export_' . date('Y-m-d_H-i') . '.csv';

        $headers = [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "attachment; filename=\"$csvFileName\"",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function () use ($contacts) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Name', 'Email', 'Message', 'Status', 'Date Submitted']);

            foreach ($contacts as $contact) {
                fputcsv($file, [
                    $contact->id,
                    $contact->name,
                    $contact->email ?? 'N/A',
                    $contact->message,
                    ucfirst($contact->status),
                    $contact->created_at ? $contact->created_at->format('Y-m-d H:i:s') : 'N/A'
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    });

    Route::delete('/messages/{id}', function ($id) {
        Contact::findOrFail($id)->delete();
        return back()->with('success', 'Message delete ho gaya!');
    });
});