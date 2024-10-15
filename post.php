<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect the form inputs
    $name = $_POST['name'];
    $type = $_POST['type'];
    $magnet = $_POST['magnet'];
    $description = $_POST['description'];
    $poster = isset($_SESSION['username']) ? $_SESSION['username'] : 'Anonymous';
    $date = date("Y-m-d H:i:s");

    // Default image for torrents
    $defaultImage = 'https://betahut.bounceme.net/dashboard/uploads/6702e88c9a9a8.png';
    $image = $defaultImage;

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imageTmpPath = $_FILES['image']['tmp_name'];
        $imageName = basename($_FILES['image']['name']);
        $uploadDir = '../uploads/';
        $imageFilePath = $uploadDir . $imageName;

        // Move the uploaded file
        if (move_uploaded_file($imageTmpPath, $imageFilePath)) {
            $image = $imageFilePath;
        }
    }

    // Handle signature file upload
    $signatureFile = '';
    if (isset($_FILES['signature']) && $_FILES['signature']['error'] === UPLOAD_ERR_OK) {
        $sigTmpPath = $_FILES['signature']['tmp_name'];
        $sigName = basename($_FILES['signature']['name']);
        $uploadDir = 'uploads/signatures/';
        $sigFilePath = $uploadDir . $sigName;

        // Move the signature file
        if (move_uploaded_file($sigTmpPath, $sigFilePath)) {
            $signatureFile = $sigFilePath;
        }
    }

    // Handle DSA link input
    $dsaLink = $_POST['dsa-link'];

    // Read current torrents from JSON file
    $torrentFile = 'torrents.json';
    $torrentData = file_exists($torrentFile) ? json_decode(file_get_contents($torrentFile), true) : [];

    // Create new torrent entry
$newTorrent = [
    "id" => uniqid(),
    "name" => $name,
    "type" => $type,
    "magnet" => $magnet,
    "description" => $description,
    "poster" => $poster,
    "date" => $date,
    "image" => $image,
    "signature" => $signatureFile,
    "dsa_link" => $dsaLink,
    "folder" => preg_replace('/[^a-zA-Z0-9_-]/', '_', strtolower($name)) // Sanitized folder name
];


    // Add new torrent to the data array
    $torrentData[] = $newTorrent;

    // Write updated data to torrents.json
    file_put_contents($torrentFile, json_encode($torrentData, JSON_PRETTY_PRINT));

    // Create the torrent folder and index page
    $torrentFolder = 'torrents/' . $newTorrent['folder'];
    if (!file_exists($torrentFolder)) {
        mkdir($torrentFolder, 0777, true);
    }

    // Create the index page for the torrent
    $indexContent = '
<?php
session_start();

// Fetch torrent data from torrents.json
$torrentName = basename(__DIR__);
$torrentData = json_decode(file_get_contents("../../torrents.json"), true);
$torrent = null;

foreach ($torrentData as $item) {
    if ($item["folder"] === $torrentName) {
        $torrent = $item;
        break;
    }
}

$isOwner = isset($_SESSION["username"]) && $_SESSION["username"] === $torrent["poster"];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $torrent["name"]; ?> - Torrent</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
            padding-top: 56px;
        }
        .torrent-container {
            max-width: 900px;
            margin: auto;
            padding: 20px;
        }
        .panel {
            background-color: #ffffff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .torrent-header {
            display: flex;
            align-items: center;
        }
        .torrent-header img {
            width: 120px;
            height: 120px;
            border-radius: 10px;
            margin-right: 20px;
        }
        .torrent-header h1 {
            margin: 0;
            font-size: 1.8em;
        }
        .torrent-info p {
            margin: 0;
            font-size: 1.1em;
        }
        .download-btn {
            margin-top: 20px;
        }
        .details-section h3 {
            margin-bottom: 15px;
            font-size: 1.4em;
        }
        .edit-section {
            display: none;
        }
        <?php if ($isOwner): ?>
        .edit-section {
            display: block;
        }
        <?php endif; ?>
    </style>
</head>
<body>

<div class="torrent-container">
    <!-- Torrent Header Panel -->
    <div class="panel">
        <div class="torrent-header">
            <img src="<?php echo $torrent["image"] ? $torrent["image"] : "https://betahut.bounceme.net/dashboard/uploads/6702e88c9a9a8.png"; ?>" alt="<?php echo $torrent["name"]; ?> Image">
            <div>
                <h1><?php echo $torrent["name"]; ?></h1>
                <div class="torrent-info">
                    <p>Type: <?php echo $torrent["type"]; ?></p>
                    <p>Posted by: <?php echo $torrent["poster"]; ?></p>
                    <p>Date: <?php echo $torrent["date"]; ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Download and Verification Panel -->
    <div class="panel">
        <div class="download-btn">
            <a href="<?php echo $torrent["magnet"]; ?>" class="btn btn-primary btn-lg">Download Now</a>
            <a href="<?php echo $torrent["dsa_link"]; ?>" class="btn btn-secondary btn-lg">Verify via DSA</a>
        </div>
    </div>

    <!-- Torrent Description Panel -->
    <div class="panel">
        <div class="details-section">
            <h3>Description</h3>
            <p><?php echo $torrent["description"]; ?></p>
        </div>
    </div>

    <!-- Signature File Panel (if exists) -->
    <?php if ($torrent["signature"]): ?>
    <div class="panel">
        <div class="details-section">
            <h3>Signature File</h3>
            <a href="<?php echo $torrent["signature"]; ?>" class="btn btn-info">Download Signature</a>
        </div>
    </div>
    <?php endif; ?>

    <!-- Edit Torrent Section (for the owner only) -->
    <div class="panel edit-section">
        <h3>Edit Torrent</h3>
        <form action="../../edit.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="torrentID" value="<?php echo $torrent["id"]; ?>">
            <input type="text" class="form-control" name="newName" value="<?php echo $torrent["name"]; ?>" required>
            <input type="text" class="form-control" name="newType" value="<?php echo $torrent["type"]; ?>" required>
            <input type="text" class="form-control" name="newMagnet" value="<?php echo $torrent["magnet"]; ?>" required>
            <textarea class="form-control" name="newDescription" rows="4"><?php echo $torrent["description"]; ?></textarea>
            <label for="newImage">Update Image:</label>
            <input type="file" class="form-control" name="newImage" accept="image/*">
            <button type="submit" class="btn btn-primary mt-2">Update Torrent</button>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
    ';

    // Write the index page to the folder
    file_put_contents($torrentFolder . '/index.php', $indexContent);

    // Redirect the user to the torrent's page
    header('Location: ' . $torrentFolder . '/index.php');
    exit;
}
