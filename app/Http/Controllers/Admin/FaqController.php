<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FaqController extends Controller
{
    public function index(): View
    {
        return view('admin.faqs.index', ['faqs' => Faq::orderBy('sort_order')->paginate(20)]);
    }

    public function store(Request $request): RedirectResponse
    {
        Faq::create($this->validated($request));

        return back()->with('status', 'FAQ added.');
    }

    public function update(Request $request, Faq $faq): RedirectResponse
    {
        $faq->update($this->validated($request));

        return back()->with('status', 'FAQ updated.');
    }

    public function destroy(Faq $faq): RedirectResponse
    {
        $faq->delete();

        return back()->with('status', 'FAQ deleted.');
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'question' => ['required', 'string', 'max:500'],
            'answer' => ['required', 'string'],
            'category' => ['nullable', 'string', 'max:100'],
            'sort_order' => ['nullable', 'integer'],
            'status' => ['boolean'],
        ]);
    }
}
