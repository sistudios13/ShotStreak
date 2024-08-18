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
    <title>Today's Shots - ShotStreak</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Nunito:ital,wght@0,200..1000;1,200..1000&family=PT+Sans:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="../tailwindextras.js"></script>
    <link rel="stylesheet" href="main.css">
    <link rel="shortcut icon" href="assets/isoLogo.svg" type="image/x-icon"></head>
    <body class="bg-lightgray">
      <!-- Navbar -->
    <nav class="bg-white shadow-md py-4">
        <div class="container mx-auto flex justify-between items-center px-6">
            <a href="#" class="text-2xl font-bold text-coral">ShotStreak</a>
            <div class="flex items-center space-x-4">
                <a href="profile.php" class="text-almostblack md:hover:text-coral">Profile</a>
                <a href="logout.php" class="text-almostblack md:hover:text-coral">Logout</a>
            </div>
        </div>
    </nav>
        <!-- Registration Form Container -->
        <div class="flex flex-col items-center gap-12 justify-center min-h-screen">
            <div class="bg-white p-8 mt-6 rounded-lg shadow-lg max-w-md w-full">
                <!-- Logo -->
                <div class="text-center mb-6">
                    <img src="assets/isoLogo.svg" alt="ShotStreak Logo" class="mx-auto h-16">
                    <h1 class="text-2xl font-bold text-almostblack mt-4">Today's Shots</h1>
                </div>
    
                <!-- Registration Form -->
                <form action="input_daily.php" method="POST" class="flex flex-col justify-center gap-4">
                    <div>
                    <label for="shotstaken" class="block text-lg text-gray-700">How many shots did you take?</label>
                    <input type="number" name="shotstaken" id="shotstaken" placeholder="100" class="mt-1 p-2 w-10/12 mx-auto border rounded-md focus-visible:outline-coral" required min="1" max="999">
                    </div>
                    <div>
                    <label for="shotsmade" class="block text-lg text-gray-700">How many shots did you make?</label>
                    <input type="number" name="shotsmade" id="shotsmade" placeholder="61" class="mt-1 p-2 w-10/12 mx-auto border rounded-md focus-visible:outline-coral" required min="1" max="999">
                    </div>
    
    
                    <!-- Submit Button -->
                    <button type="submit" class="w-full bg-coral text-white py-2 rounded-md font-semibold hover:bg-coralhov transition-colors">Submit</button>
                </form>
                

                <div class="text-center mt-4">
                    <p class="text-sm text-gray-600"> <a href="home.php" class="text-coral font-semibold">Back to Home</a></p>
                </div>
            </div>


            <div class="bg-white p-8 rounded-lg shadow-lg max-w-md w-full">
                <!-- Logo -->
                <div class="text-center mb-6">
                    <span class="text-coral text-3xl font-bold">OR</span>
                    <h1 class="text-2xl font-bold text-almostblack mt-4">Shot Counter</h1>
                </div>
    

                    <!-- Try Button -->
                    <a href="counterbutton.php"><button class="w-full bg-coral text-white py-2 rounded-md font-semibold hover:bg-coralhov transition-colors">Try it</button></a>

                


            </div>
        </div>
        <footer class="bg-white py-8 text-almostblack">
          <p class="text-sm text-center">© 2024 ShotStreak. All rights reserved.</p>
        </footer>
    </body>
    </html>


