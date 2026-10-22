<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription - Jukeboxd</title>
</head>
<body>
    <h1>Inscription</h1>

    @if ($errors->any())
        <ul style="color: red;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form action="{{ route('register') }}" method="POST">
        @csrf
        <div>
            <label for="username">Nom d'utilisateur :</label><br>
            <input type="text" id="username" name="username" value="{{ old('username') }}" required>
        </div>
        <br>
        <div>
            <label for="email">Email :</label><br>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required>
        </div>
        <br>
        <div>
            <label for="password">Mot de passe (min. 8 caractères) :</label><br>
            <input type="password" id="password" name="password" required>
        </div>
        <br>
        <div>
            <label for="password_confirmation">Confirmer le mot de passe :</label><br>
            <input type="password" id="password_confirmation" name="password_confirmation" required>
        </div>
        <br>
        <button type="submit">S'inscrire</button>
    </form>

    <br>
    <a href="{{ url('/') }}">Retour à l'accueil</a> | <a href="{{ route('login') }}">Déjà un compte ? Se connecter</a>
</body>
</html>
