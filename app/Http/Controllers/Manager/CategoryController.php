<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        return view('manager.categories.index', [
            'categories' => Category::query()
                ->when($request->filled('type'), fn ($q) => $q->type($request->string('type')))
                ->ordered()
                ->paginate(20)
                ->withQueryString(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:course,book'],
            'icon' => ['nullable', 'string', 'max:60'],
            'color' => ['nullable', 'string', 'max:20'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        Category::create($data);

        return back()->with('status', 'Category created.');
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:60'],
            'color' => ['nullable', 'string', 'max:20'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer'],
            'status' => ['boolean'],
        ]);

        $category->update($data);

        return back()->with('status', 'Category updated.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $category->delete();

        return back()->with('status', 'Category deleted.');
    }
}
