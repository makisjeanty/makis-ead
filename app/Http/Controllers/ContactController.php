<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    /**
     * Display the contact page
     */
    public function index()
    {
        return view('contact.index');
    }

    /**
     * Handle contact form submission
     */
    public function submit(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
        ]);

        try {
            // Log the contact message
            Log::info('Contact form submission', $validated);

            // TODO: Send email notification
            // Mail::to(config('mail.from.address'))->send(new ContactMessage($validated));

            return redirect()->route('contact.index')
                ->with('success', __('messages.contact.success'));
        } catch (\Exception $e) {
            Log::error('Contact form error: ' . $e->getMessage());
            
            return redirect()->route('contact.index')
                ->with('error', __('messages.contact.error'));
        }
    }
}
