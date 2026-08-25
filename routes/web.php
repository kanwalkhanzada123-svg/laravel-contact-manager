<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\Contact;

// Redirect home to contact page
Route::get('/', function () {
    return redirect()->route('contact.show');
});

// Public Contact Form
Route::get('/contact', function () {
    return view('contact');
})->name('contact.show');

Route::post('/contact', function (Request $request) {
    $validated = $request->validate([
        'name'    => 'required|string|max:255',
        'email'   => 'required|email|max:255',
        'phone'   => 'nullable|string|max:20',
        'message' => 'required|string',
    ]);

    Contact::create([
        'name'           => $validated['name'],
        'email'          => $validated['email'],
        'phone'          => $validated['phone'] ?? null,
        'message'        => $validated['message'],
        'status'         => 'pending',
        'deal_value'     => 0,
        'internal_notes' => null,
    ]);

    return back()->with('success', 'Your inquiry has been submitted successfully!');
})->name('contact.store');

// Simple Authentication Routes
Route::get('/login', function () {
    if (Auth::check()) {
        return redirect()->route('messages.index');
    }
    return view('login');
})->name('login');

Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email'    => 'required|email',
        'password' => 'required',
    ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        return redirect()->route('messages.index');
    }

    return back()->withErrors([
        'email' => 'The provided credentials do not match our records.',
    ]);
})->name('login.post');

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect()->route('login');
})->name('logout');

// Authenticated CRM Dashboard Routes
Route::middleware('auth')->group(function () {

    // Dashboard Overview
    Route::get('/messages', function () {
        $contacts = Contact::latest()->paginate(15);

        $stats = [
            'total'   => Contact::count(),
            'pending' => Contact::where('status', 'pending')->count(),
            'replied' => Contact::where('status', 'replied')->count(),
            'won'     => Contact::where('status', 'won')->count(),
            'lost'    => Contact::where('status', 'lost')->count(),
            'today'   => Contact::whereDate('created_at', today())->count(),
        ];

        $pipeline = [
            'pending' => Contact::where('status', 'pending')->latest()->get(),
            'replied' => Contact::where('status', 'replied')->latest()->get(),
            'won'     => Contact::where('status', 'won')->latest()->get(),
            'lost'    => Contact::where('status', 'lost')->latest()->get(),
        ];

        // 7-Day Chart Analytics
        $chartDates = [];
        $chartCounts = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $chartDates[] = now()->subDays($i)->format('M d');
            $chartCounts[] = Contact::whereDate('created_at', $date)->count();
        }

        return view('messages', compact('contacts', 'stats', 'pipeline', 'chartDates', 'chartCounts'));
    })->name('messages.index');

    // Send Direct Email Reply
    Route::post('/messages/{id}/reply-email', function (Request $request, $id) {
        $request->validate([
            'reply_subject' => 'required|string|max:255',
            'reply_message' => 'required|string',
        ]);

        $contact = Contact::findOrFail($id);

        try {
            Mail::raw($request->reply_message, function ($message) use ($contact, $request) {
                $message->to($contact->email)
                        ->subject($request->reply_subject);
            });

            // Update status to replied and add to history
            $contact->status = 'replied';
            $logEntry = "\n[" . now()->format('M d, H:i') . "] Sent Reply: " . substr($request->reply_message, 0, 50) . '...';
            $contact->internal_notes = ($contact->internal_notes ?? '') . $logEntry;
            $contact->save();

            return back()->with('success', 'Email reply sent successfully to ' . $contact->email);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send email: ' . $e->getMessage());
        }
    })->name('messages.replyEmail');

    // Update Status via AJAX Drag & Drop OR Direct Form Action
    Route::post('/messages/{id}/update-status', function (Request $request, $id) {
        $request->validate([
            'status' => 'required|in:pending,replied,won,lost',
        ]);

        $contact = Contact::findOrFail($id);
        $contact->status = $request->status;
        $contact->save();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully',
                'status'  => $contact->status
            ]);
        }

        return back()->with('success', 'Lead status updated to ' . ucfirst($request->status) . '!');
    })->name('messages.updateStatus');

    // Update Lead Details via Modal
    Route::post('/messages/{id}/update-details', function (Request $request, $id) {
        $request->validate([
            'internal_notes' => 'nullable|string',
            'deal_value'     => 'nullable|numeric|min:0',
            'priority'       => 'nullable|string|in:Low,Medium,High',
        ]);

        $contact = Contact::findOrFail($id);
        $contact->internal_notes = $request->internal_notes;
        $contact->deal_value = $request->deal_value ?? 0;
        if ($request->filled('priority')) {
            $contact->priority = $request->priority;
        }
        $contact->save();

        return back()->with('success', 'Lead details updated successfully!');
    })->name('messages.updateDetails');

    // Delete Single Inquiry
    Route::delete('/messages/{id}', function ($id) {
        $contact = Contact::findOrFail($id);
        $contact->delete();
        return back()->with('success', 'Lead removed successfully!');
    })->name('messages.destroy');

    // Export CSV
    Route::get('/export-csv', function () {
        $fileName = 'leads_export_' . date('Y_m_d_His') . '.csv';
        $contacts = Contact::all();

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['ID', 'Name', 'Email', 'Phone', 'Message', 'Status', 'Deal Value', 'Notes', 'Created At'];

        $callback = function () use ($contacts, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($contacts as $contact) {
                fputcsv($file, [
                    $contact->id,
                    $contact->name,
                    $contact->email,
                    $contact->phone ?? 'N/A',
                    $contact->message,
                    $contact->status,
                    $contact->deal_value ?? 0,
                    $contact->internal_notes ?? '',
                    $contact->created_at ? $contact->created_at->format('Y-m-d H:i') : ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    })->name('messages.export.csv');
});