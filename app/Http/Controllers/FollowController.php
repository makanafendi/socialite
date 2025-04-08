<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class FollowController extends Controller
{
    public function follow($id)
    {
        $user = Auth::user();
    
        // Prevent self-follow
        if ($user->id !== $id) {
            $user->following()->attach($id);
        }
    
        return redirect()->back();
    }
    
    public function unfollow($id)
    {
        $user = Auth::user();
        $user->following()->detach($id);
    
        return redirect()->back();
    }

    public function followingPage(User $user)
{
    $following = $user->following;

    $notFollowing = User::whereNotIn('id', $user->following->pluck('id'))
                        ->where('id', '!=', $user->id)
                        ->get();

    return view('profiles.following', compact('user', 'following', 'notFollowing'));
}
}
