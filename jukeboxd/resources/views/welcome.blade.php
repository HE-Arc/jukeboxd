@extends('layouts.app')

@section('title', Auth::check() ? Auth::user()->username : 'Accueil')

@section('content')
@auth
    <div class="max-w-4xl mx-auto px-4 py-6">
        
        <!-- En-tête du Profil -->
        <div class="flex flex-col items-center">
            <div class="flex items-center justify-center gap-10 sm:gap-14">
                <div class="flex flex-col items-center">
                    <h1 class="mt-3 text-lg sm:text-xl font-bold text-neutral-900 tracking-tight">
                        {{ Auth::user()->username }}
                    </h1>
                </div>
            </div>
        </div>

        @php
            $user = Auth::user();
            $activeTab = request('tab', 'profile');
            $listenedAlbums = $user->albums()->with('artist')->get();
        @endphp

        <div class="mt-8 border-b border-neutral-300">
            <div class="flex justify-center gap-12 sm:gap-16">
                <a href="{{ url('/profile') }}" class="pb-2 text-sm sm:text-base font-bold {{ $activeTab !== 'albums' ? 'text-neutral-900 border-b-2 border-brand-green' : 'text-neutral-500 hover:text-neutral-800' }} cursor-pointer">
                    Profil
                </a>
                <a href="{{ url('/profile?tab=albums') }}" class="pb-2 text-sm sm:text-base font-bold {{ $activeTab === 'albums' ? 'text-neutral-900 border-b-2 border-brand-green' : 'text-neutral-500 hover:text-neutral-800' }} cursor-pointer">
                    Albums
                </a>
            </div>
        </div>

        @if ($activeTab !== 'albums')
            <div class="mt-8">
                <h2 class="text-2xl sm:text-3xl font-light text-neutral-800 mb-6 tracking-wide">
                    Albums favoris
                </h2>

                @php
                    $favoriteAlbums = $user->albums()->with('artist')->wherePivot('is_favorite', true)->take(4)->get();
                @endphp

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 sm:gap-6">
                    @foreach ($favoriteAlbums as $album)
                        @php
                            $currentRating = (float) ($album->pivot->rating ?? 0);
                        @endphp
                        <div class="flex flex-col items-center">
                            <div class="w-full aspect-square bg-neutral-200 overflow-hidden shadow-xs hover:scale-105 transition-transform duration-200 cursor-pointer group relative">
                                <img src="{{ $album->cover_url }}" 
                                     alt="{{ $album->title }}" 
                                     title="{{ $album->title }} - {{ $album->artist?->name }}"
                                     class="w-full h-full object-cover">
                            </div>
                            <x-star-rating :rating="$currentRating" :album-id="$album->spotify_album_id" />
                        </div>
                    @endforeach
                    @for ($i = count($favoriteAlbums); $i < 4; $i++)
                        <div class="aspect-square bg-neutral-200 overflow-hidden shadow-xs flex items-center justify-center text-neutral-400">
                            <span class="text-2xl text-neutral-400">+</span>
                        </div>
                    @endfor
                </div>
            </div>
        @endif

        <div id="profile-listened-section" class="mt-10">
            <h2 class="text-2xl sm:text-3xl font-light text-neutral-800 mb-6 tracking-wide">
                {{ $activeTab === 'albums' ? 'Tous les albums écoutés' : 'Albums récents' }}
            </h2>

            @php
                $displayedAlbums = $activeTab === 'albums' ? $listenedAlbums : $listenedAlbums->take(4);
            @endphp

            @if ($displayedAlbums->isNotEmpty())
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 sm:gap-6">
                    @foreach ($displayedAlbums as $album)
                        @php
                            $currentRating = (float) ($album->pivot->rating ?? 0);
                        @endphp
                        <div class="flex flex-col">
                            <div class="w-full aspect-square bg-neutral-200 rounded-md overflow-hidden shadow-xs relative">
                                @if (!empty($album->cover_url))
                                    <img src="{{ $album->cover_url }}" 
                                         alt="{{ $album->title }}" 
                                         title="{{ $album->title }} - {{ $album->artist?->name }}"
                                         class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-neutral-100 text-neutral-400 text-xs">
                                        Pas de pochette
                                    </div>
                                @endif
                            </div>

                            <div class="mt-2 flex items-start justify-between gap-2">
                                <div class="min-w-0 flex-1">
                                    <h3 class="text-xs sm:text-sm font-semibold text-neutral-900 truncate" title="{{ $album->title }}">
                                        {{ $album->title }}
                                    </h3>
                                    <p class="text-[11px] sm:text-xs text-neutral-600 truncate mt-0.5" title="{{ $album->artist?->name }}">
                                        {{ $album->artist?->name }}
                                    </p>
                                </div>

                                <button type="button" 
                                        title="Retirer des albums écoutés"
                                        data-album-id="{{ $album->spotify_album_id }}"
                                        data-listened="true"
                                        class="listened-btn p-1.5 rounded-full text-brand-green bg-emerald-50 hover:text-neutral-600 hover:bg-neutral-100 transition-all shrink-0 cursor-pointer">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 pointer-events-none" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>

                            <x-star-rating :rating="$currentRating" :album-id="$album->spotify_album_id" />
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12 bg-white rounded-xl border border-neutral-200 text-neutral-500 text-sm">
                    Aucun album écouté pour le moment. Utilisez la barre de recherche pour en ajouter !
                </div>
            @endif
        </div>

    </div>
@else
    <!-- Vue invité -->
    <div class="text-center py-16">
        <h1 class="text-4xl sm:text-5xl font-extrabold text-neutral-900 tracking-tight">
            <span class="text-brand-green">Jukeboxd</span>
        </h1>

        <div class="mt-8 flex justify-center gap-4">
            <a href="{{ route('login') }}" class="px-6 py-2.5 rounded-full bg-brand-green text-black font-semibold shadow-xs hover:opacity-90 transition-all">
                Se connecter
            </a>
            <a href="{{ route('register') }}" class="px-6 py-2.5 rounded-full bg-white text-neutral-800 font-semibold border border-neutral-300 shadow-xs hover:bg-neutral-50 transition-all">
                Créer un compte
            </a>
        </div>
    </div>
@endauth
@endsection