<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ContentManagementController extends Controller
{
    public function index()
    {
        $stats = [
            'total_pages' => $this->getTotalPages(),
            'total_posts' => $this->getTotalPosts(),
            'total_faqs' => $this->getTotalFaqs(),
            'recent_updates' => $this->getRecentUpdates(),
        ];

        return view('admin.content.index', compact('stats'));
    }

    public function pages()
    {
        $pages = $this->getPages();
        return view('admin.content.pages', compact('pages'));
    }

    public function createPage()
    {
        return view('admin.content.create-page');
    }

    public function storePage(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:pages,slug',
            'content' => 'required|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'status' => 'required|in:draft,published',
            'featured_image' => 'nullable|image|max:2048',
        ]);

        $pageData = [
            'title' => $request->title,
            'slug' => $request->slug,
            'content' => $request->content,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'status' => $request->status,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        if ($request->hasFile('featured_image')) {
            $pageData['featured_image'] = $request->file('featured_image')->store('pages', 'public');
        }

        $this->savePage($pageData);

        return redirect()->route('admin.content.pages')
            ->with('success', 'Page created successfully.');
    }

    public function editPage($slug)
    {
        $page = $this->getPage($slug);
        
        if (!$page) {
            abort(404);
        }

        return view('admin.content.edit-page', compact('page'));
    }

    public function updatePage(Request $request, $slug)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'status' => 'required|in:draft,published',
            'featured_image' => 'nullable|image|max:2048',
        ]);

        $page = $this->getPage($slug);
        
        if (!$page) {
            abort(404);
        }

        $pageData = [
            'title' => $request->title,
            'content' => $request->content,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'status' => $request->status,
            'updated_at' => now(),
        ];

        if ($request->hasFile('featured_image')) {
            // Delete old image if exists
            if ($page['featured_image']) {
                Storage::disk('public')->delete($page['featured_image']);
            }
            $pageData['featured_image'] = $request->file('featured_image')->store('pages', 'public');
        }

        $this->updatePageData($slug, $pageData);

        return redirect()->route('admin.content.pages')
            ->with('success', 'Page updated successfully.');
    }

    public function deletePage($slug)
    {
        $page = $this->getPage($slug);
        
        if (!$page) {
            abort(404);
        }

        // Delete featured image if exists
        if ($page['featured_image']) {
            Storage::disk('public')->delete($page['featured_image']);
        }

        $this->deletePageData($slug);

        return redirect()->route('admin.content.pages')
            ->with('success', 'Page deleted successfully.');
    }

    public function posts()
    {
        $posts = $this->getPosts();
        return view('admin.content.posts', compact('posts'));
    }

    public function createPost()
    {
        return view('admin.content.create-post');
    }

    public function storePost(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:posts,slug',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'status' => 'required|in:draft,published',
            'featured_image' => 'nullable|image|max:2048',
            'tags' => 'nullable|string',
        ]);

        $postData = [
            'title' => $request->title,
            'slug' => $request->slug,
            'excerpt' => $request->excerpt,
            'content' => $request->content,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'status' => $request->status,
            'tags' => $request->tags ? explode(',', $request->tags) : [],
            'author' => auth()->user()->name,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        if ($request->hasFile('featured_image')) {
            $postData['featured_image'] = $request->file('featured_image')->store('posts', 'public');
        }

        $this->savePost($postData);

        return redirect()->route('admin.content.posts')
            ->with('success', 'Post created successfully.');
    }

    public function editPost($slug)
    {
        $post = $this->getPost($slug);
        
        if (!$post) {
            abort(404);
        }

        return view('admin.content.edit-post', compact('post'));
    }

    public function updatePost(Request $request, $slug)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'status' => 'required|in:draft,published',
            'featured_image' => 'nullable|image|max:2048',
            'tags' => 'nullable|string',
        ]);

        $post = $this->getPost($slug);
        
        if (!$post) {
            abort(404);
        }

        $postData = [
            'title' => $request->title,
            'excerpt' => $request->excerpt,
            'content' => $request->content,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'status' => $request->status,
            'tags' => $request->tags ? explode(',', $request->tags) : [],
            'updated_at' => now(),
        ];

        if ($request->hasFile('featured_image')) {
            // Delete old image if exists
            if ($post['featured_image']) {
                Storage::disk('public')->delete($post['featured_image']);
            }
            $postData['featured_image'] = $request->file('featured_image')->store('posts', 'public');
        }

        $this->updatePostData($slug, $postData);

        return redirect()->route('admin.content.posts')
            ->with('success', 'Post updated successfully.');
    }

    public function deletePost($slug)
    {
        $post = $this->getPost($slug);
        
        if (!$post) {
            abort(404);
        }

        // Delete featured image if exists
        if ($post['featured_image']) {
            Storage::disk('public')->delete($post['featured_image']);
        }

        $this->deletePostData($slug);

        return redirect()->route('admin.content.posts')
            ->with('success', 'Post deleted successfully.');
    }

    public function faqs()
    {
        $faqs = $this->getFaqs();
        return view('admin.content.faqs', compact('faqs'));
    }

    public function createFaq()
    {
        return view('admin.content.create-faq');
    }

    public function storeFaq(Request $request)
    {
        $request->validate([
            'question' => 'required|string|max:500',
            'answer' => 'required|string',
            'category' => 'required|string|max:100',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $faqData = [
            'id' => Str::uuid(),
            'question' => $request->question,
            'answer' => $request->answer,
            'category' => $request->category,
            'order' => $request->order ?? 0,
            'is_active' => $request->boolean('is_active', true),
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $this->saveFaq($faqData);

        return redirect()->route('admin.content.faqs')
            ->with('success', 'FAQ created successfully.');
    }

    public function editFaq($id)
    {
        $faq = $this->getFaq($id);
        
        if (!$faq) {
            abort(404);
        }

        return view('admin.content.edit-faq', compact('faq'));
    }

    public function updateFaq(Request $request, $id)
    {
        $request->validate([
            'question' => 'required|string|max:500',
            'answer' => 'required|string',
            'category' => 'required|string|max:100',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $faq = $this->getFaq($id);
        
        if (!$faq) {
            abort(404);
        }

        $faqData = [
            'question' => $request->question,
            'answer' => $request->answer,
            'category' => $request->category,
            'order' => $request->order ?? 0,
            'is_active' => $request->boolean('is_active'),
            'updated_at' => now(),
        ];

        $this->updateFaqData($id, $faqData);

        return redirect()->route('admin.content.faqs')
            ->with('success', 'FAQ updated successfully.');
    }

    public function deleteFaq($id)
    {
        $faq = $this->getFaq($id);
        
        if (!$faq) {
            abort(404);
        }

        $this->deleteFaqData($id);

        return redirect()->route('admin.content.faqs')
            ->with('success', 'FAQ deleted successfully.');
    }

    public function announcements()
    {
        $announcements = $this->getAnnouncements();
        return view('admin.content.announcements', compact('announcements'));
    }

    public function createAnnouncement()
    {
        return view('admin.content.create-announcement');
    }

    public function storeAnnouncement(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:info,warning,success,danger',
            'target_audience' => 'required|in:all,subscribers,admins',
            'is_active' => 'boolean',
            'expires_at' => 'nullable|date|after:now',
        ]);

        $announcementData = [
            'id' => Str::uuid(),
            'title' => $request->title,
            'content' => $request->content,
            'type' => $request->type,
            'target_audience' => $request->target_audience,
            'is_active' => $request->boolean('is_active', true),
            'expires_at' => $request->expires_at,
            'created_by' => auth()->user()->name,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $this->saveAnnouncement($announcementData);

        return redirect()->route('admin.content.announcements')
            ->with('success', 'Announcement created successfully.');
    }

    // Helper methods for data storage (implement based on your preference - database, files, etc.)
    
    private function getTotalPages()
    {
        return count($this->getPages());
    }

    private function getTotalPosts()
    {
        return count($this->getPosts());
    }

    private function getTotalFaqs()
    {
        return count($this->getFaqs());
    }

    private function getRecentUpdates()
    {
        return 5; // Mock data
    }

    private function getPages()
    {
        // Mock data - implement with your storage method
        return [
            [
                'title' => 'About Us',
                'slug' => 'about-us',
                'status' => 'published',
                'updated_at' => now()->subDays(2),
            ],
            [
                'title' => 'Terms of Service',
                'slug' => 'terms-of-service',
                'status' => 'published',
                'updated_at' => now()->subDays(5),
            ],
        ];
    }

    private function getPosts()
    {
        // Mock data - implement with your storage method
        return [
            [
                'title' => 'Trading Strategies for Beginners',
                'slug' => 'trading-strategies-beginners',
                'status' => 'published',
                'author' => 'Admin',
                'created_at' => now()->subDays(3),
            ],
        ];
    }

    private function getFaqs()
    {
        // Mock data - implement with your storage method
        return [
            [
                'id' => '1',
                'question' => 'How do I start trading?',
                'category' => 'General',
                'is_active' => true,
                'order' => 1,
            ],
        ];
    }

    private function getAnnouncements()
    {
        // Mock data - implement with your storage method
        return [
            [
                'id' => '1',
                'title' => 'System Maintenance',
                'type' => 'warning',
                'target_audience' => 'all',
                'is_active' => true,
                'created_at' => now()->subHours(2),
            ],
        ];
    }

    // Implement these methods based on your storage preference
    private function savePage($data) { /* Implement */ }
    private function getPage($slug) { /* Implement */ return null; }
    private function updatePageData($slug, $data) { /* Implement */ }
    private function deletePageData($slug) { /* Implement */ }
    
    private function savePost($data) { /* Implement */ }
    private function getPost($slug) { /* Implement */ return null; }
    private function updatePostData($slug, $data) { /* Implement */ }
    private function deletePostData($slug) { /* Implement */ }
    
    private function saveFaq($data) { /* Implement */ }
    private function getFaq($id) { /* Implement */ return null; }
    private function updateFaqData($id, $data) { /* Implement */ }
    private function deleteFaqData($id) { /* Implement */ }
    
    private function saveAnnouncement($data) { /* Implement */ }
}