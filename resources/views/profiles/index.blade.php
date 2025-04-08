@extends('layouts.app')

@section('content')
<div class="container max-w-[1000px] mx-auto px-4 py-8">
    <!-- Profile Header -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="md:flex md:items-start p-8 gap-12">
            <!-- Profile Picture -->
            <div class="flex-shrink-0 mb-6 md:mb-0">
                <div class="w-48 h-48 rounded-2xl overflow-hidden shadow-lg border-4 border-white">
                    <img src="{{ $user->profile->profileImage() }}" 
                         alt="{{ $user->username }}'s profile"
                         class="w-full h-full object-cover">
                </div>
            </div>

            <!-- Profile Info -->
            <div class="flex-grow">
                <div class="flex flex-col gap-6">
                    <!-- Username and Actions -->
                    <div class="flex flex-wrap items-center gap-4">
                        <h1 class="text-2xl font-bold text-gray-900">{{ $user->username }}</h1>
                        
                        <div class="flex gap-3">
                            @cannot('update', $user->profile)
                                @if(Auth::user()->following->contains($user->id))
                                    <form action="{{ route('unfollow', $user->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" 
                                                class="px-6 py-2 text-sm font-medium text-red-500 hover:text-white border-2 border-red-500 hover:bg-red-500 rounded-full transition-colors duration-300">
                                            Unfollow
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('follow', $user->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" 
                                                class="px-6 py-2 text-sm font-medium text-blue-500 hover:text-white border-2 border-blue-500 hover:bg-blue-500 rounded-full transition-colors duration-300">
                                            Follow
                                        </button>
                                    </form>
                                @endif
                            @endcannot

                            @can('update', $user->profile)
                                <a href="/p/create" 
                                   class="inline-flex items-center px-6 py-2 text-sm font-medium text-white bg-blue-500 hover:bg-blue-600 rounded-full transition-colors duration-300">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Add Post
                                </a>
                                <a href="/profile/{{ $user->id }}/edit" 
                                   class="inline-flex items-center px-6 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 bg-gray-100 hover:bg-gray-200 rounded-full transition-colors duration-300">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                    </svg>
                                    Edit Profile
                                </a>
                            @endcan
                        </div>
                    </div>

                    <!-- Stats -->
                    <div class="flex gap-8">
                        <div class="text-center">
                            <span class="block text-xl font-bold text-gray-900">{{ $postCount }}</span>
                            <span class="text-sm text-gray-600">Posts</span>
                        </div>
                        <a href="{{ route('following.page', $user->id) }}" class="text-center hover:text-blue-500 transition-colors">
                            <span class="block text-xl font-bold text-gray-900">{{ $followerCount }}</span>
                            <span class="text-sm text-gray-600">Followers</span>
                        </a>
                        <a href="{{ route('following.page', $user->id) }}" class="text-center hover:text-blue-500 transition-colors">
                            <span class="block text-xl font-bold text-gray-900">{{ $followingCount }}</span>
                            <span class="text-sm text-gray-600">Following</span>
                        </a>
                    </div>

                    <!-- Bio -->
                    <div class="max-w-2xl">
                        <p class="text-gray-700 leading-relaxed">
                            {{ $user->profile->description ?: 'No bio yet.' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Posts Grid -->
    <div class="mt-12">
        <h2 class="text-xl font-semibold text-gray-900 mb-6">Posts</h2>
        
        @if($user->posts->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($user->posts as $post)
                    <div class="relative group">
                        <a href="/p/{{ $post->id }}" class="block aspect-square overflow-hidden rounded-xl shadow-sm">
                            <img src="/storage/{{ $post->image }}" 
                                 alt="Post by {{ $user->username }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-300"></div>
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12 bg-gray-50 rounded-xl">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
                          d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-1">No Posts Yet</h3>
                <p class="text-gray-500">When {{ $user->username }} shares photos, they'll appear here.</p>
            </div>
        @endif
    </div>
</div>
@endsection

