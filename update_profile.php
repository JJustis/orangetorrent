<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    echo "You must be logged in to update your profile.";
    exit;
}

// Get the current logged-in user's username
$username = $_SESSION['username'];

// Define the path to the user's profile directory
$userFolder = 'users/' . $username;

// Check if the profile directory exists
if (!file_exists($userFolder)) {
    echo "User profile not found.";
    exit;
}

// Handle the form submission to update the bio and avatar
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Update the user's bio
    $bio = $_POST['bio'];

    // Handle avatar upload
    $avatar = '';
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $avatarTmpPath = $_FILES['avatar']['tmp_name'];
        $avatarName = basename($_FILES['avatar']['name']);
        $uploadDir = $userFolder . '/';
        $avatarFilePath = $uploadDir . $avatarName;

        // Move the uploaded avatar to the user's profile folder
        if (move_uploaded_file($avatarTmpPath, $avatarFilePath)) {
            $avatar = $avatarFilePath;
        }
    }

    // Update the profile page with the new bio and avatar
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
            <p>Email: user@example.com</p> <!-- Static email can be dynamic -->
            <p>Bio: $bio</p>
            <p><img src='" . ($avatar ? $avatar : 'default-avatar.png') . "' alt='Profile Avatar' style='width:150px; height:150px;'></p>
            <div class='edit-section'>
                <h3>Edit Profile</h3>
                <form action='../update_profile.php' method='post' enctype='multipart/form-data'>
                    <label for='bio'>Bio:</label>
                    <textarea name='bio' class='form-control' rows='4'>$bio</textarea>
                    <label for='avatar'>Upload Avatar:</label>
                    <input type='file' name='avatar' class='form-control'>
                    <button type='submit' class='btn btn-primary mt-2'>Update Profile</button>
                </form>
            </div>
        </div>
    </body>
    </html>
    ";

    // Save the updated profile page back to the user's directory
    file_put_contents($userFolder . '/index.php', $profilePage);

    // Show a success message or redirect back to the profile page
    echo "Profile updated successfully!";
    // header('Location: users/' . $username . '/index.php'); // Uncomment to redirect back to profile
}
?>
