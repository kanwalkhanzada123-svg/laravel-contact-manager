<?php

use Illuminate\Support\Facades\Route;
use App\Models\Contact;
use Illuminate\Http\Request;

// 1. Home Page
Route::get('/', function () {
    return view('welcome');
});

// 2. Contact Form Page (Dikhana)
Route::get('/contact', function () {
    return view('contact');
});

// 3. Contact Form Submit (Validation + Flash Message ke sath)
Route::post('/contact-submit', function (Request $request) {
    $request->validate([
        'name' => 'required|min:3',
        'message' => 'required|min:5',
    ]);

    Contact::create([
        'name' => $request->name,
        'message' => $request->message,
    ]);

    return redirect('/contact')->with('success', 'Aapka message kamyabi se bhej diya gaya hai!');
});

// 4. Admin Dashboard (Saare messages table mein dekhna)
Route::get('/messages', function () {
    $messages = Contact::latest()->get();
    return view('messages', compact('messages'));
});

// 5. Message Delete Karna
Route::delete('/messages/{id}', function ($id) {
    Contact::findOrFail($id)->delete();
    return redirect('/messages')->with('success', 'Message delete ho gaya hai!');
});