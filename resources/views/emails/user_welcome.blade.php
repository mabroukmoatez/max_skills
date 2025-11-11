<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenue sur notre plateforme</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #0056b3;
        }
        .credentials {
            background-color: #e9e9e9;
            padding: 15px;
            border-left: 5px solid #0056b3;
            margin-top: 20px;
        }
        .button {
            display: inline-block;
            background-color: #007bff;
            color: #ffffff !important;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .footer {
            margin-top: 30px;
            font-size: 0.9em;
            color: #777;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Bienvenue, {{ $user->firstname ?? $user->email }}!</h1>
        <p>Merci de vous être inscrit sur notre plateforme. Votre paiement a été traité avec succès et votre compte a été créé.</p>

        <p>Voici vos informations de connexion :</p>
        <div class="credentials">
            <p><strong>Email:</strong> {{ $user->email }}</p>
            <p><strong>Mot de passe:</strong> {{ $password }}</p>
        </div>

        <p>Nous vous recommandons de changer votre mot de passe après votre première connexion.</p>

        <a href="{{ url('/login') }}" class="button">Connectez-vous à votre compte</a>

        <p>Si vous avez des questions, n'hésitez pas à nous contacter.</p>

        <div class="footer">
            <p>Cordialement,</p>
            <p>{{ config('app.name') }}</p>
        </div>
    </div>
</body>
</html>