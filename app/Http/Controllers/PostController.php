<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostCollection;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function store() {
        $attr = request()->validate([
            'content' => ['required', 'min:1', 'max:10000']
        ]);
        $post = request()->user()->posts()->create($attr);
        return [
            'post' => new PostResource($post->load('author', 'likes', 'comments'))
        ];
    }

    public function show(Post $post) {
        $this->authorize('view', [Post::class, $post]);
        return [
            'post' => new PostResource($post->load('author', 'likes', 'comments'))
        ];
    }

    public function index() {
        $ids = request()->user()->friends()->pluck('friend_id');
        $ids = [...$ids, request()->user()->id];
        $posts = Post::with('comments.author', 'author', 'likes.user')->filter(request('q'))->whereIn('author_id', $ids)->paginate();
        return [
            'posts' => PostResource::collection($posts)->response()->getData()
            
        ];
    }

    public function update(Post $post) {
        $this->authorize('update', [Post::class, $post]);
        $attr = request()->validate([
            'content' => ['required', 'min:1', 'max:10000']
        ]);
        $post->update($attr);
        return [
            'post' => new PostResource($post->load('author', 'likes', 'comments'))
        ];
    }

    public function destroy(Post $post) {
        $this->authorize('delete', [Post::class, $post]);
        $post->delete();
        return [
            'msg' => 'Post got deleted'
        ];
    }
}
