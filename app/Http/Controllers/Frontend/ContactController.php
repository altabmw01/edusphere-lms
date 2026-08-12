<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Contact\ContactRequest;
use App\Models\ContactMessage;
use App\Notifications\ContactMessageReceivedNotification;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function index(): View
    {
        return view('frontend.contact');
    }

    public function store(ContactRequest $request): RedirectResponse
    {
        $message = ContactMessage::create($request->validated());

        // Notify all admins of the new inbound message.
        User::role(User::ROLE_ADMIN)->active()->get()
            ->each(fn (User $admin) => $admin->notify(new ContactMessageReceivedNotification($message)));

        return back()->with('status', "Thanks for reaching out! We'll get back to you shortly.");
    }
}
