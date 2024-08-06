<?php

if ($_SERVER['REQUEST_METHOD'] == 'GET' && realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME'])) {
    header('HTTP/1.0 404 Not Found', TRUE, 404);
    die("<h1>Not Found</h1><p>The requested URL was not found on this server.</p>");
}

require_once './connect.php';

session_start();

$_SESSION = [];

$email = filter_input(INPUT_POST, "email");
$password = filter_input(INPUT_POST, "paswd");

if ($password == "" || $email == "") {
    echo '{"status": "error", "message": "Invalid login credentials"}';
    return 0;
}

$sql = "SELECT flat, password, role FROM users WHERE email = '" . $email . "' LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['password']) && $row['role'] > 0) {
            $_SESSION['user'] = $row['flat'];
            $_SESSION['role'] = $row['role'];
            echo '{"status": "success", "message": "' . $_SESSION['user'] . '"}';
        } else {
            echo '{"status": "error", "message": "Invalid login credentials"}';
            return 0;
        }
    }
} else {
    echo '{"status": "error", "message": "Invalid login credentials"}';
    return 0;
}

$conn->close();
