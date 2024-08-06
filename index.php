<?php
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

session_start();

$token = "jGG5j5H654lg645fdcxKj";

if (filter_input(INPUT_GET, "code") !== $token) {
    header('HTTP/1.0 404 Not Found', TRUE, 404);
    die("<h1>Not Found</h1><p>The requested URL was not found on this server.</p>");
}

$_SESSION['lang']  = filter_input(INPUT_GET, "lang") ?? 'eng';

//$_SESSION = [];

require_once "./includes/prop.php";

?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Housing Society | v1.1</title>
    <link rel="icon" type="image/x-icon" href="./imgs/house-30-16.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400..900&display=swap" rel="stylesheet">
    <link href="./libs/css/style.css" rel="stylesheet">
</head>


<body>
    <main class="form-signin w-100 m-auto">
        <div class="row">
            <div class="col-8" style="text-align: center;">
                <span class="h3 mb-3 fw-normal cinzel-400">
                    My <span style="font-size: 10px;">Housing</span> Society <i class="bi bi-buildings-fill"></i>
                </span>
            </div>
            <div class="col-2" id="btnLoginIcon"><i class="bi bi-box-arrow-in-right clickable" data-bs-toggle="modal" data-bs-target="#loginModel"></i></div>
            <div class="col-2" id="btnTranslateIcon"><a href="./index.php?lang=<?= $_SESSION['lang'] == 'eng' ? 'hin' : 'eng'  ?>&code=<?= $token ?>"><i class="bi bi-translate clickable"></i></a></div>
        </div>
        <div class="row">
            <div class="col-9">
                <input type="text" id="flat" name="flat" class="form-control" placeholder="<?= $prop['inputFlat'] ?>">
            </div>
            <div class="col-3">
                <button class="btn btn-primary" id="findf"><?= $prop['find'] ?></button>
            </div>
        </div>
        <hr>
        <div class="row" id="page1"></div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
        popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
        popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl));

        $(document).ready(function(e) {

            $('#page1').html('<div class="d-flex justify-content-center"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>');

            $.post("./views/society-details.php", {})
                .done(function(data) {
                    $("#page1").html(data);
                })
                .fail(function() {
                    $("#page1").html('<div class="col-12"><div class="alert alert-danger" role="alert"><?= $prop['failFlat'] ?><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div></div>');
                });
            <?php
            if (isset($_SESSION['user'])) {
            ?>
                $('#btnLoginIcon').html("<i class=\"bi bi-person\"> " + <?= $_SESSION['user'] ?> + "</i> <i class=\"bi bi-box-arrow-right clickable\" onclick=\"return logout();\"></i>")
                findFlat(<?= $_SESSION['user'] ?>);
            <?php
            }
            ?>

            $('#findf').click(function() {
                if ($('#flat').val()) {
                    if ($.isNumeric($('#flat').val())) {
                        findFlat($('#flat').val());
                    } else {
                        findOwner($('#flat').val());
                    }
                } else {
                    $("#page1").html('<div class="col-12"><div class="alert alert-danger" role="alert"><?= $prop['noFlat'] ?><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div></div>');
                }
            });

            const myModalEl = document.getElementById('registerModal')
            myModalEl.addEventListener('show.bs.modal', event => {
                $('#regemail').val("");
                $('#regflat').val("");
                $('#code').val("");
                $('#regpassword').val("");
                $('#regpassword2').val("");
            });

            const myModalE2 = document.getElementById('loginModel')
            myModalE2.addEventListener('show.bs.modal', event => {
                $('#email').val("");
                $('#password').val("");
            });

        });

        function findFlat(flatnum) {
            $('#page1').html('<div class="d-flex justify-content-center"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>');
            $.post("./views/flat-details.php", {
                    flat: flatnum
                })
                .done(function(data) {
                    $("#page1").html(data);
                })
                .fail(function() {
                    $("#page1").html('<div class="col-12"><div class="alert alert-danger" role="alert"><?= $prop['failFlat'] ?><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div></div>');
                });
        }

        function findOwner(flatnum) {
            $('#page1').html('<div class="d-flex justify-content-center"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>');
            $.post("./views/owner-details.php", {
                    keyword: flatnum
                })
                .done(function(data) {
                    $("#page1").html(data);
                })
                .fail(function() {
                    $("#page1").html('<div class="col-12"><div class="alert alert-danger" role="alert"><?= $prop['failFlat'] ?><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div></div>');
                });
        }

        function logout() {
            $.post("./includes/logout.php", {}, function(data) {
                    location.reload();
                })
                .fail(function() {
                    location.reload();
                });
        }

        function login() {
            $("#login-alert").html("");
            $('#btnLogin').prop("disabled", true);
            $('#btnLogin').html('<span class="spinner-grow spinner-grow-sm small-text" role="status" aria-hidden="true"></span>');
            $.post("./includes/login.php", {
                    email: $('#email').val(),
                    paswd: $('#password').val()
                }, function(data) {
                    json_data = JSON.parse(data);
                    if (json_data.status == 'success') {
                        $('#loginModel').modal('hide');
                        $('#btnLoginIcon').html("<i class=\"bi bi-person\"> " + json_data.message + "</i> <i class=\"bi bi-box-arrow-right clickable\" onclick=\"return logout();\"></i>");
                        findFlat(json_data.message);
                    } else {
                        $("#login-alert").html('<span class="label" style="color: red"><i class="bi bi-exclamation-triangle"></i> ' + json_data.message + '</span>');
                        $('#btnLogin').html('<?= $prop['login'] ?>');
                        $('#btnLogin').prop("disabled", false);
                    }
                })
                .fail(function() {
                    $("#login-alert").html('<span class="label" style="color: red"><i class="bi bi-exclamation-triangle"></i> Failed to login!</span>');
                    $('#btnLogin').html('<?= $prop['login'] ?>');
                    $('#btnLogin').prop("disabled", false);
                });
        }

        function otpSend() {
            $("#otp-alert").html("");
            $('#btnOtpSend').prop("disabled", true);
            $('#btnOtpSend').html('<span class="spinner-grow spinner-grow-sm small-text" role="status" aria-hidden="true"></span>');
            $.post("./includes/send-otp.php", {
                    email: $('#regemail').val(),
                    flat: $('#regflat').val()
                }, function(data) {
                    json_data = JSON.parse(data);
                    if (json_data.status == 'success') {
                        $('#btnOtpSend').html('Done');
                        $("#otp-alert").html('<span class="label" style="color: green"><i class="bi bi-hand-thumbs-up"></i> ' + json_data.message + '</span>');
                    } else {
                        $("#otp-alert").html('<span class="label" style="color: red"><i class="bi bi-exclamation-triangle"></i> ' + json_data.message + '</span>');
                        $('#btnOtpSend').html('OTP');
                        $('#btnOtpSend').prop("disabled", false);
                    }
                })
                .fail(function() {
                    $("#otp-alert").html('<span class="label" style="color: red"><i class="bi bi-exclamation-triangle"></i> Failed to send OTP!</span>');
                    $('#btnOtpSend').html('OTP');
                    $('#btnOtpSend').prop("disabled", false);
                });
        }

        function register() {
            $("#register-alert").html("");
            $('#btnRegister').prop("disabled", true);
            $('#btnRegister').html('<span class="spinner-grow spinner-grow-sm small-text" role="status" aria-hidden="true"></span>');
            $.post("./includes/register.php", {
                    otp: $('#code').val(),
                    pass: $('#regpassword').val(),
                    pass2: $('#regpassword2').val(),
                }, function(data) {
                    json_data = JSON.parse(data);
                    if (json_data.status == 'success') {
                        $('#btnRegister').html('Done');
                        $("#register-alert").html('<span class="label" style="color: green"><i class="bi bi-hand-thumbs-up"></i> ' + json_data.message + '</span>');
                    } else {
                        $("#register-alert").html('<span class="label" style="color: red"><i class="bi bi-exclamation-triangle"></i> ' + json_data.message + '</span>');
                        $('#btnRegister').html('<?= $prop['register'] ?>');
                        $('#btnRegister').prop("disabled", false);
                        if (json_data.message == "Try again later!") {
                            $('#btnOtpSend').html('<?= $prop['register'] ?>');
                            $('#btnOtpSend').prop("disabled", false);
                        }
                    }
                })
                .fail(function() {
                    $("#register-alert").html('<span class="label" style="color: red"><i class="bi bi-exclamation-triangle"></i> Registration failed!</span>');
                    $('#btnRegister').html('<?= $prop['register'] ?>');
                    $('#btnRegister').prop("disabled", false);
                });
        }
    </script>

    <!-- Modal -->
    <div class="modal fade" id="loginModel" tabindex="-1" aria-labelledby="loginModelLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="loginModelLabel">Sign-in</i>
                    </h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modal-body">
                    <span id="login-alert" class="small-text"></span>
                    <div class="col-12">
                        Email: <input type="text" id="email" name="email" class="form-control">
                    </div>
                    <div class="col-12">
                        Password: <input type="password" id="password" name="password" class="form-control">
                    </div>
                    <div class="row">
                        <div class="col-8">
                            <button class="btn btn-primary" id="btnLogin" onclick="return login();"><?= $prop['login'] ?></button>
                        </div>
                        <div class="col-4">
                            <a href="#" data-bs-target="#registerModal" data-bs-toggle="modal">Register</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="registerModalLabel">Register</i>
                    </h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modal-body">
                    <div class="col-12">
                        Email: <input type="text" id="regemail" name="regemail" class="form-control">
                    </div>
                    <div class="col-12">
                        Flat: <input type="text" id="regflat" name="regflat" class="form-control">
                    </div>
                    <div class="row">
                        <div class=" col-4">
                            <button class="btn btn-primary small-text" id="btnOtpSend" onclick="return otpSend();">OTP</button>
                        </div>
                        <div class="col-8">
                            <span id="otp-alert" class="small-text"></span>
                        </div>
                    </div>
                    <div class="col-12">
                        Code: <input type="text" id="code" name="code" class="form-control" placeholder="<?= $prop['inputCode'] ?>">
                    </div>
                    <div class="col-12">
                        Password: <input type="password" id="regpassword" name="pregassword" class="form-control">
                    </div>
                    <div class=" col-12">
                        Retype Password: <input type="password" id="regpassword2" name="regpassword2" class="form-control">
                    </div>
                    <div class="row">
                        <div class=" col-6">
                            <button class="btn btn-primary" id="btnRegister" onclick="return register();"><?= $prop['register'] ?></button>
                        </div>
                        <div class="col-6">
                            <span id="register-alert" class="small-text"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>