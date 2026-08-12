<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $request->validate(['email' => ['required', 'email']]);

        NewsletterSubscriber::updateOrCreate(
            ['email' => $request->string('email')],
            ['is_subscribed' => true, 'unsubscribed_at' => null]
        );

        return back()->with('status', 'Subscribed! Watch your inbox for updates.');
    }

    public function unsubscribe(string $email): RedirectResponse
    {
        NewsletterSubscriber::where('email', $email)->update([
            'is_subscribed' => false,
            'unsubscribed_at' => now(),
        ]);

        return redirect('/')->with('status', 'You have been unsubscribed.');
    }
}
