<?php

if ($_SERVER['REQUEST_METHOD'] == 'GET' && realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME'])) {
    header('HTTP/1.0 404 Not Found', TRUE, 404);
    die("<h1>Not Found</h1><p>The requested URL was not found on this server.</p>");
}

require_once './connect.php';

session_start();

$password = filter_input(INPUT_POST, "pass");

if ($password == "" || $password != filter_input(INPUT_POST, "pass2")) {
    echo '{"status": "error", "message": "Password do not match!"}';
    return 0;
}

if (!isset($_SESSION['OTP_GEN'])) {
    echo '{"status": "error", "message": "Try again later!"}';
    return 0;
}

if ($_SESSION['OTP_GEN'] != filter_input(INPUT_POST, "otp")) {
    $_SESSION['OTP_CHECK'] += 1;

    if ($_SESSION['OTP_CHECK'] >= 4) {
        unset($_SESSION['OTP_GEN']);
        echo '{"status": "error", "message": "Try again later!"}';
        header("Refresh:0");
        return 0;
    }

    echo '{"status": "error", "message": "Invalid OTP!"}';
    return 0;
}


$sql = "INSERT INTO users (flat, email, password) VALUES ('" . $_SESSION['flat'] . "', '" . $_SESSION['email'] . "', '" . password_hash($password, PASSWORD_DEFAULT) . "')";
if ($conn->query($sql) === TRUE) {
    echo '{"status": "success", "message": "User registered! Please login."}';
} else {
    echo '{"status": "error", "message": "Failed to register!"}';
}

$_SESSION = [];

$conn->close();
