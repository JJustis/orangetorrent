<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['newUsername'];
    $email = $_POST['email'];
    $password = password_hash($_POST['newPassword'], PASSWORD_DEFAULT); // Hash the password for security

    // Read current users from JSON file
    $userFile = 'users.json';
    $users = file_exists($userFile) ? json_decode(file_get_contents($userFile), true) : [];

    // Check if username already exists
    foreach ($users as $user) {
        if ($user['username'] == $username) {
            echo "Username already exists!";
            exit;
        }
    }

    // Create new user entry
    $newUser = [
        "username" => $username,
        "email" => $email,
        "password" => $password
    ];

    // Add new user to the data array
    $users[] = $newUser;

    // Write updated data to users.json
    file_put_contents($userFile, json_encode($users, JSON_PRETTY_PRINT));

    // Create user's folder and profile page
    $userFolder = 'users/' . $username;
    if (!file_exists($userFolder)) {
        mkdir($userFolder, 0777, true);
    }

    // Create a basic profile page for the user
    $profilePage = "
    <?php
    session_start();
    \$isOwner = isset(\$_SESSION['username']) && \$_SESSION['username'] === '$username';
    ?>
    <!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>$username's Profile</title>
        <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css' rel='stylesheet'>
        <style>
            body { padding: 20px; font-family: Arial, sans-serif; }
            .profile-container { max-width: 800px; margin: auto; }
            .edit-section { display: none; }
            <?php if (\$isOwner): ?>
            .edit-section { display: block; }
            <?php endif; ?>
        </style>
    </head>
    <body>
        <div class='profile-container'>
            <h1>Welcome to $username's Profile</h1>
            <p>Email: $email</p>
            <div class='edit-section'>
                <h3>Edit Profile</h3>
                <form action='update_profile.php' method='post' enctype='multipart/form-data'>
                    <label for='bio'>Bio:</label>
                    <textarea name='bio' class='form-control' rows='4'><?php echo isset(\$_POST['bio']) ? \$_POST['bio'] : ''; ?></textarea>
                    <label for='avatar'>Upload Avatar:</label>
                    <input type='file' name='avatar' class='form-control'>
                    <button type='submit' class='btn btn-primary mt-2'>Update Profile</button>
                </form>
            </div>
        </div>
    </body>
    </html>
    ";

    // Write the profile page to the user's folder
    file_put_contents($userFolder . '/index.php', $profilePage);

    // Redirect to the login page or show success message
    echo "Registration successful! Your profile has been created.";
    header('Location: index.php'); // Uncomment to redirect after registration
}
?>
