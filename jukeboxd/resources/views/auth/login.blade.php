@extends('layouts.app')

@section('title', 'Connexion')

@section('content')
<div class="max-w-md mx-auto my-8 sm:my-12">
    <div class="bg-white p-8 rounded-2xl shadow-xs border border-neutral-200">
        <h1 class="text-2xl font-bold text-neutral-900 text-center mb-6">Connexion</h1>

        <form action="{{ route('login') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label for="email" class="block text-sm font-medium text-neutral-700 mb-1">Adresse e-mail</label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       value="{{ old('email') }}" 
                       required 
                       autofocus
                       class="w-full px-4 py-2 rounded-xl border border-neutral-300 text-sm focus:outline-hidden focus:border-brand-green focus:ring-2 focus:ring-brand-green/20 transition-all">
                @error('email')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-neutral-700 mb-1">Mot de passe</label>
                <input type="password" 
                       id="password" 
                       name="password" 
                       required
                       class="w-full px-4 py-2 rounded-xl border border-neutral-300 text-sm focus:outline-hidden focus:border-brand-green focus:ring-2 focus:ring-brand-green/20 transition-all">
                @error('password')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between pt-1">
                <label class="flex items-center text-sm text-neutral-600 cursor-pointer">
                    <input type="checkbox" name="remember" class="rounded-sm border-neutral-300 text-brand-green focus:ring-brand-green mr-2">
                    Se souvenir de moi
                </label>
            </div>

            <button type="submit" 
                    class="w-full mt-4 py-2.5 px-4 rounded-full bg-brand-green text-black font-semibold text-sm hover:opacity-90 transition-all cursor-pointer shadow-xs">
                Se connecter
            </button>
        </form>

        <div class="mt-6 pt-6 border-t border-neutral-100 text-center text-sm text-neutral-600">
            Pas encore de compte ? 
            <a href="{{ route('register') }}" class="font-semibold text-neutral-900 hover:text-brand-green transition-colors">
                S'inscrire
            </a>
        </div>
    </div>
</div>
@endsection
