@section('title', 'Connexion')
@include('layout.head')

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
    /* --- Base Styles --- */
    html {
        height: 100%;
    }
    body {
        margin: 0;
        padding: 0;
        background-color: #000;
        font-family: sans-serif;
        color: #fff;
    }

    /* --- Desktop Styles (> 992px ) --- */
    @media (min-width: 993px) {
        body {
            /* This combination ensures perfect centering without overflow */
            display: grid;
            place-items: center;
            height: 100%;
        }
    }

    .main-container {
        width: 100%;
        max-width: 1000px;
        /* Padding is applied differently on mobile vs desktop */
    }

    .login-wrapper {
        display: grid;
        grid-template-columns: 1fr 1fr;
        background-color: #000;
        border: 1px solid #333;
        border-radius: 20px;
        overflow: hidden;
        margin-top:10px;
        margin-bottom:10px;
    }

    .image-container {
        display: block;
        height: min-content;
        max-height: 0;
    }

    .image-container img {
        width: 100%;
        height: 100%;
        object-fit: none;
    }

    .form-section {
        background-color: #000;
        padding: 3rem;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .form-section h1 {
        font-weight: bold;
        font-size: 1.8rem;
        margin-bottom: 0.5rem;
    }

    .form-section .subtitle {
        color: #a0a0a0;
        margin-bottom: 2rem;
    }

    .btn-google {
        background-color: #1a1a1a;
        border: 1px solid #444;
        color: #fff;
        width: 100%;
        padding: 0.75rem;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
        text-decoration: none;
        transition: background-color 0.3s;
        font-size: 0.9rem;
    }

    .btn-google:hover {
        background-color: #2d2d2d;
        color: #fff;
    }

    .divider {
        display: flex;
        align-items: center;
        text-align: center;
        color: #a0a0a0;
        margin: 0.6rem 0;
        font-size: 0.9rem;
    }

    .divider::before,
    .divider::after {
        content: '';
        flex: 1;
        border-bottom: 1px solid #444;
    }

    .divider:not(:empty)::before { margin-right: 1em; }
    .divider:not(:empty)::after { margin-left: 1em; }

    .form-label {
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
        color: #a0a0a0;
    }

    .form-control {
        background-color: #1a1a1a;
        border: 1px solid #444;
        color: #fff;
        padding: 0.75rem;
        border-radius: 8px;
    }

    .form-control:focus {
        background-color: #1a1a1a;
        color: #fff;
        border-color: #f97316;
        box-shadow: none;
    }
    
    .form-control::placeholder { color: #6c757d; }

    .forgot-password {
        text-align: left;
        font-size: 0.8rem;
        margin-top: 0.75rem;
    }

    .forgot-password a {
        color: #a0a0a0;
        text-decoration: none;
    }
    
    .forgot-password a:hover { color: #fff; }

    .btn-connexion {
        background-color: #f97316;
        color: #fff;
        width: 100%;
        padding: 0.75rem;
        border-radius: 8px;
        border: none;
        font-weight: bold;
        margin-top: 1.5rem;
        transition: background-color 0.3s;
    }
    
    .btn-connexion:hover { background-color: #ea580c; }

    .signup-link {
        text-align: center;
        margin-top: 0rem;
        font-size: 0.9rem;
        color: #a0a0a0;
    }

    .signup-link a {
        color: #f97316;
        font-weight: bold;
        text-decoration: none;
    }

    .form-label {
        color : #fff;
        font-weight: 700 !important;
    }
    /* --- Mobile Styles (<= 992px) --- */
    @media (max-width: 992px) {
        body {
            height: auto; /* Allow body to grow with content */
        }
        .main-container {
            padding: 1rem; /* Add padding for mobile view */
        }
        .login-wrapper {
            grid-template-columns: 1fr;
            border: none;
        }
        .image-container {
            display: none;
        }
        .form-section {
            padding: 2rem;
        }
    }
</style>

<body>

<div class="main-container">
    <div class="login-wrapper">
        <!-- Left Image Section -->
        <div class="image-container">
            <img src="{{ asset('assets/images/login/login_bg.png') }}" alt="Robot Visor Image">
        </div>

        <!-- Right Form Section -->
        <div class="form-section">
            <h1>Content de ton retour! 👋</h1>
            <p class="subtitle">Connectez-vous pour accéder à votre compte avec Google, ou utilisez votre e-mail et votre mot de passe.</p>

            <a href="{{ route('google.redirect') }}" class="btn btn-google">
                <img src="{{ asset('assets/images/login/google.png') }}" alt="Google Logo" width="18" height="18">
                <span>Connectez-vous avec Google</span>
            </a>

            <div class="divider">Ou</div>

            <form method="POST" action="{{ route('login_admin_submit' ) }}">
                @csrf

                <!-- Error Messages -->
                @if ($errors->any())
                    <div class="alert alert-danger" style="padding: 0.75rem 1rem; font-size: 0.9rem;">
                        {{ $errors->first('email') ?: $errors->first('password') }}
                    </div>
                @endif

                <div class="mb-3">
                    <label for="email" class="form-label">Adresse E-mail</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="eg: yahiahannachi@gmail.com" value="{{ old('email') }}" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Mot de passe</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Saisissez votre mot de passe" required>
                    <div class="forgot-password">
                        <a href="#">Mot de passe oublié ?</a>
                    </div>
                </div>
                <button type="submit" class="btn btn-connexion">Connexion</button>
            </form>

            <div class="signup-link">
                Vous avez besoin d'un compte? <a href="{{ route('register') }}">Inscrivez-vous</a>
            </div>
        </div>
    </div>
</div>

@section('script')
    <script src="{{asset('assets/vendor/bootstrap/bootstrap.bundle.min.js')}}"></script>
@endsection

</body>
</html>
