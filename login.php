<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Read users from JSON file
    $userFile = 'users.json';
    $users = file_exists($userFile) ? json_decode(file_get_contents($userFile), true) : [];

    // Find the user and verify password
    foreach ($users as $user) {
        if ($user['username'] == $username && password_verify($password, $user['password'])) {
            // Store user in session
            $_SESSION['username'] = $username;
            echo "Login successful!";
            // Redirect or display success message
            header('Location: index.php'); // Redirect to home page after login
            exit;
        }
    }

    // If login failed
    echo "Invalid username or password!";
}
?>
