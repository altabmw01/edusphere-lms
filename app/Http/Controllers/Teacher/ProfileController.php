<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('teacher.profile.edit', [
            'user' => $request->user(),
            'profile' => $request->user()->teacherProfile,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'headline' => ['nullable', 'string', 'max:255'],
            'biography' => ['nullable', 'string', 'max:2000'],
            'experience_years' => ['nullable', 'integer', 'min:0'],
            'skills' => ['nullable', 'array'],
            'social_links.facebook' => ['nullable', 'url'],
            'social_links.linkedin' => ['nullable', 'url'],
            'social_links.twitter' => ['nullable', 'url'],
        ]);

        $request->user()->teacherProfile()->updateOrCreate(['user_id' => $request->user()->id], $data);

        return back()->with('status', 'Profile updated.');
    }
}
