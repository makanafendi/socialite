<?php

namespace App\Http\Controllers;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Cache;

use App\Models\User;

use Illuminate\Http\Request;

class ProfilesController extends Controller
{
    public function index(\App\Models\User $user)
    {
        $postCount = Cache::remember('count.posts.' . $user->id, now()->addSeconds(5), function () use ($user) {
            return $user->posts->count();
        });

        $followerCount = Cache::remember('count.followers.' . $user->id, now()->addSeconds(5), function () use ($user) {
            return $user->followers->count();
        });

        $followingCount = Cache::remember('count.following.' . $user->id, now()->addSeconds(5), function () use ($user) {
            return $user->following->count();
        });

        return view('profiles.index', compact('user', 'postCount', 'followerCount', 'followingCount'));
    }

    public function edit(\App\Models\User $user)
    {
        $this->authorize('update', $user->profile);

        return view('profiles.edit', compact('user'));
    }

    public function updatePicture(User $user)
    {
        $this->authorize('update', $user->profile);

        $data = request()->validate([
            'image' => 'required|image',
        ]);

        $imagePath = request('image')->store('profile', 'public');

        $manager = new ImageManager(new Driver());
        $image = $manager->read(public_path("storage/{$imagePath}"));
        $image->scale(width: 1000, height: 1000);
        $image->save();

        $user->profile->update([
            'image' => $imagePath
        ]);

        return redirect()->back()->with('success', 'Profile picture updated successfully');
    }

    public function updateBio(User $user)
    {
        $this->authorize('update', $user->profile);

        $data = request()->validate([
            'description' => 'required|max:100',
        ]);

        $user->profile->update($data);

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Bio updated successfully');
    }
}





