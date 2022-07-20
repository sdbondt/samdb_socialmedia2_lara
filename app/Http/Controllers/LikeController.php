<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Like;
use App\Models\Post;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function togglePost(Post $post) {
        $this->authorize('view', [Post::class, $post]);
        $likeExists = Like::where('user_id', request()->user()->id)->where('likeable_type', 'App\Models\Post')->where('likeable_id', $post->id)->first();
        if($likeExists) {
            $likeExists->delete();
            return [
                'msg' => 'Like deleted'
            ];
        } else {
            $like = new Like;
            $like->user_id = request()->user()->id;
            $post->likes()->save($like);
            return [
                'msg' => 'like added'
            ];
        }
        
    }

    public function toggleComment(Comment $comment) {
        $this->authorize('view', [Comment::class, $comment]);
        $likeExists = Like::where('user_id', request()->user()->id)->where('likeable_type', 'App\Models\Comment')->where('likeable_id', $comment->id)->first();
        if($likeExists) {
            $likeExists->delete();
            return [
                'msg' => 'Like deleted'
            ];
        } else {
            $like = new Like;
            $like->user_id = request()->user()->id;
            $comment->likes()->save($like);
            return [
                'msg' => 'like added'
            ];
        }
    }
}
