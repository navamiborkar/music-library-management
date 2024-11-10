<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Creative Website</title>
  <link rel="stylesheet" href="welcome_page.css">
</head>
<body>
  <header>
    <div class="navbar">
      
    </div>
    <div class="hero">
      <div class="controls">
        <div class="typewriter">
          <h1>Play, Pause, Repeat.</h1>
          <!-- Added ID to the button -->
          <button id="enterButton">Enter</button>
        </div>
      </div>
    </div>
  </header>

  <script src="welcome_page.js"></script>
  <script>
    // JavaScript to handle the button click event
    document.getElementById('enterButton').addEventListener('click', function() {
      window.location.href = 'index.php'; // Redirect to login.php
    });
  </script>
</body>
</html>
