<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleBookmark;
use App\Models\ArticleCategory;
use App\Models\ArticleComment;
use App\Models\Sticker;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArticleController extends Controller
{
    /**
     * Display a listing of articles.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Get filter parameters
        $category = $request->get('category');
        $tag = $request->get('tag');
        $difficulty = $request->get('difficulty');
        
        // Base query
        $query = Article::with(['category', 'creator', 'tags'])
            ->published()
            ->orderBy('is_featured', 'desc')
            ->orderBy('published_at', 'desc');
        
        // Apply filters
        if ($category) {
            $query->where('category_id', $category);
        }
        
        if ($tag) {
            $query->whereHas('tags', function($q) use ($tag) {
                $q->where('tags.id', $tag);
            });
        }
        
        if ($difficulty) {
            $query->where('difficulty_level', $difficulty);
        }
        
        $articles = $query->get();
        
        // Get user's bookmarked article IDs
        $bookmarkedIds = [];
        if ($user) {
            $bookmarkedIds = $user->articleBookmarks()
                ->pluck('article_id')
                ->toArray();
        }
        
        // Transform articles for view
        $articlesData = $articles->map(function($article) use ($bookmarkedIds) {
            return [
                'id' => $article->id,
                'title' => $article->title,
                'excerpt' => $article->excerpt,
                'category' => $article->category->name,
                'category_icon' => $article->category->icon,
                'author' => $article->creator->name,
                'reading_time' => $article->reading_time_minutes,
                'difficulty' => ucfirst($article->difficulty_level),
                'published_at' => $article->published_at->diffForHumans(),
                'views' => $article->views_count,
                'likes' => $article->likes_count,
                'is_bookmarked' => in_array($article->id, $bookmarkedIds),
                'is_featured' => $article->is_featured,
                'tags' => $article->tags->pluck('name')->take(3)->toArray(),
            ];
        });
        
        // Get categories and tags for filters
        $categories = ArticleCategory::active()->orderBy('order')->get();
        $tags = Tag::orderBy('articles_count', 'desc')->take(15)->get();
        
        return view('articles.index', compact('articlesData', 'categories', 'tags'));
    }

    /**
     * Display the specified article.
     */
    public function show($id)
    {
        $user = Auth::user();
        
        // Fetch article with relationships including comments
        $articleModel = Article::with(['category', 'creator', 'tags', 'comments.user', 'comments.sticker', 'comments.replies.user', 'comments.replies.sticker'])
            ->findOrFail($id);
        
        // Increment views
        $articleModel->increment('views_count');
        
        // Check if bookmarked
        $bookmark = null;
        if ($user) {
            $bookmark = $user->articleBookmarks()
                ->where('article_id', $articleModel->id)
                ->first();
        }
        
        // Check if liked by current user
        $isLiked = false;
        if ($user) {
            $isLiked = $articleModel->isLikedBy($user);
        }
        
        // Get related articles (same category)
        $relatedArticles = Article::with(['category', 'creator'])
            ->published()
            ->where('category_id', $articleModel->category_id)
            ->where('id', '!=', $articleModel->id)
            ->limit(3)
            ->get()
            ->map(function($article) {
                return [
                    'id' => $article->id,
                    'title' => $article->title,
                    'excerpt' => $article->excerpt,
                    'reading_time' => $article->reading_time_minutes,
                    'views' => $article->views_count,
                ];
            });
        
        // Prepare article data for view
        $article = [
            'id' => $articleModel->id,
            'title' => $articleModel->title,
            'excerpt' => $articleModel->excerpt,
            'content' => $articleModel->content,
            'category' => $articleModel->category->name,
            'category_icon' => $articleModel->category->icon,
            'category_color' => $articleModel->category->color,
            'author' => $articleModel->creator->name,
            'published_at' => $articleModel->published_at->format('d M Y'),
            'reading_time' => $articleModel->reading_time_minutes,
            'difficulty' => ucfirst($articleModel->difficulty_level),
            'views' => $articleModel->views_count,
            'likes' => $articleModel->likes_count,
            'comments_count' => $articleModel->comments_count,
            'is_bookmarked' => $bookmark !== null,
            'is_liked' => $isLiked,
            'allow_comments' => $articleModel->allow_comments,
            'tags' => $articleModel->tags->map(function($tag) {
                return [
                    'id' => $tag->id,
                    'name' => $tag->name,
                    'color' => $tag->color,
                ];
            })->toArray(),
        ];

        // Get stickers for comment form
        $stickers = Sticker::active()->ordered()->get()->groupBy('category');
        
        return view('articles.show', compact('article', 'relatedArticles', 'articleModel', 'stickers'));
    }

    /**
     * Toggle bookmark for an article.
     */
    public function toggleBookmark($id)
    {
        $user = Auth::user();
        $article = Article::findOrFail($id);
        
        $bookmark = $user->articleBookmarks()
            ->where('article_id', $article->id)
            ->first();
        
        if ($bookmark) {
            // Remove bookmark
            $bookmark->delete();
            $article->decrement('bookmarks_count');
            $message = 'Bookmark dihapus';
        } else {
            // Add bookmark
            $user->articleBookmarks()->create([
                'article_id' => $article->id,
                'last_read_at' => now(),
            ]);
            $article->increment('bookmarks_count');
            $message = 'Artikel disimpan ke bookmark';
        }
        
        return redirect()->back()->with('success', $message);
    }

    /**
     * Toggle like for an article.
     */
    public function toggleLike(Request $request, $id)
    {
        $user = Auth::user();
        $article = Article::findOrFail($id);
        
        $liked = $article->toggleLike($user);
        
        $message = $liked ? 'Artikel disukai' : 'Like dihapus';
        
        // Return JSON for AJAX requests
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'liked' => $liked,
                'likes_count' => $article->fresh()->likes_count,
                'message' => $message
            ]);
        }
        
        return redirect()->back()->with('success', $message);
    }

    /**
     * Store a new comment on an article.
     */
    public function storeComment(Request $request, $id)
    {
        $user = Auth::user();
        $article = Article::findOrFail($id);
        
        // Validate that article allows comments
        if (!$article->allow_comments) {
            return redirect()->back()->with('error', 'Komentar tidak diizinkan pada artikel ini');
        }
        
        // Validate: at least content or sticker_id must be provided
        $request->validate([
            'content' => 'nullable|string|max:1000',
            'sticker_id' => 'nullable|exists:stickers,id',
            'parent_id' => 'nullable|exists:article_comments,id',
        ], [
            'content.max' => 'Komentar maksimal 1000 karakter',
            'sticker_id.exists' => 'Stiker tidak valid',
            'parent_id.exists' => 'Komentar parent tidak ditemukan',
        ]);
        
        // Ensure at least one is provided (can have both)
        if (!$request->filled('content') && !$request->filled('sticker_id')) {
            return redirect()->back()->with('error', 'Komentar atau stiker harus diisi');
        }
        
        // Create comment
        $comment = ArticleComment::create([
            'article_id' => $article->id,
            'user_id' => $user->id,
            'parent_id' => $request->parent_id,
            'content' => $request->content,
            'sticker_id' => $request->sticker_id,
        ]);
        
        // Increment sticker usage if sticker comment
        if ($comment->sticker_id) {
            $comment->sticker->incrementUsage();
        }
        
        // Update article comments count
        $article->increment('comments_count');
        
        return redirect()->back()->with('success', 'Komentar berhasil ditambahkan');
    }

    /**
     * Update an existing comment.
     */
    public function updateComment(Request $request, $commentId)
    {
        $user = Auth::user();
        $comment = ArticleComment::findOrFail($commentId);
        
        // Check ownership
        if (!$comment->canDelete($user)) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk mengedit komentar ini');
        }
        
        // Only text comments can be edited
        if ($comment->isStickerComment()) {
            return redirect()->back()->with('error', 'Komentar stiker tidak dapat diedit');
        }
        
        $request->validate([
            'content' => 'required|string|max:1000',
        ], [
            'content.required' => 'Komentar tidak boleh kosong',
            'content.max' => 'Komentar maksimal 1000 karakter',
        ]);
        
        $comment->update([
            'content' => $request->content,
            'is_edited' => true,
            'edited_at' => now(),
        ]);
        
        return redirect()->back()->with('success', 'Komentar berhasil diperbarui');
    }

    /**
     * Delete a comment.
     */
    public function deleteComment($commentId)
    {
        $user = Auth::user();
        $comment = ArticleComment::findOrFail($commentId);
        
        // Check ownership
        if (!$comment->canDelete($user)) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk menghapus komentar ini');
        }
        
        $article = $comment->article;
        
        // Delete comment (replies will be cascade deleted)
        $repliesCount = $comment->replies()->count();
        $comment->delete();
        
        // Update article comments count
        $article->decrement('comments_count', 1 + $repliesCount);
        
        return redirect()->back()->with('success', 'Komentar berhasil dihapus');
    }
}
