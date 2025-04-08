@extends('layouts.app')

@section('content')
<div class="container w-[640px] mx-auto">
    @foreach($posts as $post)
    <div class="bg-white flex flex-col gap-10 p-4 shadow-sm rounded-md mb-4">
        <div>
            <div>
                <a href="/profile/{{ $post->user->id }}">
                    <img src="/storage/{{ $post->image }}" alt="" class="w-full aspect-square">
                </a>
            </div>

            <hr class="border-t border-gray-300 my-4">

            <div class="flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <img src="{{ $post->user->profile->profileImage() }}" alt="" class="w-[35px] h-[35px] rounded-full">
                    <h3 class="text-sm font-semibold ">
                        <a href="/profile/{{ $post->user->id }}">{{ $post->user->username }}</a>
                    </h3>
                </div>
                <div>
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
            <p class="text-gray-500 text-sm flex mt-4">
                {{ $post->caption }}
            </p>
        </div>
    </div>

    @endforeach
    {{ $posts->links() }}
</div>
@endsection