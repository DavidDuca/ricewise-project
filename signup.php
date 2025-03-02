<?php
define('SECURE_ACCESS', true);
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $mobile_number = $_POST['No'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirmPassword'];
    $age = $_POST['age'];
    $sex = $_POST['sex'];
    $weight = $_POST['weight'];

    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    $stmt = $conn->prepare("INSERT INTO users (username, mobile_number, email, password_hash, age, sex, weight) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssisi", $username, $mobile_number, $email, $hashed_password, $age, $sex, $weight);

    if ($password === $confirm_password){
        if ($stmt->execute()) {
            header("Location: login.html");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }

    }else{
        echo "Error: Password and Confirm Password does not match " . $stmt->error;
    }


    $stmt->close();
}

$conn->close();
?>
