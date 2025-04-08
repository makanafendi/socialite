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
        @auth
        <nav class="bg-white shadow-md">
            <div class="container mx-auto px-4 py-4">
                <div class="flex justify-between items-center">
                    <!-- Logo -->
                    <a href="{{ url('/home') }}" class="flex items-center space-x-2">
                        <img src="/SVG/Socialite logo.svg" alt="Socialite Logo" class="w-[25px] h-[25px]">
                        <div class="text-xl font-medium">Socialite</div>
                    </a>

                    <!-- Right Side Navigation -->
                    <div class="flex items-center space-x-6">
                        <!-- Search Icon for Mobile -->
                        <a href="{{ route('search.page') }}" class="md:hidden">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </a>

                        <!-- Desktop Search -->
                        <div class="hidden md:block relative" x-data="{ searchQuery: '', searchResults: [], isSearching: false }">
                            <input 
                                type="text" 
                                placeholder="Search profiles..." 
                                x-model="searchQuery"
                                @input.debounce.300ms="
                                    if (searchQuery.length > 0) {
                                        isSearching = true;
                                        fetch(`/search?query=${searchQuery}`)
                                            .then(res => res.json())
                                            .then(data => {
                                                searchResults = data;
                                                isSearching = false;
                                            });
                                    } else {
                                        searchResults = [];
                                    }
                                "
                                @click.away="searchResults = []"
                                class="bg-gray-100 px-4 py-2 rounded-full text-sm focus:outline-none w-64"
                            >
                            
                            <!-- Search Results Dropdown -->
                            <div 
                                x-show="searchResults.length > 0"
                                class="absolute mt-2 w-full bg-white rounded-md shadow-lg py-1 z-50"
                            >
                                <template x-for="user in searchResults" :key="user.id">
                                    <a 
                                        :href="`/profile/${user.id}`"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center space-x-3"
                                    >
                                        <img :src="user.profile.image ? `/storage/${user.profile.image}` : '/storage/profile/default-avatar.png'" 
                                             class="w-8 h-8 rounded-full object-cover"
                                             :alt="user.username">
                                        <div>
                                            <div x-text="user.username" class="font-medium"></div>
                                            <div x-text="user.name" class="text-xs text-gray-500"></div>
                                        </div>
                                    </a>
                                </template>
                            </div>

                            <!-- Loading indicator -->
                            <div 
                                x-show="isSearching"
                                class="absolute right-3 top-2"
                            >
                                <svg class="animate-spin h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                        </div>

                        <!-- Profile Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center space-x-2 focus:outline-none" id="navbarDropdown">
                                <img src="{{ Auth::user()->profile->profileImage() }}" 
                                    alt="Profile" 
                                    class="w-8 h-8 rounded-full object-cover">
                                <span class="font-medium text-sm hidden md:block">{{ Auth::user()->username }}</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <!-- Dropdown Menu -->
                            <div x-show="open" 
                                @click.away="open = false"
                                class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50"
                                id="dropdownMenu">
                                <a href="/profile/{{ Auth::user()->id }}" 
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    Profile
                                </a>
                                <a href="/p/create" 
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    Create Post
                                </a>
                                <a href="{{ route('chat.list') }}" 
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    Messages
                                </a>
                                <hr class="my-1">
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" 
                                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
        @endauth

        <main class="py-8">
            <div class="container mx-auto px-4">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>

</html>







