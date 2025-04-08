@extends('layouts.app')

@section('content')
<div class="container max-w-[768px] mx-auto">
    <h1 class="text-3xl font-bold mb-4">Edit Profile</h1>
    <form action="/profile/{{ $user->id }}" enctype="multipart/form-data" method="POST" class="bg-white p-4 rounded-md shadow-md">
        @csrf
        @method('PATCH')
        <div class="mb-4">
            <input id="title"
                type="text"
                placeholder="Write a title"
                maxlength="30"
                class="bg-gray-200 rounded-md appearance-none w-full py-2 px-3 leading-tight focus:outline-none focus:shadow-outline @error('title') border-red-500 @enderror"
                name="title"
                value="{{ old('title') ?? $user->profile->title }}"
                oninput="updateCharacterCount('title', 30)">

            <div class="flex items-center">
                @error('title')
                <p class="text-red-500 text-xs italic mt-2">
                    <strong>{{ $message }}</strong>
                </p>
                @enderror

                <p id="title-counter" class="text-gray-500 text-xs mt-1 ml-auto">
                    {{ strlen(old('title') ?? $user->profile->title) }} / 30
                </p>
            </div>
        </div>

        <div class="mb-4">
            <input id="description"
                type="text"
                placeholder="Write a description"
                maxlength="50"
                class="bg-gray-200 rounded-md appearance-none w-full py-2 px-3 leading-tight focus:outline-none focus:shadow-outline @error('description') border-red-500 @enderror"
                name="description"
                value="{{ old('description') ?? $user->profile->description }}"
                oninput="updateCharacterCount('description', 50)">

            <div class="flex items-center">
                @error('description')
                <p class="text-red-500 text-xs italic mt-2">
                    <strong>{{ $message }}</strong>
                </p>
                @enderror
                <p id="description-counter" class="text-gray-500 text-xs mt-1 ml-auto">
                    {{ strlen(old('description') ?? $user->profile->description) }} / 50
                </p>
            </div>
        </div>

        <div class="mb-4">
            <input id="url"
                type="text"
                placeholder="Write a url"
                maxlength="30"
                class="bg-gray-200 rounded-md appearance-none w-full py-2 px-3 leading-tight focus:outline-none focus:shadow-outline @error('url') border-red-500 @enderror"
                name="url"
                value="{{ old('url') ?? $user->profile->url }}"
                oninput="updateCharacterCount('url', 30)">

            <div class="flex items-center">
                @error('url')
                <p class="text-red-500 text-xs italic mt-2">
                    <strong>{{ $message }}</strong>
                </p>
                @enderror
                <p id="url-counter" class="text-gray-500 text-xs mt-1 ml-auto">
                    {{ strlen(old('url') ?? $user->profile->url) }} / 30
                </p>
            </div>
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
            <button class="bg-primary text-white px-4 py-2 rounded-md">Save Profile</button>
        </div>
    </form>
</div>
<script>
    function updateCharacterCount(fieldId, maxLength) {
        const input = document.getElementById(fieldId);
        const counter = document.getElementById(`${fieldId}-counter`);
        const currentLength = input.value.length;
        counter.textContent = `${currentLength} / ${maxLength}`;
    }
</script>
@endsection