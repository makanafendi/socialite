<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Socialite</title>

    <!-- Fonts -->


    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 font-sans relative">
    <div id="app">
        <nav class="bg-white shadow-md">
            <div class="container mx-auto px-4 py-4 flex justify-between items-center">
                <a class="flex items-center space-x-2" href="{{ url('/login') }}">
                    <img src="/SVG/Socialite logo.svg" alt="Laravel Logo" class="w-[25px] h-[25px]">
                    <div class="text-xl font-bold">Socialite</div>
                </a>

                <div class="flex items-center space-x-4">
                    <!-- Authentication Links -->
                    @guest
                    @if (Route::has('login'))
                    <a class="text-gray-600 hover:text-gray-800" href="{{ route('login') }}">{{ __('Login') }}</a>
                    @endif

                    @if (Route::has('register'))
                    <a class="text-gray-600 hover:text-gray-800" href="{{ route('register') }}">{{ __('Register') }}</a>
                    @endif
                    @else
                    <div class="relative">
                        <div class="flex items-center gap-2">
                            <div>
                                <a href="/profile/{{ Auth::user()->id }}">
                                    <img src="{{Auth::user()->profile->profileImage() }}" alt="profile image" class="w-[25px] h-[25px] rounded-full">
                                </a>
                            </div>
                            <button
                                id="navbarDropdown"
                                class="text-gray-600 hover:text-gray-800 focus:outline-none"
                                onclick="toggleDropdown()">
                                <h1 class="text-sm font-bold">{{ Auth::user()->username }}</h1>
                            </button>
                        </div>
                        <div
                            id="dropdownMenu"
                            class="hidden absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-md shadow-lg">
                            <a
                                class="block px-4 py-2 text-gray-700 hover:bg-gray-100"
                                href="{{ route('logout') }}"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                {{ __('Logout') }}
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                                @csrf
                            </form>
                        </div>
                    </div>
                    @endguest
                </div>
            </div>
        </nav>

        @if (!Str::contains(request()->path(), 'chat'))
        <div class="fixed bottom-0 right-0 m-10">
            <a href="{{ route('chat.list') }}">
                <div class="bg-white p-2 text-xs shadow-md rounded-md">message here</div>
            </a>
        </div>
        @endif

        <main class="py-8">
            <div class="container mx-auto px-4">
                @yield('content')
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('dropdownMenu');
            const button = document.getElementById('navbarDropdown');

            if (button.contains(event.target)) {
                // Toggle dropdown visibility
                dropdown.classList.toggle('hidden');
            } else if (!dropdown.contains(event.target)) {
                // Hide dropdown if clicking outside
                dropdown.classList.add('hidden');
            }
        });
    </script>
</body>

</html>