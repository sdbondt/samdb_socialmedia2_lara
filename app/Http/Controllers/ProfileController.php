<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index() {
        return [
            'user' => request()->user()->load('posts', 'comments', 'likes', 'sentRequests', 'receivedRequests', 'friends')
        ];

    }
}
