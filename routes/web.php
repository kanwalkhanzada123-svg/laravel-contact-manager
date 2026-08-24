<?php

use Illuminate\Support\Facades\Route;
use App\Models\Contact;
use App\Http\Controllers\PageController;
use App\Mail\AutoResponderMail;

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
        'stage' => 'new',
    ]);

    try {
        \Illuminate\Support\Facades\Mail::to($request->email)->send(new AutoResponderMail($request->name));
    } catch (\Exception $e) {
        // Log or bypass if mailer fails
    }

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

    return back()->withErrors(['email' => 'Invalid credentials provided.']);
})->name('login.submit');

Route::post('/logout', function (\Illuminate\Http\Request $request) {
    \Illuminate\Support\Facades\Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login');
})->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/messages', function (\Illuminate\Http\Request $request) {
        $viewType = $request->get('view', 'table'); // 'table' or 'kanban'
        $query = Contact::query();

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('email', 'like', "%{$searchTerm}%")
                  ->orWhere('message', 'like', "%{$searchTerm}%")
                  ->orWhere('admin_notes', 'like', "%{$searchTerm}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'starred') {
                $query->where('is_starred', true);
            } elseif (in_array($request->status, ['pending', 'replied'])) {
                $query->where('status', $request->status);
            }
        }

        $stats = [
            'total'   => Contact::count(),
            'pending' => Contact::where('status', '!=', 'replied')->orWhereNull('status')->count(),
            'replied' => Contact::where('status', 'replied')->count(),
            'starred' => Contact::where('is_starred', true)->count(),
            'today'   => Contact::whereDate('created_at', \Carbon\Carbon::today())->count(),
        ];

        if ($viewType === 'kanban') {
            $allContacts = $query->orderBy('is_starred', 'desc')->latest()->get();
            $kanbanLeads = [
                'new' => $allContacts->where('stage', 'new'),
                'contacted' => $allContacts->where('stage', 'contacted'),
                'won' => $allContacts->where('stage', 'won'),
                'lost' => $allContacts->where('stage', 'lost'),
            ];
            return view('messages', compact('kanbanLeads', 'stats', 'viewType'));
        }

        $contacts = $query->orderBy('is_starred', 'desc')->latest()->paginate(10)->withQueryString();
        return view('messages', compact('contacts', 'stats', 'viewType'));
    })->name('messages.index');

    Route::post('/messages/{id}/update-stage', function (\Illuminate\Http\Request $request, $id) {
        $contact = Contact::findOrFail($id);
        $stage = $request->stage;
        if (in_array($stage, ['new', 'contacted', 'won', 'lost'])) {
            $contact->stage = $stage;
            if ($stage === 'contacted' || $stage === 'won') {
                $contact->status = 'replied';
            }
            $contact->save();
        }
        return response()->json(['success' => true]);
    })->name('messages.updateStage');

    Route::post('/messages/{id}/toggle-star', function ($id) {
        $contact = Contact::findOrFail($id);
        $contact->is_starred = !$contact->is_starred;
        $contact->save();
        return back();
    })->name('messages.toggleStar');

    Route::post('/messages/{id}/notes', function (\Illuminate\Http\Request $request, $id) {
        $contact = Contact::findOrFail($id);
        $contact->update(['admin_notes' => $request->admin_notes]);
        return back()->with('success', 'Admin note updated successfully!');
    })->name('messages.updateNotes');

    Route::post('/messages/bulk-delete', function (\Illuminate\Http\Request $request) {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:contacts,id',
        ]);

        Contact::whereIn('id', $request->ids)->delete();
        return back()->with('success', count($request->ids) . ' leads delete ho gayin!');
    })->name('messages.bulkDelete');

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
            fputcsv($file, ['ID', 'Name', 'Email', 'Message', 'Stage', 'Admin Notes', 'Status', 'Starred', 'Created At']);

            foreach ($contacts as $contact) {
                fputcsv($file, [
                    $contact->id,
                    $contact->name,
                    $contact->email,
                    $contact->message,
                    $contact->stage ?? 'new',
                    $contact->admin_notes ?? '',
                    $contact->status ?? 'pending',
                    $contact->is_starred ? 'Yes' : 'No',
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