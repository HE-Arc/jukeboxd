@extends('layouts.app')

@section('title', $query !== '' ? 'Recherche : ' . $query : 'Recherche')

@section('content')
<div class="max-w-6xl mx-auto py-4">

    <div class="mb-8">
        <h1 class="text-2xl sm:text-3xl font-light text-neutral-900 tracking-tight">
            {{ $query !== '' ? "Résultats pour \"{$query}\"" : 'Rechercher un album ou un artiste' }}
        </h1>
    </div>

    @if (count($albums) > 0)
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4 sm:gap-5">
            @foreach ($albums as $album)
                <div class="flex flex-col">
                    <div class="aspect-square bg-neutral-200 rounded-md overflow-hidden shadow-xs relative">
                        @if (!empty($album['cover_url']))
                            <img src="{{ $album['cover_url'] }}" 
                                 alt="{{ $album['title'] }}" 
                                 loading="lazy" 
                                 class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-neutral-100 text-neutral-400 text-xs">
                                Pas de pochette
                            </div>
                        @endif
                    </div>

                    <div class="mt-2 flex items-start justify-between gap-2 text-left">
                        <div class="min-w-0 flex-1">
                            <h3 class="text-xs sm:text-sm font-semibold text-neutral-900 truncate" title="{{ $album['title'] }}">
                                {{ $album['title'] }}
                            </h3>
                            <p class="text-[11px] sm:text-xs text-neutral-600 truncate mt-0.5" title="{{ $album['artist'] }}">
                                {{ $album['artist'] }}
                            </p>
                            @if (!empty($album['year']))
                                <span class="text-[10px] sm:text-[11px] text-neutral-400 font-medium">
                                    {{ $album['year'] }}
                                </span>
                            @endif
                        </div>

                        <button type="button" 
                                title="Marquer comme écouté"
                                class="listened-btn p-1.5 rounded-full text-neutral-300 hover:text-neutral-600 hover:bg-neutral-100 transition-all shrink-0 cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 pointer-events-none" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-16 bg-white rounded-xl border border-neutral-200 text-neutral-600 text-sm">
            @if ($query === '')
                Utilisez la barre de recherche ci-dessus pour explorer des albums.
            @elseif (mb_strlen($query) < 2)
                Veuillez saisir au moins 2 caractères pour effectuer une recherche.
            @else
                Aucun album trouvé pour « {{ $query }} ».
            @endif
        </div>
    @endif

</div>
@endsection
