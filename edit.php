<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    echo "You must be logged in to edit a torrent.";
    // header('Location: login.html'); // Uncomment to redirect to login page
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $torrentID = $_POST['torrentID'];
    $newName = $_POST['newName'];
    $newType = $_POST['newType'];
    $newMagnet = $_POST['newMagnet'];
    $newDescription = $_POST['newDescription'];

    // Read the existing torrents from torrents.json
    $torrentFile = 'torrents.json';
    $torrentData = file_exists($torrentFile) ? json_decode(file_get_contents($torrentFile), true) : [];

    // Handle image upload
    $newImage = '';
    if (isset($_FILES['newImage']) && $_FILES['newImage']['error'] === UPLOAD_ERR_OK) {
        $imageTmpPath = $_FILES['newImage']['tmp_name'];
        $imageName = basename($_FILES['newImage']['name']);
        $uploadDir = 'uploads/';
        $imageFilePath = $uploadDir . $imageName;
        
        if (move_uploaded_file($imageTmpPath, $imageFilePath)) {
            $newImage = $imageFilePath;
        }
    }

    // Find and update the torrent with the given ID
    $found = false;
    foreach ($torrentData as &$torrent) {
        if ($torrent['id'] == $torrentID) {
            if ($torrent['poster'] != $_SESSION['username']) {
                // User does not own the torrent, deny access
                echo "You do not have permission to edit this torrent.";
                exit;
            }

            // Update the torrent fields with new data
            $torrent['name'] = $newName;
            $torrent['type'] = $newType;
            $torrent['magnet'] = $newMagnet;
            $torrent['description'] = $newDescription;
            if ($newImage) {
                $torrent['image'] = $newImage; // Only update image if new one is provided
            }
            $found = true;

            // Create or update the folder for the torrent with its own index page
            $torrentFolder = 'torrents/' . preg_replace('/[^a-zA-Z0-9_-]/', '_', strtolower($newName));
            if (!file_exists($torrentFolder)) {
                mkdir($torrentFolder, 0777, true);
            }

            // Create the index page for this torrent
            $indexContent = "
            <!DOCTYPE html>
            <html lang='en'>
            <head>
                <meta charset='UTF-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <title>$newName - Torrent</title>
                <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css' rel='stylesheet'>
                <style>
                    body { padding: 20px; font-family: Arial, sans-serif; }
                    .torrent-detail { max-width: 800px; margin: auto; }
                    .torrent-header { display: flex; align-items: center; }
                    .torrent-header img { width: 150px; height: 150px; margin-right: 20px; }
                    .torrent-content { margin-top: 20px; }
                    .torrent-content p { font-size: 1.1em; }
                </style>
            </head>
            <body>
                <div class='torrent-detail'>
                    <div class='torrent-header'>
                        <img src='" . ($torrent['image'] ?? 'https://betahut.bounceme.net/dashboard/uploads/6702e88c9a9a8.png') . "' alt='$newName Image'>
                        <div>
                            <h1>$newName</h1>
                            <p>Type: $newType</p>
                            <p><a href='$newMagnet' class='btn btn-primary'>Download</a></p>
                        </div>
                    </div>
                    <div class='torrent-content'>
                        <h3>Description</h3>
                        <p>$newDescription</p>
                    </div>
                </div>
            </body>
            </html>";

            // Write the index page to the folder
            file_put_contents($torrentFolder . '/index.html', $indexContent);

            break;
        }
    }

    // If the torrent was not found, show an error
    if (!$found) {
        echo "Torrent not found.";
        exit;
    }

    // Write updated data back to torrents.json
    file_put_contents($torrentFile, json_encode($torrentData, JSON_PRETTY_PRINT));

    // Redirect or show success message
    echo "Torrent updated successfully!";
    header('Location: index.php'); // Uncomment to redirect after editing
}
?>
