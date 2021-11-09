<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title> Log In </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <!-- App favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/favicon/apple-touch-icon.png')}}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/favicon/favicon-32x32.png')}}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/favicon/favicon-16x16.png')}}">
    <link rel="mask-icon" href="{{ asset('assets/favicon/safari-pinned-tab.svg')}}" color="#a0390e">
    <meta name="msapplication-TileColor" content="#8d0e0e">
    <meta name="theme-color" content="#ffffff">

    <!-- App css -->
    <link href="{{ asset('assets/css/config/creative/bootstrap.min.css')}}" rel="stylesheet" type="text/css"
        id="bs-default-stylesheet" />
    <link href="{{ asset('assets/css/config/creative/app.min.css')}}" rel="stylesheet" type="text/css"
        id="app-default-stylesheet" />
    <link href="{{ asset('assets/css/config/creative/app-dark.min.css')}}" rel="stylesheet" type="text/css"
        id="app-dark-stylesheet" />
    <!-- icons -->
    <link href="{{ asset('assets/css/icons.min.css')}}" rel="stylesheet" type="text/css" />
</head>

<body class="loading authentication-bg authentication-bg-pattern" style="background-color: #38414a;">

    <div class="account-pages mt-5 mb-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-5">
                    <div class="card">

                        <div class="card-body p-4">

                            <div class="text-center w-75 m-auto">
                                <div class="auth-logo">
                                    <span class="logo logo-dark text-center">
                                        <span class="logo-lg">
                                            <img src="{{ asset('assets/images/logo/poultryLogo.png') }}" alt=""
                                                height="100">
                                        </span>
                                    </span>
                                    <h4>Poultry Management System</h4>

                                </div>
                            </div>

                            <form action="{{ route('login') }}" method="POST" class="needs-validation" novalidate>
                                @csrf

                                <div class="form-group mb-3">
                                    <label for="email">{{ __('E-Mail Address')
                                        }}</label>
                                    <input class="form-control" id="email" type="email" name="email"
                                        value="{{ old('email') }}" required autocomplete="email" autofocus
                                        id="emailaddress" required placeholder="Enter your admission email">
                                    <div class="invalid-tooltip" style="position: initial;">
                                        Please provide a valid email address.
                                    </div>
                                    @error('email')
                                    <div class="invalid-tooltip" style="position: initial;">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="password">{{
                                        __('Password')
                                        }}</label>
                                    <input id="password" type="password" class="form-control" name="password" required
                                        autocomplete="current-password" placeholder="Enter Your Password">

                                    @error('password')
                                    <div class="invalid-tooltip" style="position: initial;">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                <div class="form-group mb-0 text-center">

                                    <button type="submit" class="btn btn-dark btn-block">
                                        {{ __('Login') }}
                                    </button>

                                    @if (Route::has('password.request'))
                                    {{-- <a class="btn btn-link" href="{{ route('password.request') }}">
                                        {{ __('Forgot Your Password?') }}
                                    </a> --}}
                                    @endif
                                </div>
                            </form>
                        </div> <!-- end card-body -->
                    </div>
                    <!-- end card -->
                </div> <!-- end col -->
            </div>
            <!-- end row -->
        </div>
        <!-- end container -->
    </div>
    <!-- end page -->


    <footer class="footer footer-alt text-white-50">
        <?php echo date('Y');?> &copy; All rights reserved. Poultry Management System(PMS)
    </footer>

    <!-- Vendor js -->
    <script src="{{ asset('assets/js/vendor.min.js') }}"></script>

    <!-- App js -->
    <script src="{{ asset('assets/js/app.min.js') }}"></script>

</body>

</html>