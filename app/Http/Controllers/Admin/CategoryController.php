<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index()
    {
        return view('admin.categories.index', ['categories' => Category::withCount('medicines')->latest()->paginate(10)]);
    }

    public function create()
    {
        return view('admin.categories.form', ['category' => new Category]);
    }

    public function store(Request $request, AuditLogService $audit)
    {
        $data = $request->validate(['name' => ['required', 'max:120', 'unique:categories,name'], 'description' => ['nullable']]);
        $data['slug'] = Str::slug($data['name']);
        $category = Category::create($data);
        $audit->record('create_category', $category);

        return redirect()->route('admin.categories.index')->with('success', 'Kategori dibuat.');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.form', compact('category'));
    }

    public function update(Request $request, Category $category, AuditLogService $audit)
    {
        $data = $request->validate(['name' => ['required', 'max:120', Rule::unique('categories', 'name')->ignore($category->id)], 'description' => ['nullable']]);
        $data['slug'] = Str::slug($data['name']);
        $category->update($data);
        $audit->record('update_category', $category);

        return redirect()->route('admin.categories.index')->with('success', 'Kategori diperbarui.');
    }

    public function destroy(Category $category, AuditLogService $audit)
    {
        if ($category->medicines()->exists()) {
            return back()->with('error', 'Kategori masih dipakai obat.');
        }

        $audit->record('delete_category', $category);
        $category->delete();

        return back()->with('success', 'Kategori dihapus.');
    }
}
