<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;
use Illuminate\Support\Facades\Mail;
use App\Mail\LeadReplyMail;

class PageController extends Controller
{
    public function showAbout()
    {
        return view('about');
    }

    public function reply(Request $request, $id)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $contact = Contact::findOrFail($id);

        Mail::to($contact->email)->send(new LeadReplyMail($request->subject, $request->message, $contact->name));

        $contact->update(['status' => 'replied']);

        return back()->with('success', 'Reply email sent successfully!');
    }
}