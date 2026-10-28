<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Jukeboxd') }} - @yield('title', 'Musique & Albums')</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-brand-light text-neutral-800 antialiased font-sans min-h-screen flex flex-col">
    <header class="bg-white border-b border-neutral-200 sticky top-0 z-50 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between gap-4">

            <a href="{{ url('/') }}" class="flex items-center shrink-0 transition-opacity hover:opacity-90">
                <img src="{{ asset('images/logos/logo.png') }}" 
                     alt="Jukeboxd Logo" 
                     class="h-10 w-auto object-contain"
                     onerror="this.onerror=null; this.src='https://dummyimage.com/180x40/39C142/ffffff&text=Jukeboxd';">
            </a>

            <div class="flex items-center gap-4 sm:gap-6">
                <form action="{{ url('/search') }}" method="GET" class="relative">
                    <div class="relative flex items-center">
                        <input type="text" 
                               name="q" 
                               placeholder="Rechercher un album, artiste..." 
                               value="{{ request('q') }}"
                               class="w-48 sm:w-72 md:w-80 h-10 pl-4 pr-10 rounded-full bg-brand-green text-black placeholder-neutral-700 text-sm font-medium border border-neutral-900 focus:outline-hidden focus:ring-2 focus:ring-neutral-900 transition-all" />
                        
                        <button type="submit" aria-label="Rechercher" class="absolute right-3 text-neutral-900 hover:scale-110 transition-transform">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z" />
                            </svg>
                        </button>
                    </div>
                </form>

                <nav class="flex items-center gap-3">
                    @auth
                        <div class="flex items-center gap-3">
                            <a href="{{ url('/profile') }}" class="text-sm font-semibold text-neutral-800 hover:text-brand-green transition-colors">
                                {{ Auth::user()->username }}
                            </a>

                            <form action="{{ route('logout') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-xs bg-neutral-200 hover:bg-neutral-300 text-neutral-700 font-medium px-3 py-1.5 rounded-full transition-colors cursor-pointer">
                                    Déconnexion
                                </button>
                            </form>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-medium text-neutral-700 hover:text-brand-green transition-colors">
                            Connexion
                        </a>
                        <a href="{{ route('register') }}" class="text-sm font-semibold bg-neutral-900 hover:bg-neutral-800 text-white px-3.5 py-1.5 rounded-full transition-colors">
                            Inscription
                        </a>
                    @endauth
                </nav>

            </div>
        </div>
    </header>

    @if (session('success') || session('info') || $errors->any())
        <div class="max-w-5xl mx-auto w-full px-4 pt-4">
            @if (session('success'))
                <div class="bg-emerald-50 border border-emerald-300 text-emerald-800 px-4 py-3 rounded-lg text-sm mb-3">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('info'))
                <div class="bg-blue-50 border border-blue-300 text-blue-800 px-4 py-3 rounded-lg text-sm mb-3">
                    {{ session('info') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-rose-50 border border-rose-300 text-rose-800 px-4 py-3 rounded-lg text-sm mb-3">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    @endif

    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @yield('content')
    </main>

    <footer class="bg-white border-t border-neutral-200 py-6 text-center text-xs text-neutral-500">
        <p>Jukeboxd</p>
    </footer>

</body>
</html>