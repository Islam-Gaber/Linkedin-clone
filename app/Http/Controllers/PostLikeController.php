<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostLikeController extends Controller
{
    public function like(Request $request, $id)
    {
        $post = Post::findOrFail($id);

        if ($post->likes()->where('user_id', $request->user()->id)->exists()) {
            return response(null, 409);
        }

        $post->likes()->create([
            'user_id' => $request->user()->id,
        ]);

        return response(null, 204);
    }

    public function unlike(Request $request, $id)
    {
        $post = Post::findOrFail($id);

        if (!$post->likes()->where('user_id', $request->user()->id)->exists()) {
            return response(null, 409);
        }

        $post->likes()->where('user_id', $request->user()->id)->delete();

        return response(null, 204);
    }
}
