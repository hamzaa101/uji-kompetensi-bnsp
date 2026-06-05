<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Medicine;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        $query = Medicine::active()->with('category')->withSum('batches as stock_sum', 'quantity');
        $query->when($request->search, fn ($q, $search) => $q->where('name', 'like', "%{$search}%"));
        $query->when($request->category_id, fn ($q, $id) => $q->where('category_id', $id));
        $query->when($request->type, fn ($q, $type) => $q->where('type', $type));

        match ($request->sort) {
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            default => $query->orderBy('name'),
        };

        $medicines = $query->paginate(12)->withQueryString();
        $suggestions = collect();

        if ($request->filled('search') && $medicines->total() < 3) {
            $term = strtolower($request->search);
            $suggestions = Medicine::active()->pluck('name')
                ->map(fn ($name) => ['name' => $name, 'distance' => levenshtein($term, strtolower($name))])
                ->sortBy('distance')
                ->take(3)
                ->pluck('name');
        }

        return view('catalog.index', [
            'medicines' => $medicines,
            'categories' => Category::orderBy('name')->get(),
            'types' => ['obat_resep', 'obat_bebas', 'suplemen', 'alat_kesehatan'],
            'suggestions' => $suggestions,
        ]);
    }

    public function show(Medicine $medicine)
    {
        abort_unless($medicine->is_active, 404);

        return view('catalog.show', ['medicine' => $medicine->load('category', 'supplier', 'batches')]);
    }

    public function autocomplete(Request $request)
    {
        $term = $request->query('q', '');

        return Medicine::active()
            ->where('name', 'like', "%{$term}%")
            ->orderBy('name')
            ->limit(8)
            ->get(['name', 'slug'])
            ->map(fn ($medicine) => ['label' => $medicine->name, 'url' => route('catalog.show', $medicine)]);
    }
}
