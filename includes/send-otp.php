<?php

if ($_SERVER['REQUEST_METHOD'] == 'GET' && realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME'])) {
    header('HTTP/1.0 404 Not Found', TRUE, 404);
    die("<h1>Not Found</h1><p>The requested URL was not found on this server.</p>");
}

require_once './connect.php';

session_start();

$_SESSION['OTP_GEN'] = $otp_gen = rand(1000, 10000);


$email = filter_input(INPUT_POST, "email");
$flat = filter_input(INPUT_POST, "flat");

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo '{"status": "error", "message": "Invalid email address!"}';
    return 0;
}

if (!is_numeric($flat)) {
    echo '{"status": "error", "message": "Invalid flat number!"}';
    return 0;
}

$sql = "SELECT * FROM flats f WHERE flat = " . $flat;
$result = $conn->query($sql);

if ($result->num_rows <= 0) {
    echo '{"status": "error", "message": "Invalid flat number!"}';
    return 0;
}

require './PHPMailer/src/Exception.php';
require './PHPMailer/src/PHPMailer.php';
require './PHPMailer/src/SMTP.php';

$mail = new PHPMailer\PHPMailer\PHPMailer();
$mail->IsSMTP(); // enable SMTP
//$mail->SMTPDebug = 1; // debugging: 1 = errors and messages, 2 = messages only
$mail->SMTPAuth = true; // authentication enabled
$mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for Gmail
$mail->Host = "smtp.gmail.com";
$mail->Port = 465; // or 587
$mail->IsHTML(true);
$mail->Username = "<email>";
$mail->Password = "<password>";
$mail->From = 'no-reply.<email>';
$mail->FromName = 'My Housing Society';
$mail->addAddress($email);
$mail->addReplyTo('no-reply.<email>');
$mail->isHTML(true);
$mail->Subject = 'OTP - Validate Email Address';
$mail->Body = 'Dear ' . $flat . ',<br><br><b>' . $_SESSION['OTP_GEN'] . '</b> is your OTP to verify email address.<br>Your account will be activated once admin verify your details.<br><br>Thank You.';

//$_SESSION['OTP_GEN'] = 0000;
//echo '{"status": "success", "message": "OTP sent!"}';
//return;

if (!$mail->send()) {
    echo '{"status": "error", "message": "Failed to send OTP!"}';
} else {
    $_SESSION['email'] = $email;
    $_SESSION['flat'] = $flat;
    echo '{"status": "success", "message": "OTP sent!"}';
}

$conn->close();
