@extends('layouts.app')

@section('content')
<div class="container max-w-[768px] mx-auto">
    <div class="bg-white flex gap-4 p-4 shadow-sm rounded-md">
        <div class="w-[200px] h-[200px] aspect-square">
            <img src="/storage/{{ $post->image }}" alt="">
        </div>

        <div class="flex flex-col gap-2 w-full">
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <div>
                        <img src="{{$post->user->profile->profileImage() }}" alt="" class="w-[25px] h-[25px] rounded-full">
                    </div>
                    <h3 class="text-sm font-semibold">
                        <a href="/profile/{{ $post->user->id }}">{{ $post->user->username }}</a>
                    </h3>
                </div>
                <div class="flex gap-4 items-center">
                    @cannot('update', $post->user->profile)
                    @if(Auth::user()->following->contains($post->user))
                    <form action="{{ route('unfollow', $post->user->profile) }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-primary text-white px-2 py-1 text-xs rounded-md">Unfollow</button>
                    </form>
                    @else
                    <form action="{{ route('follow', $post->user->profile) }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-primary text-white px-2 py-1 text-xs rounded-md">Follow</button>
                    </form>
                    @endif
                    @endcannot
                </div>
            </div>

            <hr class="border-t border-gray-300">

            <p class="text-gray-500 text-sm flex gap-1">
                {{ $post->caption }}
            </p>
        </div>


    </div>
    @endsection