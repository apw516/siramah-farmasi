<!doctype html>
<html lang="en">

<head>
    <title>Login 04</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

    <link rel="stylesheet" href="{{ asset('public/login-form-14/login-form-14/css/style.css') }}">
    <style>
        .bgbg::before {
            content: "";
            background-image: url("public/login-form-14/login-form-14/images/bgwaled.jpg");
            background-size: 100%;
            position: absolute;
            top: 0px;
            right: 0px;
            height: 100%;
            bottom: 0px;
            left: 0px;
            opacity: 0.5;
        }

        h1 {
            text-shadow: 1px 2px white;
        }
    </style>
</head>

<body class="bgbg" style="position:relative">
    <section class="ftco-section shadow">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 text-center mb-5">
                    <h1 class="heading-section text-bold">SIRAMAH FARMASI</h1>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-md-12 col-lg-10">
                    <div style="box-shadow: 10px 10px 5px grey;"class="wrap d-md-flex">
                        <div class="img"
                            style="background-image: url(public/login-form-14/login-form-14/images/bg-2.avif);">
                        </div>
                        <div class="login-wrap p-4 p-md-5">
                            <div class="d-flex">
                                <div class="w-100">
                                    <h3 class="mb-4">Silahkan Login</h3>
                                </div>
                                <div class="w-100">
                                </div>
                            </div>
                            <form action="{{ route('login')}}" method="Post" class="signin-form">
                                @csrf
                                <div class="form-group mb-3">
                                    <label class="label" for="name">Username</label>
                                    <input type="text" class="form-control" placeholder="Username" required name="username" id="username">
                                </div>
                                <div class="form-group mb-3">
                                    <label class="label" for="password">Password</label>
                                    <input type="password" class="form-control" placeholder="Password" required name="password" id="password">
                                </div>
                                <div class="form-group">
                                    <button type="submit"
                                        style="background-color:rgb(141, 207, 207)"class="form-control btn rounded submit px-3">Login</button>
                                </div>
                                {{-- <div class="form-group d-md-flex">
                                    <div class="w-50 text-left">
                                        <label class="checkbox-wrap checkbox-primary mb-0">Remember Me
                                            <input type="checkbox" checked>
                                            <span class="checkmark"></span>
                                        </label>
                                    </div>
                                    <div class="w-50 text-md-right">
                                        <a href="#">Forgot Password</a>
                                    </div>
                                </div> --}}
                            </form>
                            {{-- <p class="text-center">Not a member? <a data-toggle="tab" href="#signup">Sign Up</a></p> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="{{ asset('public/login-form-14/login-form-14/js/jquery.min.js') }}"></script>
    <script src="{{ asset('public/login-form-14/login-form-14/js/popper.js') }}"></script>
    <script src="{{ asset('public/login-form-14/login-form-14/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('public/login-form-14/login-form-14/js/main.js') }}"></script>

</body>

</html>
