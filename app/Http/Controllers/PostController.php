<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PostController extends Controller
{
    /**
     * Get a list of all posts.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::with(['user', 'likes', 'comments'])
            ->withCount('likes')
            ->withCount('comments')
            ->latest()
            ->get();
            
        $posts = $posts->filter(function ($post) {
            return Gate::allows('view', $post);
        });

        return response()->json([
            'posts' => $posts,
        ]);
    }

    /**
     * Get a specific post by ID.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $post = Post::with(['user', 'likes', 'comments'])
            ->withCount('likes')
            ->withCount('comments')
            ->findOrFail($id);

        return response()->json([
            'post' => $post,
        ]);
    }

    /**
     * Create a new post.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $post = new Post();
        $post->user_id = auth()->user()->id;
        $post->content = $request->input('content');
        $post->save();

        return response()->json([
            'post' => $post,
        ], 201);
    }

    /**
     * Update an existing post by ID.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $post = Post::findOrFail($id);
        if (Gate::denies('update-post', $post)) {
            return response()->json([
                'message' => 'You are not authorized to update this post.'
            ], 403);
        }

        $post->content = $request->input('content');
        $post->save();

        return response()->json([
            'post' => $post,
        ]);
    }

    /**
     * Delete a post by ID.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        if (Gate::denies('delete-post', $post)) {
            return response()->json([
                'message' => 'You are not authorized to delete this post.'
            ], 403);
        }

        $post->delete();

        return response()->json([], 204);
    }
}
