<?php 


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = $_POST["username"];
    $password = $_POST["password"];

    if ($username == "TKSB1" && $password == "1234") {
        session_start();
        $_SESSION["username"] = $username;
        header("Location: /");
        exit;
    } else {
        header("Location: /login");
        exit;
    }
}