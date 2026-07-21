<?php

namespace App\Services;

use App\Models\Category;

class CategoryService
{
    public function getCategoryStats(Category $category): array
    {
        return [
            'total' => $category->complaints()->count(),
            'pending' => $category->complaints()->where('status', 'pending')->count(),
            'in_progress' => $category->complaints()->whereIn('status', ['in_progress', 'under_review'])->count(),
            'resolved' => $category->complaints()->whereIn('status', ['approved', 'resolved'])->count(),
        ];
    }

    public function createCategory(array $validatedData): Category
    {
        return Category::create($validatedData);
    }

    public function updateCategory(Category $category, array $validatedData): bool
    {
        return $category->update($validatedData);
    }

    public function deleteCategory(Category $category): ?bool
    {
        return $category->delete();
    }
}
