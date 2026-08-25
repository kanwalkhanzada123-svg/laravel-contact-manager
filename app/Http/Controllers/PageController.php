<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PageController extends Controller
{
    public function about()
    {
        return view('about');
    }

    public function contact()
    {
        return view('contact');
    }

    public function storeContact(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'message' => 'required|string',
        ]);

        Contact::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'message' => $request->message,
            'status' => 'pending',
            'source' => $request->source ?? 'Website',
            'priority' => $request->priority ?? 'Medium',
            'deal_value' => $request->deal_value ?? 0,
        ]);

        return back()->with('success', 'Your inquiry has been submitted successfully!');
    }

    public function messages()
    {
        $contacts = Contact::latest()->paginate(10);
        $leads = $contacts;
        
        $allLeads = Contact::all();
        $pipeline = [
            'pending' => $allLeads->where('status', 'pending'),
            'replied' => $allLeads->where('status', 'replied'),
            'won'     => $allLeads->where('status', 'won'),
            'lost'    => $allLeads->where('status', 'lost'),
        ];

        $stats = [
            'total' => Contact::count(),
            'unread' => Contact::where('status', 'pending')->count(),
            'starred' => 0,
            'today' => Contact::whereDate('created_at', today())->count(),
            'pending' => Contact::where('status', 'pending')->count(),
            'replied' => Contact::where('status', 'replied')->count(),
            'won' => Contact::where('status', 'won')->count(),
            'lost' => Contact::where('status', 'lost')->count(),
            'total_value' => Contact::sum('deal_value') ?? 0,
        ];

        return view('messages', compact('contacts', 'leads', 'stats', 'pipeline'));
    }

    public function exportCsv(): StreamedResponse
    {
        $fileName = 'leads_export_' . date('Y-m-d_H-i-s') . '.csv';
        $contacts = Contact::all();

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = ['ID', 'Name', 'Email', 'Phone', 'Source', 'Priority', 'Deal Value', 'Status', 'Notes', 'Date'];

        $callback = function() use($contacts, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($contacts as $contact) {
                fputcsv($file, [
                    $contact->id,
                    $contact->name,
                    $contact->email,
                    $contact->phone,
                    $contact->source ?? 'Website',
                    $contact->priority ?? 'Medium',
                    $contact->deal_value ?? 0,
                    $contact->status,
                    $contact->internal_notes ?? '',
                    $contact->created_at ? $contact->created_at->format('Y-m-d H:i') : ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function reply(Request $request, $id)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $contact = Contact::findOrFail($id);
        $contact->update(['status' => 'replied']);

        return back()->with('success', 'Reply sent successfully!');
    }

    public function replyLead(Request $request)
    {
        $request->validate([
            'lead_id' => 'required|exists:contacts,id',
            'message' => 'required|string',
            'internal_notes' => 'nullable|string',
        ]);

        $lead = Contact::findOrFail($request->lead_id);
        
        if ($request->filled('internal_notes')) {
            $lead->internal_notes = $request->internal_notes;
        }
        
        $lead->status = 'replied';
        $lead->save();

        return back()->with('success', 'Reply recorded and notes updated successfully!');
    }
}