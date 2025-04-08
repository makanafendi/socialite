@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4">
        <div class="flex justify-center">
            <div class="w-full max-w-md">
                <div class="bg-white shadow-md rounded px-8 py-6">
                    <div class="text-lg font-semibold mb-4">{{ __('Verify Your Email Address') }}</div>

                    <div class="mb-4">
                        @if (session('resent'))
                            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded" role="alert">
                                {{ __('A fresh verification link has been sent to your email address.') }}
                            </div>
                        @endif
                    </div>

                    <p class="text-gray-700 mb-4">
                        {{ __('Before proceeding, please check your email for a verification link.') }}
                    </p>
                    <p class="text-gray-700 mb-4">
                        {{ __('If you did not receive the email') }},
                    </p>

                    <form method="POST" action="{{ route('verification.resend') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-blue-500 hover:text-blue-700 font-bold p-0 m-0 align-baseline">
                            {{ __('click here to request another') }}
                        </button>.
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
