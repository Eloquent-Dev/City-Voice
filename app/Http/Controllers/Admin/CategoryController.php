<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Division;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Services\CategoryService;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('complaints')
            ->orderBy('id', 'asc')
            ->paginate(15);

        return view('admin.categories.index', compact('categories'));
    }

    public function show(Category $category, CategoryService $service)
    {
        $stats = $service->getCategoryStats($category);

        $complaints = $category->complaints()
            ->with(['user'])
            ->latest() // Cleaner than orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.categories.show', compact('category', 'stats', 'complaints'));
    }

    public function create()
    {
        $divisions = Division::all();

        return view('admin.categories.create', compact('divisions'));
    }

    public function store(StoreCategoryRequest $request, CategoryService $service)
    {
        $service->createCategory($request->validated());

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category created successfully!');
    }

    public function edit(Category $category)
    {
        $divisions = Division::all();

        return view('admin.categories.edit', compact('category', 'divisions'));
    }

    public function update(UpdateCategoryRequest $request, Category $category, CategoryService $service)
    {
        $service->updateCategory($category, $request->validated());

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category updated successfully!');
    }

    public function destroy(Category $category, CategoryService $service)
    {
        if ($category->complaints()->exists()) {
        return back()->with('error', 'Cannot delete this category because there are complaints assigned to it. Please reassign or resolve them first.');
    }
        $service->deleteCategory($category);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category deleted successfully!');
    }
}
