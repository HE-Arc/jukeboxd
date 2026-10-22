<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion - Jukeboxd</title>
</head>
<body>
    <h1>Connexion</h1>

    @if ($errors->any())
        <ul style="color: red;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form action="{{ route('login') }}" method="POST">
        @csrf
        <div>
            <label for="email">Email :</label><br>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required>
        </div>
        <br>
        <div>
            <label for="password">Mot de passe :</label><br>
            <input type="password" id="password" name="password" required>
        </div>
        <br>
        <div>
            <label>
                <input type="checkbox" name="remember"> Se souvenir de moi
            </label>
        </div>
        <br>
        <button type="submit">Se connecter</button>
    </form>

    <br>
    <a href="{{ url('/') }}">Retour à l'accueil</a> | <a href="{{ route('register') }}">Pas encore de compte ? S'inscrire</a>
</body>
</html>
