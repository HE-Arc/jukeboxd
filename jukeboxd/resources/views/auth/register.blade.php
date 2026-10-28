@extends('layouts.app')

@section('title', 'Inscription')

@section('content')
<div class="max-w-md mx-auto my-8 sm:my-12">
    <div class="bg-white p-8 rounded-2xl shadow-xs border border-neutral-200">
        <h1 class="text-2xl font-bold text-neutral-900 text-center mb-6">Créer un compte</h1>

        <form action="{{ route('register') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label for="username" class="block text-sm font-medium text-neutral-700 mb-1">Nom d'utilisateur</label>
                <input type="text" 
                       id="username" 
                       name="username" 
                       value="{{ old('username') }}" 
                       required 
                       autofocus
                       class="w-full px-4 py-2 rounded-xl border border-neutral-300 text-sm focus:outline-hidden focus:border-brand-green focus:ring-2 focus:ring-brand-green/20 transition-all">
                @error('username')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-neutral-700 mb-1">Adresse e-mail</label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       value="{{ old('email') }}" 
                       required
                       class="w-full px-4 py-2 rounded-xl border border-neutral-300 text-sm focus:outline-hidden focus:border-brand-green focus:ring-2 focus:ring-brand-green/20 transition-all">
                @error('email')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-neutral-700 mb-1">Mot de passe (8 caractères minimum)</label>
                <input type="password" 
                       id="password" 
                       name="password" 
                       required
                       class="w-full px-4 py-2 rounded-xl border border-neutral-300 text-sm focus:outline-hidden focus:border-brand-green focus:ring-2 focus:ring-brand-green/20 transition-all">
                @error('password')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-neutral-700 mb-1">Confirmer le mot de passe</label>
                <input type="password" 
                       id="password_confirmation" 
                       name="password_confirmation" 
                       required
                       class="w-full px-4 py-2 rounded-xl border border-neutral-300 text-sm focus:outline-hidden focus:border-brand-green focus:ring-2 focus:ring-brand-green/20 transition-all">
            </div>

            <button type="submit" 
                    class="w-full mt-4 py-2.5 px-4 rounded-full bg-brand-green text-black font-semibold text-sm hover:opacity-90 transition-all cursor-pointer shadow-xs">
                S'inscrire
            </button>
        </form>

        <div class="mt-6 pt-6 border-t border-neutral-100 text-center text-sm text-neutral-600">
            Déjà inscrit ? 
            <a href="{{ route('login') }}" class="font-semibold text-neutral-900 hover:text-brand-green transition-colors">
                Se connecter
            </a>
        </div>
    </div>
</div>
@endsection
