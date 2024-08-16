<?php
// We need to use sessions, so you should always start sessions using the below code.
session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
	header('Location: index.html');
	exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shot Goal - ShotStreak</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Nunito:ital,wght@0,200..1000;1,200..1000&family=PT+Sans:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="../tailwindextras.js"></script>
    <link rel="stylesheet" href="main.css">
    <link rel="shortcut icon" href="assets/isoLogo.svg" type="image/x-icon"></head>
    <body class="bg-light-gray">

        <!-- Registration Form Container -->
        <div class="flex items-center justify-center min-h-screen">
            <div class="bg-white p-8 rounded-lg shadow-lg max-w-md w-full">
                <!-- Logo -->
                <div class="text-center mb-6">
                    <img src="assets/isoLogo.svg" alt="ShotStreak Logo" class="mx-auto h-16">
                    <h1 class="text-2xl font-bold text-almostblack mt-4">Daily Shot Goal</h1>
                </div>
    
                <!-- Registration Form -->
                <form action="set_goal.php" method="POST" class="flex flex-col justify-center gap-4">
                    
                    <label for="shotgoal" class="block text-lg text-gray-700">Enter your shot goal:</label>
                    <input type="number" name="shotgoal" id="shotgoal" placeholder="100" class="mt-1 p-2 w-10/12 mx-auto border rounded-md focus-visible:outline-coral" required min="1" max="999">
    
    
    
                    <!-- Submit Button -->
                    <button type="submit" class="w-full bg-coral text-white py-2 rounded-md font-semibold hover:bg-coral-red-light transition-colors">Submit</button>
                </form>
                
                <!-- Already have an account -->
                <div class="text-center mt-4">
                    <p class="text-sm text-gray-600"> <a href="home.php" class="text-coral font-semibold">Back to Home</a></p>
                </div>
            </div>
        </div>
        <footer class="bg-darkslate py-8 text-white">
            <div class="container mx-auto grid grid-cols-1 md:grid-cols-2 gap-4">
              <div class="footer-links flex flex-col justify-center items-center">
                <a href="index.html" class="block mb-2 text-center">Home</a>
                <a href="register.html" class="block mb-2 text-center">Register</a>
                <a href="login.html" class="block mb-2 text-center">Login</a>
                <!-- Add more links -->
              </div> 
              <div class="text-center flex justify-center items-center">
                <p class="text-xs">© 2024 ShotStreak. All rights reserved.</p>
              </div>
            </div>
          </footer>
    </body>
    </html>


