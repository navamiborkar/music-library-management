<?php
include 'connection.php'; // Ensure this file connects to your database

// Fetch all genres
$genres_query = "SELECT * FROM categories";
$genres_result = $conn->query($genres_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Music List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #2d0a4d, #4a0a5e, #690b6f);
            background-attachment: fixed;
            color: #fff;
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 0;
            position: relative;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Create floating bokeh effects */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at 70% 20%, rgba(255, 215, 0, 0.15) 5px, transparent 50px),
                        radial-gradient(circle at 20% 40%, rgba(255, 215, 0, 0.1) 8px, transparent 60px),
                        radial-gradient(circle at 90% 60%, rgba(255, 215, 0, 0.12) 10px, transparent 70px),
                        radial-gradient(circle at 40% 80%, rgba(255, 215, 0, 0.08) 6px, transparent 40px);
            pointer-events: none;
            z-index: 1;
        }

        .header-container {
            width: 100%;
            display: flex;
            justify-content: center;
            position: relative;
            padding: 20px;
            box-sizing: border-box;
            z-index: 2;
        }

        .create-playlist-btn {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            background-color: rgba(255, 255, 255, 0.1);
            color: #FFD700;
            border: 2px solid rgba(255, 215, 0, 0.5);
            padding: 10px 20px;
            border-radius: 25px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s ease;
            text-decoration: none;
            backdrop-filter: blur(5px);
        }

        .create-playlist-btn:hover {
            background-color: rgba(255, 215, 0, 0.2);
            box-shadow: 0 0 15px rgba(255, 215, 0, 0.3);
            transform: translateY(-50%) scale(1.05);
        }

        h1 {
            font-size: 2.5em;
            margin: 0;
            color: #fff;
            text-shadow: 0 0 10px rgba(255, 255, 255, 0.3);
        }

        .genre {
            margin-top: 20px;
            padding: 15px;
            background-color: rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            width: 80%;
            max-width: 600px;
            text-align: center;
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            z-index: 2;
        }

        .genre h2 {
            color: #FFD700;
            margin-bottom: 15px;
            text-shadow: 0 0 8px rgba(255, 215, 0, 0.3);
        }

        .track-list {
            list-style: none;
            padding: 0;
            margin-top: 10px;
        }

        .track-list li {
            padding: 12px;
            background-color: rgba(255, 255, 255, 0.1);
            margin: 8px 0;
            border-radius: 8px;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .track-list li:hover {
            background-color: rgba(255, 215, 0, 0.1);
            transform: translateX(5px);
            box-shadow: 0 0 10px rgba(255, 215, 0, 0.2);
        }

        @media (max-width: 768px) {
            .genre {
                width: 90%;
                margin: 15px auto;
            }

            h1 {
                font-size: 2em;
                text-align: center;
            }

            .create-playlist-btn {
                position: relative;
                display: block;
                margin: 20px auto;
                transform: none;
                top: auto;
                right: auto;
            }

            .header-container {
                flex-direction: column;
                align-items: center;
                padding: 15px;
            }
        }
    </style>
</head>
<body>

<div class="header-container">
    <h1>Explore Music by Genre</h1>
    <a href="playlist.php" class="create-playlist-btn">Create Playlist</a>
</div>

<?php
// Loop through each genre and fetch associated tracks
while ($genre = $genres_result->fetch_assoc()) {
    echo "<div class='genre'>";
    echo "<h2>" . htmlspecialchars($genre['category_name']) . "</h2>";

    // Fetch tracks for the current genre
    $tracks_query = $conn->prepare("SELECT track_name, artist_name FROM music_tracks WHERE category_id = ?");
    $tracks_query->bind_param("i", $genre['category_id']);
    $tracks_query->execute();
    $tracks_result = $tracks_query->get_result();

    // Check if there are tracks in this genre
    if ($tracks_result->num_rows > 0) {
        echo "<ul class='track-list'>";
        while ($track = $tracks_result->fetch_assoc()) {
            echo "<li>" . htmlspecialchars($track['track_name']) . " by " . 
                 htmlspecialchars($track['artist_name']) . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>No tracks available in this genre.</p>";
    }
    
    echo "</div>";
    
    // Free the result set
    $tracks_query->close();
}

// Close the database connection
$conn->close();
?>

</body>
</html>