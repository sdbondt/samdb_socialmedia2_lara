<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'postId' => $this->id,
            'content' => $this->content,
            'author' => [
                'authorId' => $this->author->id,
                'name' => $this->author->getFullName(),
            ],
            'comments' => new PostCommentCollection($this->comments),
            'likes' => new PostLikeCollection($this->likes)
        ];
    }
}
