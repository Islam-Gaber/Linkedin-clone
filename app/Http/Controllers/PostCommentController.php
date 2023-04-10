<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostComment;
use Illuminate\Http\Request;

class PostCommentController extends Controller
{
    public function store(Request $request, $id)
    {
        $request->validate([
            'content' => 'required',
        ]);

        $post = Post::findOrFail($id);

        $comment = $post->comments()->create([
            'user_id' => $request->user()->id,
            'content' => $request->input('content'),
        ]);

        $comment->load('user');

        return response()->json([
            'comment' => $comment,
        ]);
    }

    public function update(Request $request, $postId, $commentId)
    {
        $request->validate([
            'content' => 'required',
        ]);

        $comment = PostComment::findOrFail($commentId);

        if ($comment->user_id != $request->user()->id) {
            return response(null, 403);
        }

        $comment->update([
            'content' => $request->input('content'),
        ]);

        return response(null, 204);
    }

    public function destroy(Request $request, $postId, $commentId)
    {
        $comment = PostComment::findOrFail($commentId);

        if ($comment->user_id != $request->user()->id) {
            return response(null, 403);
        }

        $comment->delete();

        return response(null, 204);
    }
}
