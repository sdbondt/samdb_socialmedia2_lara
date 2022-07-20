<?php

namespace App\Http\Controllers;

use App\Models\User;

class FollowController extends Controller
{
    public function sendRequest(User $user) {
        $requestExists = request()->user()->sentRequests?->contains($user->id) || false;
        $receivedRequestExists = request()->user()->receivedRequests?->contains($user->id) || false;
        $friendExists = request()->user()->friends?->contains($user->id) || false;
        if($requestExists) {
            return [
                'msg' => 'You already sent this user a request.'
            ];
        } elseif($receivedRequestExists) {
            return [
                'msg' => 'This user already sent you a request'
            ];
        } elseif(request()->user()->id == $user->id) {
            return [
                'msg' => 'Can\'t send a friend request to yourself'
            ];
        } elseif($friendExists) {
            return [
                'msg' => 'Can\'t send a request to someone who already is your friend'
            ];
        } else {
            request()->user()->sentRequests()->attach($user->id);
            return [
                'sent requests' => request()->user()->load('sentRequests')->sentRequests
            ];
        }    
    }

    public function cancelRequest(User $user) {
        $requestExists = request()->user()->sentRequests->contains($user->id);
        if($requestExists) {
            request()->user()->sentRequests()->detach($user->id);
            return [
                'msg' => 'Request got removed.',
                'sent requests' => request()->user()->load('sentRequests')->sentRequests
            ];
        } else {
            return [
                'msg' => 'You haven\'t sent this user a request'
            ];
        }    
    }

    public function acceptRequest(User $user) {
        $requestExists = request()->user()->receivedRequests->contains($user->id);
        if(!$requestExists) {
            return [
                'msg' => 'No request to accept.',
            ];
        } else {
            request()->user()->receivedRequests()->detach($user->id);
            request()->user()->friends()->attach($user->id);
            $user->friends()->attach(request()->user()->id);
            return [
                'msg' => 'New friend!',
                'friends' => request()->user()->load('friends')->friends
            ];
        }  
    }

    public function declineRequest(User $user) {
        $requestExists = request()->user()->receivedRequests->contains($user->id);
        if(!$requestExists) {
            return [
                'msg' => 'No request to decline',
            ];
        } else {
            request()->user()->receivedRequests()->detach($user->id);
            return [
                'received requests' => request()->user()->load('receivedRequests')->receivedRequests
            ];
        }
    }

    public function unfriend(User $user) {
        $friendExists = request()->user()->friends?->contains($user->id) || false;
        if($friendExists) {
            $user->friends()->detach(request()->user()->id);
            request()->user()->friends()->detach($user->id);
            return [
                'friends' => request()->user()->load('friends')->friends
            ];
        } else {
            return [
                'msg' => 'Can\'t unfriend someone who isn\'t your friend'
            ];
        }
    }
}
