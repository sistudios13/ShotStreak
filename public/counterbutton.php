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
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="../tailwindextras.js"></script>
    <link rel="stylesheet" href="main.css">
    <link rel="shortcut icon" href="assets/isoLogo.svg" type="image/x-icon">
</head>
<body class="bg-lightgray h-screen">
<!-- Navbar -->
    <nav class="bg-white shadow-md py-4">
        <div class="container mx-auto flex justify-between items-center px-6">
            <a href="#" class="text-2xl font-bold text-coral">ShotStreak</a>
            <div class="flex items-center space-x-4">
                <a href="profile.php" class="text-almostblack md:hover:text-coral">Home</a>
                <a href="logout.php" class="text-almostblack md:hover:text-coral">Logout</a>
            </div>
        </div>
    </nav>    
    <div x-data="{show : false}" class="flex flex-col bg-white justify-center mt-4 gap-4 items-center">
        <h1 class="font-bold pt-4 text-coral text-2xl">How It Works</h1>
        <span @click="show = !show " class="pb-4">Show</span>
        <ul x-show="show" x-collapse class="w-11/12 text-lg flex flex-col py-4 gap-2">
            <li><b>1. </b>Take a shot</li>
            <li><b>2. </b>If you miss, press the missed shot button. <br> If you make the shot, press the made shot button.</li>
            <li><b>3. </b>Your score will be kept and your shooting percentage will be automatically calculated</li>
            <li><b>4. </b>When You're done, press the submit button. It will automatically submit your shot data</li>
        </ul>
    </div>
<div class="bg-white p-8 rounded-lg shadow-md mt-6 mx-auto w-11/12 max-w-md text-center">
        <h1 class="text-3xl font-bold text-coral mb-6">Shot Counter</h1>

        <form action="input_daily.php" method="POST">
            <div class="flex justify-center gap-3">

            
            

            <div class="mb-6">
                <h2 class="text-xl font-semibold">Shots Made</h2>
                <p id="shotsMade" class="text-2xl font-bold">0</p>
                <input type="hidden" id="shotsMadeInput" name="shotsmade" value="0">
                
            </div>
            <div class="mb-4">
                <h2 class="text-xl font-semibold">Shots Taken</h2>
                <p id="shotsTaken" class="text-2xl font-bold">0</p>
                <input type="hidden" id="shotsTakenInput" name="shotstaken" value="0">
                
            </div>
            </div>
            <div>
                <h2 class="text-xl font-semibold">Shooting Percentage</h2>
                <p id="shootingPercentage" class="text-2xl font-bold">0%</p>
            </div>

            <button type="button" onclick="incrementShotsTaken()" class="bg-coral text-white text-xl font-bold size-32 px-4 py-2 rounded mt-2 active:bg-golden">
                    Missed <br>Shot
            </button>

            <button type="button" onclick="incrementShotsMade()" class="bg-coral text-white text-xl font-bold size-32 px-4 py-2 rounded mt-2 active:bg-golden">
                    Made <br> Shot
            </button>

            <div>
            <button type="button" onclick="resetCounts()" class="bg-almostblack text-white px-4 py-2 rounded mt-4 ">
                Reset
            </button>

            <button type="submit" class="bg-golden text-dark-slate px-4 py-2 rounded mt-4 hover:bg-coral-red">
                Submit
            </button>
            </div>
        </form>
        <div class="text-center mt-4">
                    <p class="text-sm text-gray-600"> <a href="home.php" class="text-coral font-semibold">Back to Home</a></p>
                </div>
    </div>
    <footer class="bg-white mt-12 py-8 text-almostblack md:absolute md:w-full md: bottom-0">
          <p class="text-sm text-center">© 2024 ShotStreak. All rights reserved.</p>
        </footer>
    <script>
        let shotsTaken = 0;
        let shotsMade = 0;

        function incrementShotsTaken() {
            shotsTaken++;
            updateDisplay();
        }

        function incrementShotsMade() {
                shotsMade++;
                shotsTaken++;
                updateDisplay();
        }

        function updateDisplay() {
            document.getElementById('shotsTaken').innerText = shotsTaken;
            document.getElementById('shotsMade').innerText = shotsMade;
            document.getElementById('shotsTakenInput').value = shotsTaken;
            document.getElementById('shotsMadeInput').value = shotsMade;

            const percentage = shotsTaken > 0 ? ((shotsMade / shotsTaken) * 100).toFixed(2) : 0;
            document.getElementById('shootingPercentage').innerText = `${percentage}%`;
        }

        function resetCounts() {
            shotsTaken = 0;
            shotsMade = 0;
            updateDisplay();
        }
    </script>
</body>
</html>
