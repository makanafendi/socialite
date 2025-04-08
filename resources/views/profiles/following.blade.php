@extends('layouts.app')

@section('content')
<div class="container w-[500px] mx-auto">
    <h1 class="text-2xl font-bold mb-4">Following</h1>


    <div class="bg-white p-4 shadow-sm rounded-md mb-6">
        <h2 class="text-lg font-semibold mb-3">People You Follow</h2>
        <ul>
            @forelse($following as $user)
            <li class="flex items-center justify-between p-2">
                <div class="flex items-center gap-3">
                    <img src="{{ $user->profile->profileImage() }}" alt="profile image" class="w-[35px] h-[35px] rounded-full">
                    <a href="/profile/{{ $user->id }}" class="font-medium">{{ $user->username }}</a>
                </div>
                @if(Auth::user()->id === $user->id && Auth::user()->id !== $user->id)
                <form action="{{ route('unfollow', $user->id) }}" method="POST">
                    @csrf                   
                    <button type="submit" class="text-red-500 px-3 py-1 text-xs rounded-md">Unfollow</button>
                </form>
                @endif
            </li>
            @empty
            <p class="text-gray-500">You're not following anyone yet.</p>
            @endforelse
        </ul>
    </div>


    <div class="bg-white p-4 shadow-sm rounded-md">
        <h2 class="text-lg font-semibold mb-3">People You Might Know</h2>
        <ul>
            @forelse($notFollowing as $user)
            <li class="flex justify-between items-center p-2">
                <div class="flex items-center gap-3">
                    <img src="{{ $user->profile->profileImage() }}" alt="profile image" class="w-[35px] h-[35px] rounded-full">
                    <a href="/profile/{{ $user->id }}" class="font-medium">{{ $user->username }}</a>
                </div>
                @if(Auth::user()->id !== $user->id)
                <form action="{{ route('follow', $user->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="text-blue-500 px-3 py-1 text-xs rounded-md">Follow</button>
                </form>
                @endif
            </li>
            @empty
            <p class="text-gray-500">No users to follow right now.</p>
            @endforelse
        </ul>
    </div>
</div>
@endsection