<?php
// We need to use sessions, so you should always start sessions using the below code.
session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
	header('Location: index.html');
	exit;
}
if ($_SESSION['type'] != 'user') {
	header('Location: index.html');
	exit;
}
$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'shotstreak';
$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if (mysqli_connect_errno()) {
	exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}
// We don't have the password or email info stored in sessions, so instead, we can get the results from the database.
$stmt = $con->prepare('SELECT password, email FROM accounts WHERE id = ?');
// In this case we can use the account ID to get the account info.
$stmt->bind_param('i', $_SESSION['id']);
$stmt->execute();
$stmt->bind_result($password, $email);
$stmt->fetch();
$stmt->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - ShotStreak</title>
    
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="../tailwindextras.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="main.css">
    <link rel="shortcut icon" href="assets/isoLogo.svg" type="image/x-icon">
</head>
<body class="bg-lightgray dark:bg-almostblack h-fit">

    <!-- Navbar -->
    <nav class="bg-white dark:bg-darkslate shadow-md py-4">
        <div class="container mx-auto flex justify-between items-center px-6">
            <a href="#" class="text-2xl font-bold text-coral">ShotStreak</a>
            <div class="flex items-center gap-2">
                <button id="theme-toggle"><img class="size-5 dark:hidden" src="assets/dark.svg" alt="dark"><img class="size-5 hidden dark:block" src="assets/light.svg" alt="dark"></button>
                
                <a href="home.php" class="text-almostblack dark:text-lightgray md:hover:text-coral">Home</a>
                <a href="logout.php" class="text-almostblack dark:text-lightgray md:hover:text-coral">Logout</a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mx-auto px-6 py-8">


        <!-- Dashboard Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

            <!-- Daily Summary Card -->
            <div class="bg-white dark:bg-darkslate p-6 rounded-lg shadow-md flex flex-col gap-4">
                <h3 class="text-xl font-semibold text-almostblack dark:text-lightgray  mb-4">Your Information</h3>
                <div class="flex flex-col items-start justify-between gap-4 ">
                    <div>
                        <p class="text-lg font-bold text-coral">Username:</p>
                        <p class="text-almostblack dark:text-lightgray "><?=htmlspecialchars($_SESSION['name'], ENT_QUOTES)?></p>
                    </div>
                    <div>
                        <p class="text-lg font-bold text-coral">Email:</p>
                        <p class="text-almostblack dark:text-lightgray "><?=htmlspecialchars($email, ENT_QUOTES)?></p>
                    </div>
                </div>
            </div>

            <!-- Progress Chart Card -->
            <div class="bg-white dark:bg-darkslate p-6 rounded-lg shadow-md" x-data="{change: false}">
                <h3 class="text-lg font-semibold text-almostblack  dark:text-lightgray mb-4">Edit Account</h3>
                <div class="flex flex-col gap-3" @click.away="change = false">
                        <a @click="change = !change" class="text-lg text-coral font-bold mb-3 cursor-pointer">Change Password</a>
                        <form class="flex flex-col gap-3" action="change.php" method="POST" id="registerForm" x-show="change" x-collapse >
                            <!-- Password Input -->
                    <div>
                        <label for="newpassword" class="block text-md font-medium text-gray-700 dark:text-lightgray ">New Password</label>
                        <input autofocus type="password" id="password" name="newpassword" minlength="5" maxlength="20" class="mt-1 p-2 w-full border  dark:bg-lightgray rounded-md focus-visible:outline-coral" required>
                    </div>
    
                    <!-- Confirm Password Input -->
                    <div>
                        <label for="confirm-password" class="block text-md font-medium text-gray-700 dark:text-lightgray ">Confirm New Password</label>
                        <input type="password" id="confirm-password" name="confirm-password" class="mt-1 p-2 w-full border dark:bg-lightgray rounded-md focus-visible:outline-coral" required>
                    </div>
    
                    <!-- Submit Button -->
                    <button type="submit" class="w-full md:hover:bg-coralhov bg-coral dark:text-lightgray  text-white py-2 rounded-md font-semibold hover:bg-coral-red-light transition-colors">Change Password</button>
                        </form>
                    </div>
            </div>

            
    </div>
    </div>
    <footer class="bg-white py-8 text-almostblack dark:text-lightgray dark:bg-almostblack static bottom-0 left-0 w-full">
          <p class="text-sm text-center">© 2024 ShotStreak. All rights reserved.</p>
    </footer>
    <script src="confirmpass.js"></script>
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