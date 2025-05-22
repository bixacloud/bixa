<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KnowledgeArticle;
use App\Models\KnowledgeCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class KnowledgeArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = KnowledgeArticle::with(['category', 'author']);

        // Apply category filter
        if ($request->has('category_id') && $request->category_id !== 'all') {
            $query->where('category_id', $request->category_id);
        }

        // Apply search filter
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // Get all categories for filter dropdown
        $categories = KnowledgeCategory::where('is_active', true)->get();
        
        $articles = $query->latest('published_at')->paginate(10);

        return view('admin.knowledge.articles.index', compact('articles', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = KnowledgeCategory::where('is_active', true)->get();
        return view('admin.knowledge.articles.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:knowledge_categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:knowledge_articles'],
            'content' => ['required', 'string'],
            'excerpt' => ['nullable', 'string'],
        ]);

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        // Set user ID (author)
        $validated['user_id'] = auth()->id();
        
        // Handle checkboxes
        $validated['is_featured'] = $request->has('is_featured');
        $validated['is_published'] = $request->has('is_published');
        
        // Set published_at date if published
        if ($validated['is_published']) {
            $validated['published_at'] = now();
        }

        try {
            KnowledgeArticle::create($validated);
            return redirect()->route('admin.knowledge.articles.index')
                ->with('success', 'Article created successfully');
        } catch (\Exception $e) {
            Log::error('Failed to create knowledge article: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create article: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(KnowledgeArticle $article)
    {
        $categories = KnowledgeCategory::where('is_active', true)->get();
        return view('admin.knowledge.articles.edit', compact('article', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, KnowledgeArticle $article)
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:knowledge_categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('knowledge_articles')->ignore($article)],
            'content' => ['required', 'string'],
            'excerpt' => ['nullable', 'string'],
        ]);

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        // Handle checkboxes - these will be false if not present in the request
        $validated['is_featured'] = $request->has('is_featured');
        $validated['is_published'] = $request->has('is_published');

        // Update published_at if publishing for the first time
        if (!$article->is_published && $validated['is_published']) {
            $validated['published_at'] = now();
        }

        try {
            $article->update($validated);
            return redirect()->route('admin.knowledge.articles.index')
                ->with('success', 'Article updated successfully');
        } catch (\Exception $e) {
            Log::error('Failed to update knowledge article: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update article: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(KnowledgeArticle $article)
    {
        try {
            // Delete related ratings first to avoid foreign key constraint issues
            $article->ratings()->delete();
            
            // Then delete the article
            $article->delete();
            
            return redirect()->route('admin.knowledge.articles.index')
                ->with('success', 'Article deleted successfully');
        } catch (\Exception $e) {
            Log::error('Failed to delete knowledge article: ' . $e->getMessage());
            return redirect()->route('admin.knowledge.articles.index')
                ->with('error', 'Failed to delete article: ' . $e->getMessage());
        }
    }
}