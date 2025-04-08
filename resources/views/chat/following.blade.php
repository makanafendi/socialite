@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-xl font-bold mb-4">People You Follow</h1>
    <div class="bg-white p-4 shadow-md rounded-md">
        <ul>
            @foreach($following as $user)
                <li class="py-2">
                    <a href="{{ route('chat.user', $user->id) }}" class="text-blue-500 font-semibold">
                        {{ $user->name }}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</div>
@endsection
