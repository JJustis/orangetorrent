<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyTorrentBay</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
            padding-top: 56px;
        }

        /* Logo Area */
        .logo {
            text-align: center;
            margin-top: 20px;
        }
        .logo img {
            max-width: 150px;
        }

        /* Paneling for the Page */
        .page-panel {
            background-color: #ffffff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            margin: 20px;
        }

        /* Top Panels */
        .top-panels {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            margin-top: 20px;
            flex-wrap: wrap;
        }
        .panel {
            background-color: #007bff;
            color: white;
            padding: 20px;
            border-radius: 10px;
            width: 100%;
            max-width: 32%;
        }
        .panel form input, .panel form textarea, .panel form button {
            width: 100%;
            margin-top: 10px;
        }

        /* Stylish Torrent Listing */
        .torrent-listing {
            background-color: white;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            transition: transform 0.2s ease;
        }
        .torrent-listing:hover {
            transform: scale(1.02);
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.15);
        }
        .torrent-header {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .torrent-header img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
        }
        .torrent-header h4 {
            margin: 0;
            font-size: 1.1em;
            color: #333;
        }
        .torrent-meta {
            font-size: 0.9em;
            color: #777;
        }
        .torrent-content {
            margin-left: auto;
        }
        .torrent-content a {
            color: white;
            text-decoration: none;
        }
        .torrent-content a:hover {
            text-decoration: underline;
        }

        .footer {
            background-color: #343a40;
            color: white;
            padding: 20px;
            text-align: center;
            margin-top: 50px;
        }
    </style>
</head>
<body>

<?php session_start(); ?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">OrangeTorrents</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <?php if (isset($_SESSION['username'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="users/<?php echo $_SESSION['username']; ?>/index.php">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                <?php else: ?>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- Logo Area -->
<div class="container logo">
    <img src="path-to-your-logo.png" alt="MyTorrentBay Logo">
</div>

<!-- Page Content Wrapped in Panels -->
<div class="container page-panel">
    
    <!-- Three Panels at the Top -->
    <div class="top-panels">
        <!-- Submit a Torrent Panel -->
        <div class="panel">
            <h5>Submit a Torrent</h5>
            <form action="post.php" method="post" enctype="multipart/form-data">
                <input type="text" class="form-control" name="name" placeholder="File Name" required>
                <input type="text" class="form-control" name="type" placeholder="Type (e.g., Audio, Video)" required>
                <input type="text" class="form-control" name="magnet" placeholder="Magnet Link" required>
                <textarea class="form-control" name="description" placeholder="Explain the uses, post licenses, and verification files" rows="4"></textarea>

                <!-- Torrent Image Upload -->
                <label for="image">Torrent Image (Optional):</label>
                <input type="file" class="form-control" name="image" accept="image/*">

                <!-- Signature File Upload -->
                <label for="signature">Signature File (Optional):</label>
                <input type="file" class="form-control" name="signature" accept=".sig">

                <!-- DSA Verification Link -->
                <label for="dsa-link">DSA Verification Site Link:</label>
                <input type="url" class="form-control" name="dsa-link" placeholder="https://example.com/dsa-verification" required>

                <button type="submit" class="btn btn-light mt-2">Submit Torrent</button>
            </form>
        </div>

        <!-- Edit a Torrent Panel -->
        <div class="panel">
            <h5>Edit a Torrent</h5>
            <form action="edit.php" method="post" enctype="multipart/form-data">
                <input type="text" class="form-control" name="torrentID" placeholder="Torrent ID" required>
                <input type="text" class="form-control" name="newName" placeholder="New File Name" required>
                <input type="text" class="form-control" name="newType" placeholder="New Type" required>
                <input type="text" class="form-control" name="newMagnet" placeholder="New Magnet Link" required>
                <textarea class="form-control" name="newDescription" placeholder="Update Description" rows="4"></textarea>
                <label for="image">Update Torrent Image (Optional):</label>
                <input type="file" class="form-control" name="newImage" accept="image/*">
                <button type="submit" class="btn btn-light mt-2">Edit Torrent</button>
            </form>
        </div>

        <!-- Login/Register Panel -->
        <div class="panel">
            <h5>Login / Register</h5>
            <form action="login.php" method="post" id="loginForm">
                <input type="text" class="form-control" name="username" placeholder="Username" required>
                <input type="password" class="form-control" name="password" placeholder="Password" required>
                <button type="submit" class="btn btn-light mt-2">Login</button>
            </form>

            <p class="mt-2">New here? <a href="#" id="toggleRegister">Register</a></p>
            <form action="register.php" method="post" id="registerForm" style="display:none;">
                <input type="text" class="form-control" name="newUsername" placeholder="New Username" required>
                <input type="email" class="form-control" name="email" placeholder="Email" required>
                <input type="password" class="form-control" name="newPassword" placeholder="Password" required>
                <button type="submit" class="btn btn-light mt-2">Register</button>
            </form>
        </div>
    </div>

    <!-- Stylish Torrent Listings -->
    <div class="container mt-4">
        <div id="torrentContainer">
            <!-- Stylish torrent submissions will be dynamically populated here -->
        </div>
    </div>
</div>

<!-- Footer -->
<div class="footer">
    <p>&copy; 2024 OrangeTorrents</p>
</div>

<script>
// Toggle between login and register forms
document.getElementById('toggleRegister').addEventListener('click', function(e) {
    e.preventDefault();
    document.getElementById('loginForm').style.display = 'none';
    document.getElementById('registerForm').style.display = 'block';
});

// Fetch and display torrent listings
fetch('torrents.json')
    .then(response => response.json())
    .then(data => {
        const container = document.getElementById('torrentContainer');
        data.forEach(item => {
            const listing = document.createElement('div');
            listing.classList.add('torrent-listing');
            listing.innerHTML = `
                <div class="torrent-header">
                    <img src="${item.image ? item.image : 'https://betahut.bounceme.net/dashboard/uploads/6702e88c9a9a8.png'}" alt="Torrent Image">
                    <div>
                        <h4>${item.name}</h4>
                        <div class="torrent-meta">Type: ${item.type} | Posted: ${item.date}</div>
                    </div>
                </div>
                <div class="torrent-content">
                    <p>${item.description}</p>
                    <a href="torrents/${item.folder}/index.php" class="btn btn-success">View Details</a>
                </div>
            `;
            container.appendChild(listing);
        });
    })
    .catch(error => console.log('Error:', error));
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
