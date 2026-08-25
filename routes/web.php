<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\PageController;
use App\Models\Contact;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::post('/contact', [PageController::class, 'storeContact'])->name('contact.store');
Route::post('/contact-submit', [PageController::class, 'storeContact'])->name('contact.submit');

// Login Routes
Route::get('/login', function () {
    return view('login');
})->name('login');

Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        return redirect()->intended('/messages');
    }

    return back()->withErrors([
        'email' => 'Invalid credentials.',
    ]);
});

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login');
})->name('logout');

// CRM / Dashboard Routes
Route::get('/messages', [PageController::class, 'messages'])->name('messages.index');
Route::get('/messages/dashboard', [PageController::class, 'messages'])->name('messages');

Route::get('/messages/export/csv', [PageController::class, 'exportCsv'])->name('messages.export.csv');

// Star / Unstar Route
Route::post('/messages/{id}/toggle-star', function ($id) {
    $contact = Contact::findOrFail($id);
    if (\Illuminate\Support\Facades\Schema::hasColumn('contacts', 'is_starred')) {
        $contact->is_starred = !$contact->is_starred;
        $contact->save();
    }
    return back()->with('success', 'Status updated!');
})->name('messages.toggleStar');

// Read / Unread Route
Route::post('/messages/{id}/toggle-read', function ($id) {
    $contact = Contact::findOrFail($id);
    $contact->status = ($contact->status === 'replied') ? 'pending' : 'replied';
    $contact->save();
    return back();
})->name('messages.toggleRead');

// Bulk Delete Route
Route::post('/messages/bulk-delete', function (Request $request) {
    $ids = $request->input('ids', []);
    if (!empty($ids)) {
        Contact::whereIn('id', $ids)->delete();
    }
    return back()->with('success', 'Selected messages deleted successfully!');
})->name('messages.bulkDelete');

// Single Delete Route
Route::delete('/messages/{id}', function ($id) {
    Contact::findOrFail($id)->delete();
    return back()->with('success', 'Message delete ho gaya!');
})->name('messages.destroy');

Route::post('/messages/{id}/reply', [PageController::class, 'reply'])->name('messages.reply');
Route::post('/crm/leads/reply', [PageController::class, 'replyLead'])->name('leads.reply');

// Update Status via Drag & Drop
Route::post('/messages/{id}/update-status', function (Request $request, $id) {
    $request->validate([
        'status' => 'required|string|in:pending,replied,won,lost',
    ]);

    $contact = Contact::findOrFail($id);
    $contact->status = $request->status;
    $contact->save();

    return response()->json(['success' => true, 'message' => 'Status updated successfully!']);
})->name('messages.updateStatus');