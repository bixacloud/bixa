<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Coderflex\LaravelTicket\Models\Category;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
   
    public function index()
    {
        try {
            $categories = Category::withCount('tickets')
                                ->orderBy('created_at', 'desc')
                                ->paginate(10);

            return view('admin.tickets.category', compact('categories'));
        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Error loading categories: ' . $e->getMessage());
        }
    }

    
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255', 'unique:categories,name'],
                'description' => ['nullable', 'string', 'max:1000'],
            ]);

            // Thêm slug từ name
            Category::create([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'slug' => Str::slug($validated['name']) // Tạo slug từ name
            ]);

            return redirect()->route('admin.tickets.categories.index')
                           ->with('success', 'Category created successfully');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Error creating category: ' . $e->getMessage())
                           ->withInput();
        }
    }

    public function update(Request $request, Category $category)
    {
        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255', 'unique:categories,name,' . $category->id],
                'description' => ['nullable', 'string', 'max:1000'],
            ]);

            $category->update([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'slug' => Str::slug($validated['name']) // Cập nhật slug khi name thay đổi
            ]);

            return redirect()->route('admin.tickets.categories.index')
                           ->with('success', 'Category updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Error updating category: ' . $e->getMessage())
                           ->withInput();
        }
    }

    
   

    
    public function destroy(Category $category)
    {
        try {
            // Check if category has any tickets
            if ($category->tickets()->exists()) {
                return redirect()->back()
                               ->with('error', 'Cannot delete category with existing tickets');
            }

            $category->delete();

            return redirect()->route('admin.tickets.categories.index')
                           ->with('success', 'Category deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Error deleting category: ' . $e->getMessage());
        }
    }

  
    public function getCategories()
    {
        try {
            $categories = Category::select('id', 'name')
                                ->orderBy('name')
                                ->get();

            return response()->json([
                'success' => true,
                'data' => $categories
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading categories: ' . $e->getMessage()
            ], 500);
        }
    }
}