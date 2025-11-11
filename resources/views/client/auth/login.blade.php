@section('title', 'Connexion')
@include('layout.head')

@include('layout.css')
<style>
    @media (min-width: 768px) {
        .form_container {
            margin-top: -90px;
        }
    }
    .login-form-container .form_container {
        background-color :#ffffff00 !important;
        border: 1px solid #fff !important;
        border-radius : 35px !important;
    }

    .form-control {
        background-color : #010211 !important;
        color: #fff !important;

    }
    .form-control:focus {
        color: #fff !important;
    }
    .form-control::placeholder {
        color: #fff !important;
        opacity: 1 !important;
    }
    .form-control::-webkit-input-placeholder { /* Chrome, Safari, Opera */
        color: #fff !important;
        opacity: 1;
    }

    .form-control::-moz-placeholder { /* Firefox 19+ */
        color: #fff !important;
        opacity: 1;
    }

    .form-control:-ms-input-placeholder { /* IE 10+ */
        color: #fff !important;
        opacity: 1;
    }

    .form-control:-moz-placeholder { /* Firefox 18- */
        color: #fff !important;
        opacity: 1;
    }

    .btn-submit {
        background-color : #F8994F !important;
        color : #fff !important;
    }
    .btn.btn-submit.w-100 {
        padding : 10px 30px !important;
        margin-top : 8px !important;
        border-radius : 10px !important;
    }
    #logo-dash {
        display: flex;
        flex-direction: row;
        flex-wrap: nowrap;
        justify-content: center;
    }
    .app-form .form-select:focus, .app-form .form-control:focus{   
        box-shadow: 0 0 0 .25rem rgba(var(--primary), .0) !important; 
        border: 1px solid #fff !important;
     }
        
</style>
<body>
<div class="app-wrapper d-block">
    <div class="">
        <main class="w-100 p-0">
            <div class="container-fluid">
                <div class="row">

                    <div class="col-12 p-0">
                        <div class="login-form-container">
                            <div class="col-12 mb-0" id="logo-dash">
                                <a class="logo d-inline-block" href="">
                                    <img src="{{ asset('assets/images/background/logo-dash.png') }}"  width="250" style="margin-bottom:25px;" alt="#">
                                </a>
                            </div> 
                            <div class="col-12 form_container" style="margin-top:10px ;">
                                <form class="app-form" method="POST" action="{{ route('login_submit') }}">
                                    @csrf
                                    <div class="mb-3 text-center">
                                        <h3 style="color: #fff !important;">S’identifier</h3>
                                        <p class="f-s-12 text-secondary" style="color: #fff !important;">Content que tu sois de retour! 👋</p>
                                    </div>

                                    @if ($errors->any())
                                        <div class="alert alert-danger">
                                            {{ $errors->first('email') }}
                                        </div>
                                    @endif

                                    <div class="mb-3">
                                        <label class="form-label" style="color: #fff !important;">Adresse email</label>
                                        <input type="email" class="form-control" name="email" placeholder="Entrez votre email" value="{{ old('email') }}">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" style="color: #fff !important;">Mot de passe</label>
                                        <input type="password" class="form-control" name="password" placeholder="Entrez votre mot de passe">
                                    </div>
                                  
                                    <div>
                                        <button type="submit" class="btn btn-submit w-100">Se connecter</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

</body>
@section('script')
    <script src="{{asset('assets/vendor/bootstrap/bootstrap.bundle.min.js')}}"></script>
@endsection