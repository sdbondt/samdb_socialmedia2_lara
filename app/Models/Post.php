<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'author_id'
    ];

    public function scopeFilter($query, ?string $filter) {
        if($filter ?? false) {
            $query->where(fn($query) => $query->where('content', 'like', '%' . $filter . '%') 
                ->orWhereHas('author', fn($query) => $query->where('first_name', 'like', '%' . $filter . '%') 
                    ->orWhere('last_name', 'like', '%' . $filter . '%')));}
    }

    public function author() {
        return $this->belongsTo(User::class);
    }

    public function comments() {
        return $this->hasMany(Comment::class);
    }

    public function likes() {
        return $this->morphMany(Like::class, 'likeable');
    }
}
