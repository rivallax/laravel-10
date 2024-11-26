<?php

namespace App\Http\Controllers;

// Import Model "Post"
use App\Models\Post;

use Illuminate\Http\Request;

// Return type View
use Illuminate\View\View;

// Return type RedirectResponse
use Illuminate\Http\RedirectResponse;

// Import Facade "Storage"
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index(): View
    {
        // Get all posts with pagination
        $posts = Post::latest()->paginate(5);

        // Render view with posts
        return view('posts.index', compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create(): View
    {
        return view('posts.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        // Validate form
        $request->validate([
            'image'   => 'required|image|mimes:jpeg,jpg,png|max:2048',
            'title'   => 'required|min:5',
            'content' => 'required|min:10',
        ]);

        // Upload image
        $image = $request->file('image');
        $image->storeAs('public/posts', $image->hashName());

        // Create post
        Post::create([
            'image'   => $image->hashName(),
            'title'   => $request->title,
            'content' => $request->content,
        ]);

        // Redirect to index with success message
        return redirect()->route('posts.index')->with('success', 'Data Berhasil Disimpan!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return View
     */
    public function show(int $id): View
    {
        // Get post by ID
        $post = Post::findOrFail($id);

        // Render view with post
        return view('posts.show', compact('post'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return View
     */
    public function edit(int $id): View
    {
        // Get post by ID
        $post = Post::findOrFail($id);

        // Render view with post
        return view('posts.edit', compact('post'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request $request
     * @param  int $id
     * @return RedirectResponse
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        // Validate form
        $request->validate([
            'image'   => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
            'title'   => 'required|min:5',
            'content' => 'required|min:10',
        ]);

        // Get post by ID
        $post = Post::findOrFail($id);

        // Check if image is uploaded
        if ($request->hasFile('image')) {
            // Upload new image
            $image = $request->file('image');
            $image->storeAs('public/posts', $image->hashName());

            // Delete old image
            Storage::delete('public/posts/' . $post->image);

            // Update post with new image
            $post->update([
                'image'   => $image->hashName(),
                'title'   => $request->title,
                'content' => $request->content,
            ]);
        } else {
            // Update post without image
            $post->update([
                'title'   => $request->title,
                'content' => $request->content,
            ]);
        }

        // Redirect to index with success message
        return redirect()->route('posts.index')->with('success', 'Data Berhasil Diubah!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return RedirectResponse
     */
    public function destroy(int $id): RedirectResponse
    {
        // Get post by ID
        $post = Post::findOrFail($id);

        // Delete image
        Storage::delete('public/posts/' . $post->image);

        // Delete post
        $post->delete();

        // Redirect to index with success message
        return redirect()->route('posts.index')->with('success', 'Data Berhasil Dihapus!');
    }
}
