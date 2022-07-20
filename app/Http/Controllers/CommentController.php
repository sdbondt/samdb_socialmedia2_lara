<?php

namespace App\Http\Controllers;

use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Post $post) {
        $this->authorize('view', [Post::class, $post]);
        $attr = request()->validate([
            'content' => ['required', 'min:1', 'max:10000']
        ]);
        $attr['post_id'] = $post->id;
        $comment = request()->user()->comments()->create($attr);
        return [
            'comment' => new CommentResource($comment)
        ];
    }

    public function show(Comment $comment) {
        $this->authorize('view', [Comment::class, $comment]);
        return [
            'comment' => new CommentResource($comment)
        ];
    }

    public function index(Post $post) {
        $this->authorize('view', [Post::class, $post]);
        $comments = cache()->remember('comments' . request()->user()->id, 60, function() use($post) {
            return Comment::with('author')->where('post_id', $post->id)->paginate();
        });
        return [
            'comments' => CommentResource::collection($comments)->response()->getData()
        ];
    }

    public function update(Comment $comment) {
        $this->authorize('update', [Comment::class, $comment]);
        $attr = request()->validate([
            'content' => ['required', 'min:1', 'max:10000']
        ]);
        $comment->update($attr);
        return [
            'comment' => new CommentResource($comment)
        ];
    }

    public function destroy(Comment $comment) {
        $this->authorize('delete', [Comment::class, $comment]);
        $comment->delete();
        return [
            'msg' => 'Comment got deleted'
        ];
    }
}
