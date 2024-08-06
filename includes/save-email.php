<?php

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] == 'GET' && realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME'])) {
    header('HTTP/1.0 404 Not Found', TRUE, 404);
    die("<h1>Not Found</h1><p>The requested URL was not found on this server.</p>");
}

session_start();

$_SESSION['OPERATION'] = "SAVE-EMAIL";
$_SESSION['OTP_GEN'] = $otp_gen = rand(1000, 10000);

$email = filter_input(INPUT_POST, "email");
?>
<div class="row">
    <div class="col-4"><span class="label1">Flat No.</span></div>
    <div class="col-8"><span><?= $_SESSION['FLAT'] ?></span></div>
</div>
<?php
if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['EMAIL'] = $email;
    ?>
    <div class="row">
        <div class="col-4"><span class="label1">Email</span></div>
        <div class="col-8"><span><?= $email ?></span></div>
    </div>
    <hr>
    <div class="row">
        <div class="col-12" id="otp-alert" style="text-align: center;">
            <span style="color:chocolate; font-size:large"><i class="bi bi-question-diamond"></i> Is your email address correct?</span><br>
            <span style="color:black; font-size:smaller"></i>OTP will be sent on above email address.</span>
        </div>
        <div class="col-6">
            <button class="btn btn-primary" id="btnOtpSend" onclick="otpSend();">Yes, Send OTP</button>
        </div>
        <div class="col-6">
            <button class="btn btn-secondary" data-bs-dismiss="modal"> No </button>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <input type="password" id="otp" name="otp" class="form-control" placeholder="Type OTP received in email">
        </div>
        <div class="col-12">
            <button class="btn btn-primary" id="btnOtpVerify" onclick="otpVerify();" disabled>Verify OTP and Save Email ID</button>
        </div>
    </div>
    <?php
} else {
    ?>
    <div class="col-12">
        <div class="alert alert-danger" role="alert">
            Invalid email address!
        </div>
    </div>
    <?php
}
?>

<script>
    function otpSend() {
        $('#btnOtpSend').prop("disabled", true);
        $('#btnOtpSend').html('<span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span> Sending...');
        $.post("./includes/send-otp.php", function (data) {
            json_data = JSON.parse(data);
            if (json_data.status == 'success') {
                $('#btnOtpSend').html('Done');
                $("#otp-alert").html('<span class="label" style="color: green"><i class="bi bi-hand-thumbs-up"></i> ' + json_data.message + '</span>');
                $('#btnOtpVerify').prop("disabled", false);
            } else {
                $("#otp-alert").html('<span class="label" style="color: red"><i class="bi bi-exclamation-triangle"></i> ' + json_data.message + '</span>');
                $('#btnOtpSend').html('Yes, Send OTP');
                $('#btnOtpSend').prop("disabled", false);
            }
        })
            .fail(function () {
                $("#otp-alert").html('<span class="label" style="color: red"><i class="bi bi-exclamation-triangle"></i> Failed to send OTP.</span>');
                $('#btnOtpSend').html('Failed');
                $('#btnOtpSend').prop("disabled", false);
            });
    }

    function otpVerify() {
        $('#btnOtpVerify').prop("disabled", true);
        $('#btnOtpVerify').html('<span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span> Verifying...');
        $.post("./includes/verify-otp.php", { otp: $('#otp').val() }, function (data) {
            var json_data = JSON.parse(data);
            if (json_data.status == 'success') {
                $('#btnOtpVerify').html('Done');
                $("#otp-alert").html('<span class="label" style="color: green"><i class="bi bi-hand-thumbs-up"></i> ' + json_data.message + '</span>');
                $('#modal1').modal('hide');
                findFlat(<?= $_SESSION['FLAT'] ?>);
            } else {
                $("#otp-alert").html('<span class="label" style="color: red"> <i class="bi bi-exclamation-triangle"></i> ' + json_data.message + '</span>');
                $('#btnOtpVerify').html('Verify OTP and Save Email ID');
                $('#btnOtpVerify').prop("disabled", false);
            }
        })
            .fail(function () {
                $("#otp-alert").html('<span class="label" style="color: red"> <i class="bi bi-exclamation-triangle"></i> Failed to verify OTP.</span>');
                $('#btnOtpVerify').html('Failed');
                $('#btnOtpVerify').prop("disabled", false);
            });
    }
</script>