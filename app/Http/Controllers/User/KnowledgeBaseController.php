<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\KnowledgeArticle;
use App\Models\KnowledgeCategory;
use App\Models\KnowledgeRating;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class KnowledgeBaseController extends Controller
{
    /**
     * Display the knowledge base home page.
     */
    public function index()
    {
        // Get all active categories with their articles count
        $categories = KnowledgeCategory::where('is_active', true)
            ->withCount(['activeArticles'])
            ->orderBy('sort_order')
            ->get();

        // Get featured articles
        $featuredArticles = KnowledgeArticle::where('is_published', true)
            ->where('is_featured', true)
            ->with(['category', 'author'])
            ->latest('published_at')
            ->take(3)
            ->get();

        // Get recent articles
        $recentArticles = KnowledgeArticle::where('is_published', true)
            ->with(['category', 'author'])
            ->latest('published_at')
            ->take(5)
            ->get();

        return view('knowledge.index', compact('categories', 'featuredArticles', 'recentArticles'));
    }

    /**
     * Display articles by category.
     */
    public function category(KnowledgeCategory $category)
    {
        // Ensure category is active
        if (!$category->is_active) {
            abort(404);
        }

        // Get articles in this category
        $articles = KnowledgeArticle::where('category_id', $category->id)
            ->where('is_published', true)
            ->with('author')
            ->latest('published_at')
            ->paginate(10);

        return view('knowledge.category', compact('category', 'articles'));
    }

    /**
     * Display a specific article.
     */
    public function show(KnowledgeCategory $category, KnowledgeArticle $article)
    {
        // Ensure article belongs to the specified category and is published
        if ($article->category_id !== $category->id || !$article->is_published || !$category->is_active) {
            abort(404);
        }

        // Increment view count
        $article->incrementViewCount();
        
        // Process YouTube embeds to make them responsive and compatible
        $article->content = $this->processYouTubeEmbeds($article->content);

        // Load related articles from same category
        $relatedArticles = KnowledgeArticle::where('category_id', $category->id)
            ->where('id', '!=', $article->id)
            ->where('is_published', true)
            ->with('author')
            ->inRandomOrder()
            ->take(3)
            ->get();

        // Get user's rating if logged in
        $userRating = null;
        if (auth()->check()) {
            $userRating = $article->getUserRating(auth()->id());
        }
        
        // Get all categories for sidebar
        $categories = KnowledgeCategory::where('is_active', true)
            ->withCount('activeArticles as articles_count')
            ->orderBy('sort_order')
            ->get();

        return view('knowledge.show', compact('category', 'article', 'relatedArticles', 'userRating', 'categories'));
    }

    /**
     * Process content to ensure YouTube embeds are responsive and mobile-compatible
     * 
     * @param string $content HTML content
     * @return string Processed content
     */
    private function processYouTubeEmbeds($content)
    {
        // First, find YouTube iframes and update them with improved parameters
        $pattern = '/<iframe(.*?)src="https?:\/\/(www\.)?(youtube\.com|youtube-nocookie\.com)\/embed\/([a-zA-Z0-9_-]{11})([^"]*)"(.*?)><\/iframe>/is';
        
        $content = preg_replace_callback($pattern, function($matches) {
            $beforeSrc = $matches[1];
            $domain = $matches[3];
            $videoId = $matches[4];
            $params = $matches[5] ?: '';
            $afterSrc = $matches[6];
            
            // Use youtube-nocookie.com for better privacy and compatibility
            $enhancedParams = '?rel=0&showinfo=0&modestbranding=1&playsinline=1&fs=1';
            
            // Build the new iframe with optimized attributes
            $iframe = '<iframe' . $beforeSrc . 
                     'src="https://www.youtube-nocookie.com/embed/' . $videoId . $enhancedParams . '"' . 
                     $afterSrc . ' loading="lazy" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
            
            // Return the iframe with responsive container if needed
            if (strpos($matches[0], 'responsive-embed-container') === false) {
                return '<div class="responsive-embed-container">' . $iframe . '</div>';
            } else {
                return $iframe;
            }
        }, $content);
        
        // Next, make sure all YouTube iframes are in responsive containers
        $containerPattern = '/<iframe((?!responsive-embed-container).)*src="https?:\/\/(www\.)?(youtube\.com|youtube-nocookie\.com)\/embed\/(.*?)".*?><\/iframe>/is';
        
        $content = preg_replace_callback($containerPattern, function($matches) {
            $iframe = $matches[0];
            return '<div class="responsive-embed-container">' . $iframe . '</div>';
        }, $content);
        
        // Finally, convert any Plyr divs to standard iframes (just in case)
        $plyrPattern = '/<div class="kb-plyr-container"><div data-plyr-provider="youtube" data-plyr-embed-id="([a-zA-Z0-9_-]{11})"><\/div><\/div>/is';
        
        $content = preg_replace_callback($plyrPattern, function($matches) {
            $videoId = $matches[1];
            
            $iframe = '<iframe src="https://www.youtube-nocookie.com/embed/' . $videoId . 
                     '?rel=0&showinfo=0&modestbranding=1&playsinline=1&fs=1" ' .
                     'frameborder="0" loading="lazy" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
            
            return '<div class="responsive-embed-container">' . $iframe . '</div>';
        }, $content);
        
        return $content;
    }

    /**
     * Rate an article.
     */
    public function rate(Request $request, KnowledgeArticle $article)
    {
        // Ensure user is logged in
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'You need to be logged in to rate articles.'
            ], 403);
        }

        // Validate request
        $validated = $request->validate([
            'is_helpful' => ['required', 'boolean'],
        ]);

        // Get user's existing rating if any
        $rating = KnowledgeRating::where('article_id', $article->id)
            ->where('user_id', auth()->id())
            ->first();

        if ($rating) {
            // Update existing rating
            $rating->update([
                'is_helpful' => $validated['is_helpful'],
            ]);
        } else {
            // Create new rating
            KnowledgeRating::create([
                'article_id' => $article->id,
                'user_id' => auth()->id(),
                'is_helpful' => $validated['is_helpful'],
            ]);
        }

        // Return updated counts
        return response()->json([
            'success' => true,
            'message' => 'Thank you for your feedback!',
            'likes_count' => $article->getLikesCountAttribute(),
            'dislikes_count' => $article->getDislikesCountAttribute(),
        ]);
    }

    /**
     * Search for articles.
     */
    public function search(Request $request)
    {
        $query = $request->input('query');

        if (empty($query)) {
            return redirect()->route('knowledge.index');
        }

        $articles = KnowledgeArticle::where('is_published', true)
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('content', 'like', "%{$query}%");
            })
            ->with(['category', 'author'])
            ->latest('published_at')
            ->paginate(10);

        return view('knowledge.search', compact('articles', 'query'));
    }
    
    /**
     * Handle AJAX live search requests.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajaxSearch(Request $request)
    {
        $query = $request->input('query');
        
        if (empty($query) || strlen($query) < 2) {
            return response()->json([
                'success' => false,
                'message' => 'Search query too short',
                'results' => []
            ]);
        }
        
        // Find matching articles
        $articles = KnowledgeArticle::where('is_published', true)
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('content', 'like', "%{$query}%")
                  ->orWhere('excerpt', 'like', "%{$query}%");
            })
            ->with(['category', 'author'])
            ->latest('published_at')
            ->take(5)
            ->get()
            ->map(function ($article) {
                return [
                    'id' => $article->id,
                    'title' => $article->title,
                    'url' => route('knowledge.article', ['category' => $article->category->slug, 'article' => $article->slug]),
                    'category' => [
                        'name' => $article->category->name,
                        'url' => route('knowledge.category', $article->category->slug)
                    ],
                    'excerpt' => Str::limit(strip_tags($article->excerpt ?: $article->content), 100),
                    'author' => $article->author ? $article->author->name : 'Unknown',
                    'published_at' => $article->published_at->format('M d, Y')
                ];
            });
            
        // Find matching categories
        $categories = KnowledgeCategory::where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            })
            ->withCount('activeArticles as article_count')
            ->take(3)
            ->get()
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'url' => route('knowledge.category', $category->slug),
                    'article_count' => $category->article_count,
                    'description' => Str::limit(strip_tags($category->description), 100)
                ];
            });
        
        $totalResults = $articles->count() + $categories->count();
        
        return response()->json([
            'success' => true,
            'results' => [
                'articles' => $articles,
                'categories' => $categories
            ],
            'total' => $totalResults,
            'query' => $query
        ]);
    }
}