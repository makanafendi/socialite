@extends('layouts.app')

@section('content')
<div class="container max-w-[768px] mx-auto">
    <div class="bg-white p-4 shadow-sm rounded-md">
        <div class="flex gap-4">
            <!-- Left side - Image -->
            <div class="w-[200px] h-[200px] aspect-square">
                <img src="/storage/{{ $post->image }}" alt="" class="w-full h-full object-cover">
            </div>

            <!-- Right side - Content -->
            <div class="flex flex-col flex-1">
                <!-- Profile section -->
                <div class="flex justify-between items-center pb-4 border-b border-gray-300">
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

                        @can('delete', $post)
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="p-1 hover:bg-gray-100 rounded-full">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                                </svg>
                            </button>
                            <div x-show="open" 
                                @click.away="open = false"
                                class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                                <form action="{{ route('posts.destroy', $post) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                        Delete Post
                                    </button>
                                </form>
                            </div>
                        </div>
                        @endcan
                    </div>
                </div>

                <!-- Description section -->
                <div class="flex-1 py-4">
                    <p class="text-gray-500 text-sm">
                        {{ $post->caption }}
                    </p>
                </div>

                <!-- Like system -->
                <div class="flex justify-end items-center">
                    <div x-data="likeSystem">
                        <button 
                            type="button" 
                            @click="toggleLike" 
                            class="flex items-center gap-1 transition-transform hover:scale-110"
                        >
                            <!-- Unlike heart icon -->
                            <svg 
                                x-show="!liked" 
                                x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0 scale-90"
                                x-transition:enter-end="opacity-100 scale-100"
                                class="w-6 h-6" 
                                fill="none" 
                                stroke="currentColor" 
                                viewBox="0 0 24 24"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                            
                            <!-- Like heart icon -->
                            <svg 
                                x-show="liked" 
                                x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0 scale-90"
                                x-transition:enter-end="opacity-100 scale-100"
                                class="w-6 h-6 text-red-500" 
                                fill="currentColor" 
                                viewBox="0 0 24 24"
                            >
                                <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path>
                            </svg>
                            
                            <!-- Like count -->
                            <span 
                                class="text-sm font-medium" 
                                x-text="likeCount"
                                x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0"
                                x-transition:enter-end="opacity-100"
                            ></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('likeSystem', () => ({
        liked: {{ $post->likedBy(auth()->user()) ? 'true' : 'false' }},
        likeCount: {{ $post->likes->count() }},
        
        toggleLike() {
            fetch('{{ route('posts.like', $post) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
            })
            .then(response => response.json())
            .then(data => {
                this.liked = data.liked;
                this.likeCount = data.count;
            });
        }
    }))
})
</script>
@endsection





