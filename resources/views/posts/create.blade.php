@extends('layouts.app')

@section('content')
<div class="container max-w-[768px] mx-auto">
    <h1 class="text-3xl font-bold mb-4">Add a new post</h1>
    <form action="/p" enctype="multipart/form-data" method="POST" class="bg-white p-4 rounded-md shadow-md">
        @csrf
        <div class="mb-4">
            <input id="caption" type="caption" placeholder="Write a caption" class="bg-gray-200 rounded-md appearance-none w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('caption') border-red-500 @enderror" name="caption" value="{{ old('caption') }}">
            @error('caption')
            <p class="text-red-500 text-xs italic mt-2">
                <strong>{{ $message }}</strong>
            </p>
            @enderror
        </div>

        <div>
            <input type="file" id="image" name="image">
            @error('image')
            <p class="text-red-500 text-xs italic mt-2">
                <strong>{{ $message }}</strong>
            </p>
            @enderror
        </div>

        <div class="pt-4">
            <button class="bg-primary text-white px-4 py-2 rounded-md">Add new post</button>
        </div>
    </form>
</div>
@endsection