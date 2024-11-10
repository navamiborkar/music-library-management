<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <style>
        /* Add styling for a clean and inviting look */
        body {
            background: linear-gradient(to top, #1d2671, #c33764);
            color: #fff;
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            text-align: center;
        }

        h1 {
            font-size: 2.5em;
            margin: 0;
        }

        p {
            font-size: 1.5em;
            margin: 20px 0;
        }

        .btn {
            background-color: #4CAF50; /* Green background */
            color: white;
            padding: 15px 30px;
            font-size: 1em;
            text-decoration: none;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <h1>Login Successful!!!</h1>
    <p>Let's dive into the world of music!</p>
    <button class="btn" onclick="window.location.href='lists.php'">View the Music List</button>
</body>
</html>
