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

        <div class="mt-8 border-b border-neutral-300">
            <div class="flex justify-center gap-12 sm:gap-16">
                <button type="button" class="pb-2 text-sm sm:text-base font-bold text-neutral-900 border-b-2 border-brand-green cursor-pointer">
                    Profil
                </button>
            </div>
        </div>

        <div class="mt-8">
            <h2 class="text-2xl sm:text-3xl font-light text-neutral-800 mb-6 tracking-wide">
                Albums favoris
            </h2>

            @php
                $userFavorites = Auth::user()->albums()->wherePivot('is_favorite', true)->take(4)->get();
                $albumsToDisplay = $userFavorites->isNotEmpty() ? $userFavorites : collect(range(1, 4))->map(fn() => (object) [
                    'title' => '',
                    'cover_url' => '',
                    'pivot' => (object) ['rating' => 0]
                ]);
            @endphp

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 sm:gap-6">
                @foreach ($albumsToDisplay as $album)
                    @php
                        $currentRating = (float) ($album->pivot->rating ?? 0);
                    @endphp
                    <div class="flex flex-col items-center">
                        <div class="w-full aspect-square bg-neutral-200 overflow-hidden shadow-xs hover:scale-105 transition-transform duration-200 cursor-pointer group relative">
                            @if (!empty($album->cover_url))
                                <img src="{{ $album->cover_url }}" 
                                     alt="{{ $album->title }}" 
                                     class="w-full h-full object-cover">
                            @endif
                        </div>

                        <x-star-rating :rating="$currentRating" />
                    </div>
                @endforeach
            </div>
        </div>

    </div>
@else
    <!-- Vue invité -->
    <div class="text-center py-20">
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