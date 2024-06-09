<!doctype html>
<html lang="en">

<head>
    <title>Ambil Antrian Farmasi</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

    <link rel="stylesheet" href="{{ asset('public/login-form-14/login-form-14/css/style.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

        .preloader2 {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 9999;
            background-color: #fff;
            opacity: 0.9;
        }

        .preloader2 .loading {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            font: 14px arial;
        }
    </style>
</head>

<body class="bgbg" style="position:relative">
    <div class="preloader2" id="loader2">
        <div class="loading">
            <img src="{{ asset('public/img/fb.gif') }}" width="80">
            <p>Harap Tunggu</p>
        </div>
    </div>
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

                            <form method="Post" class="signin-form">
                                @csrf
                                <div class="form-group mb-3">
                                    <label class="label" for="name">Masukan nomor rekamedis anda</label>
                                    <input type="text" class="form-control"
                                        placeholder="Ketik / scan nomor rekamedis anda ..." required name="nomorrm"
                                        id="nomorrm">
                                </div>
                                <div class="form-group">
                                    <button type="button"
                                        style="background-color:rgb(141, 207, 207)"class="form-control btn rounded submit px-3"
                                        onclick="ambilantrian()">Ambil Antrian</button>
                                </div>
                            </form>
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
    <script>
        $(".preloader2").fadeOut();

        function ambilantrian() {
            rm = $('#nomorrm').val()
            spinner = $('#loader')
            spinner.show();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    _token: "{{ csrf_token() }}",
                    rm
                },
                url: '<?= route('ambilantrian') ?>',
                error: function(data) {
                    spinner.hide()
                    Swal.fire({
                        icon: 'error',
                        title: 'Ooops....',
                        text: 'Sepertinya ada masalah......',
                        footer: ''
                    })
                },
                success: function(data) {
                    spinner.hide()
                    if (data.kode == 500) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oopss...',
                            text: data.message,
                            footer: ''
                        })
                    } else {
                        Swal.fire({
                            icon: 'success',
                            title: 'OK',
                            text: data.message,
                            footer: ''
                        })
                    }
                }
            })
        }
    </script>
</body>

</html>
