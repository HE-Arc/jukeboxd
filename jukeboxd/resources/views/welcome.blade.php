<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Jukeboxd</title>
</head>
<body>
    <h1>Jukeboxd</h1>

    @if (session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif

    @if (session('info'))
        <p style="color: blue;">{{ session('info') }}</p>
    @endif

    @auth
        <p>Connecté en tant que : <strong>{{ Auth::user()->username }}</strong> ({{ Auth::user()->email }})</p>

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit">Se déconnecter</button>
        </form>
    @else
        <p>Bienvenue sur Jukeboxd.</p>
        <a href="{{ route('login') }}"><button type="button">Login</button></a>
        <a href="{{ route('register') }}"><button type="button">Register</button></a>
    @endauth
</body>
</html>
