<?php
// We need to use sessions, so you should always start sessions using the below code.
session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
	header('Location: index.php');
	exit;
}
if ($_SESSION['type'] != 'player') {
	header('Location: index.php');
	exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Today's Shots - Shotstreak</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="tailwindextras.js"></script>

    <link rel="stylesheet" href="main.css">
    <link rel="icon" type="image/png" href="assets/favicon-48x48.png" sizes="48x48" />
    <link rel="icon" type="image/svg+xml" href="assets/favicon.svg" />
    <link rel="shortcut icon" href="assets/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="assets/apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="Shotstreak" />
    <link rel="manifest" href="assets/site.webmanifest" />
    <!-- Alpine Plugins -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/persist@3.x.x/dist/cdn.min.js"></script>
    <!-- Alpine Core -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>
<body class="bg-lightgray dark:bg-almostblack">
      <!-- Navbar -->
      <nav class="bg-white dark:bg-darkslate shadow-md py-4">
        <div class="container mx-auto flex justify-between items-center px-6">
            <a href="#" class="text-2xl font-bold text-coral">Shotstreak</a>
            <div class="flex items-center gap-2">
                <button id="theme-toggle"><img class="size-5 dark:hidden" src="assets/dark.svg" alt="dark"><img class="size-5 hidden dark:block" src="assets/light.svg" alt="dark"></button>
                
                <a href="p_profile.php" class="text-almostblack dark:text-lightgray md:hover:text-coral">Profile</a>
                <a href="logout.php" class="text-almostblack dark:text-lightgray md:hover:text-coral">Logout</a>
            </div>
        </div>
    </nav>
        <!-- Registration Form Container -->
        <div class="flex flex-col-reverse items-center gap-12 justify-center min-h-screen">
            <div class="bg-white dark:bg-darkslate p-8 mt-6 rounded-lg shadow-lg max-w-md w-full">
                <!-- Logo -->
                <div class="text-center mb-6">
                <span class="text-coral text-3xl font-bold">OR</span>
                    <h1 class="text-2xl font-bold dark:text-lightgray text-almostblack mt-4">Enter manually:</h1>
                </div>
    
                <!-- Registration Form -->
                <form action="p_input_daily.php" method="POST" class="flex flex-col justify-center gap-4">
                    <div>
                        <label for="shotstaken" class="block dark:text-lightgray text-lg text-gray-700">How many shots did you take?</label>
                        <input type="number" name="shotstaken" id="shotstaken" placeholder="100" class="mt-1 p-2 w-10/12 mx-auto border dark:bg-darkslate dark:text-lightgray rounded-md focus-visible:outline-coral" required min="1" max="999">
                    </div>
                    <div>
                        <label for="shotsmade" class="block dark:text-lightgray text-lg  text-gray-700">How many shots did you make?</label>
                        <input type="number" name="shotsmade" id="shotsmade" placeholder="61" class="mt-1 p-2 w-10/12 dark:bg-darkslate dark:text-lightgray mx-auto border rounded-md focus-visible:outline-coral" required min="1" max="999">
                    </div>
    
    
                    <!-- Submit Button -->
                    <button type="submit" class="w-full bg-coral text-white py-2 rounded-md font-semibold hover:bg-coralhov transition-colors">Submit</button>
                </form>
                

                <div class="text-center mt-4">
                    <p class="text-sm text-gray-600"> <a href="player_dashboard.php" class="text-coral font-semibold">Back to Dashboard</a></p>
                </div>
            </div>


            <div class="bg-white mt-4 dark:bg-darkslate p-8 rounded-lg shadow-lg max-w-md w-full">
                <!-- Logo -->
                <div class="text-center mb-6">
                <img src="assets/isoLogo.svg" alt="Shotstreak Logo" class="mx-auto h-16">
                    <h1 class="text-2xl dark:text-lightgray font-bold text-almostblack mt-4">Shot Counter</h1>
                </div>
    

                    <!-- Try Button -->
                    <a href="p_counterbutton.php"><button class="w-full bg-coral dark:text-lightgray text-white py-2 rounded-md font-semibold hover:bg-coralhov transition-colors">Start Counting</button></a>

                


            </div>
        </div>
        <div x-data="{show: $persist(true)}">
            <div x-show="show" x-cloak class="fixed bottom-0 bg-coral p-6 w-full flex justify-between shadow-md">
                <p><b>Please enter accurate information about your shots!</b> This will help you track your progress more effectively and make Shotstreak a better experience for everyone. Honest data, honest results!</p>
                <button @click="show = false">
                    <svg fill="#000000" height="17" width="17px" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                        viewBox="0 0 460.775 460.775" xml:space="preserve">
                        <path d="M285.08,230.397L456.218,59.27c6.076-6.077,6.076-15.911,0-21.986L423.511,4.565c-2.913-2.911-6.866-4.55-10.992-4.55
                        c-4.127,0-8.08,1.639-10.993,4.55l-171.138,171.14L59.25,4.565c-2.913-2.911-6.866-4.55-10.993-4.55
                        c-4.126,0-8.08,1.639-10.992,4.55L4.558,37.284c-6.077,6.075-6.077,15.909,0,21.986l171.138,171.128L4.575,401.505
                        c-6.074,6.077-6.074,15.911,0,21.986l32.709,32.719c2.911,2.911,6.865,4.55,10.992,4.55c4.127,0,8.08-1.639,10.994-4.55
                        l171.117-171.12l171.118,171.12c2.913,2.911,6.866,4.55,10.993,4.55c4.128,0,8.081-1.639,10.992-4.55l32.709-32.719
                        c6.074-6.075,6.074-15.909,0-21.986L285.08,230.397z"/>
                    </svg>
                </button>
            </div>
        </div>
        <footer class="bg-lightgray py-8 text-almostblack dark:text-lightgray dark:bg-almostblack static bottom-0 left-0 w-full">
          <p class="text-sm text-center">© 2024 Shotstreak. All rights reserved.</p>
    </footer>
        <script>
        const themeToggleBtn = document.getElementById('theme-toggle');
        const htmlElement = document.documentElement;

        themeToggleBtn.addEventListener('click', () => {
            if (htmlElement.classList.contains('dark')) {
            htmlElement.classList.remove('dark');
            localStorage.setItem('theme', 'light');
            } else {
            htmlElement.classList.add('dark');
            localStorage.setItem('theme', 'dark');
            }
        });

        // Check local storage for theme preference on page load
        if (localStorage.getItem('theme') === 'dark') {
            htmlElement.classList.add('dark');
        }
    </script>
    </body>
    </html>


