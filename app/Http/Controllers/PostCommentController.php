<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostComment;
use Illuminate\Http\Request;

class PostCommentController extends Controller
{
    use apiResponseTrait;
    public function store(Request $request, $post_uuid)
    {
        $request->validate([
            'content' => 'required',
        ]);

        $post = Post::where('uuid', $post_uuid)->first();

        $comment = $post->comments()->create([
            'user_id' => $request->user()->id,
            'content' => $request->input('content'),
        ]);

        $comment->load('user');

        return $this->apiResponse('comment created', [], ($comment), [], 201);
    }

    public function update(Request $request, $comment_uuid)
    {
        $request->validate([
            'content' => 'required',
        ]);

        $comment = PostComment::where('uuid', $comment_uuid)->first();

        if ($comment->user_id != $request->user()->id) {
            return response(null, 403);
        }

        $comment->update([
            'content' => $request->input('content'),
        ]);

        return $this->apiResponse('Comment updated', [], ($comment), [], 201);
    }

    public function destroy(Request $request, $comment_uuid)
    {
        $comment = PostComment::where('uuid', $comment_uuid)->first();

        if ($comment->user_id != $request->user()->id) {
            return response(null, 403);
        }

        $comment->delete();

        return $this->apiResponse('Comment deleted successfuly.', [], [], [], 201);
    }
}
