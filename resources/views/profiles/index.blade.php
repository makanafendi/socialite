@extends('layouts.app')

@section('content')
<div class="container max-w-[768px] mx-auto">
    <div class="bg-white flex gap-10 p-4 rounded-md shadow-md">

        <div class="flex justify-center items-center max-w-[200px] max-h-[200px] rounded-full overflow-hidden">
            <img src="{{ $user->profile->profileImage() }}" alt="logo">
        </div>

        <div class="flex flex-col justify-between p-2">
            <div class="flex flex-col gap-2">
                <div class="flex flex-col">
                    <div class="flex gap-2">
                        <h1 class="text-xl font-bold">{{ $user->username }}</h1>
                        @cannot('update', $user->profile)
                        @if(Auth::user()->following->contains($user->id))
                        <form action="{{ route('unfollow', $user->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="bg-primary text-white px-2 py-1 text-xs rounded-md">Unfollow</button>
                        </form>
                        @else
                        <form action="{{ route('follow', $user->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="bg-primary text-white px-2 py-1 text-xs rounded-md">Follow</button>
                        </form>
                        @endif
                        @endcannot

                    </div>
                    <div class="flex gap-2 font-medium">
                        @can('update', $user->profile)
                        <a href="/p/create" class="text-blue-500 text-xs flex items-center gap-1">
                            <x-carbon-add class="w-4 h-4" />
                            Add a post
                        </a>
                        <span class="text-gray-500 text-sm">|</span>
                        <a href="/profile/{{ $user->id }}/edit" class="text-blue-500 text-xs flex items-center gap-1">
                            <x-carbon-edit class="w-4 h-4" />
                            Edit Profile
                        </a>
                        @endcan
                    </div>
                </div>


                <ul class="flex gap-5 text-sm font-bold">
                    <li class="flex gap-1">{{ $postCount }}
                        <h1 class="text-gray-500 font-medium">Post</h1>
                    </li>
                    <li class="flex gap-1">{{ $followerCount }}
                        <a href="{{ route('following.page', $user->id) }}">
                            <h1 class="text-gray-500 font-medium">Followers</h1>
                        </a>
                    </li>
                    <li class="flex gap-1">{{ $followingCount }}
                        <a href="{{ route('following.page', $user->id) }}">
                            <h1 class="text-gray-500 font-medium">Following</h1>
                        </a>
                    </li>
                </ul>
            </div>
            <div>
                <div class="font-medium">{{ $user->profile->title }}</div>
                <div class="flex flex-col gap-2">
                    <div class="bg-gray-100 p-2 text-xs rounded-md w-fit">{{ $user->profile->description }}</div>
                    <div class="text-blue-500 text-xs"><a href="{{ $user->profile->url }}">{{ $user->profile->url }}</a></div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-3 pt-10 gap-4">
        @foreach($user->posts as $post)
        <div>
            <a href="/p/{{ $post->id }}">
                <img src="/storage/{{ $post->image }}" alt="">
            </a>
        </div>
        @endforeach
    </div>
</div>


</div>
@endsection