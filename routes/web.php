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

// Protected Admin Routes (Requires Login)
Route::middleware('auth')->group(function () {
    Route::get('/messages', function () {
        $messages = Contact::latest()->get();
        return view('messages', compact('messages'));
    });

    Route::patch('/messages/{id}/status', function ($id) {
        $contact = Contact::findOrFail($id);
        $contact->status = ($contact->status === 'unread') ? 'read' : 'unread';
        $contact->save();
        return redirect('/messages')->with('success', 'Status update ho gaya!');
    });

    Route::delete('/messages/{id}', function ($id) {
        Contact::findOrFail($id)->delete();
        return redirect('/messages')->with('success', 'Message delete ho gaya!');
    });
});