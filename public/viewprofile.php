<?php
// Start the session and connect to the database
// We need to use sessions, so you should always start sessions using the below code.
session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
	header('Location: index.html');
	exit;
}
$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'shotstreak';
$conn = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if (mysqli_connect_errno()) {
	exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}
// Get the user ID from the URL
$user_id = $_GET['user_id'];

// Fetch user data from the database
$query = "SELECT username FROM accounts WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();




$sql_stats = "SELECT SUM(user_shots.shots_made) AS total_shots, 
			  SUM(user_shots.shots_taken) AS total_taken,
               
              SUM(IF(user_shots.shots_taken >= user_goals.shots_goal, 1, 0))  AS days_count,
              SUM(IF(user_shots.shots_taken >= user_goals.shots_goal, 1, 0)) / COUNT(DISTINCT user_shots.shot_date) * 100 AS goal_achievement_rate 
              FROM user_shots 
              JOIN user_goals ON user_shots.user_id = user_goals.user_id
              WHERE user_shots.user_id = ?";
$stmt = $conn->prepare($sql_stats);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result_stats = $stmt->get_result();
$stats_data = $result_stats->fetch_assoc();


$shooting_percentage = ($stats_data['total_shots'] / $stats_data['total_taken']) * 100;

// Badges

if ($stats_data['total_taken'] >= 500) {
    $badge1 = true;
}
if ($stats_data['total_taken'] == 0) {
    $badge2 = false;
} else {
    if (($stats_data['total_shots'] / $stats_data['total_taken']) *100 >= 40 ) {
        $badge2 = true;
    }
}

if ($stats_data['total_shots'] >= 1000) {
    $badge3 = true;
}

if ($stats_data['goal_achievement_rate'] == 100 && $stats_data['days_count'] >= 7) {
    $badge4 = true;
}
if ($stats_data['total_taken'] == 0) {
    $badge5 = false;
} else {
if (($stats_data['total_shots'] / $stats_data['total_taken']) *100 >= 70 ) {
    $badge5 = true;
}
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>profile</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Nunito:ital,wght@0,200..1000;1,200..1000&family=PT+Sans:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="../tailwindextras.js"></script>
    <link rel="stylesheet" href="main.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="shortcut icon" href="assets/isoLogo.svg" type="image/x-icon"></head>
</head>
<body class="bg-lightgray dark:bg-almostblack text-almostblack dark:text-lightgray">
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
    <div class="container mx-auto px-6 py-12 ">
        <div class="max-w-4xl mx-auto bg-white dark:bg-darkslate p-8 rounded-lg shadow-lg">
            <div class="flex items-center">
                
                <div>
                    <h2 class="text-3xl font-bold text-coral"><?php echo htmlspecialchars($user['username']); ?></h2>
                </div>
            </div>
            <div class="mt-8">
                <h3 class="text-2xl font-bold">Statistics</h3>
                <div class="mt-4 grid grid-cols-1 gap-4">
                    <div class="bg-lightgray dark:bg-almostblack p-4 rounded-lg text-center shadow-md">
                        <h4 class="text-xl font-bold text-light-gray">Total Shots Taken</h4>
                        <p class="text-2xl font-bold text-golden-yellow"><?php echo $stats_data['total_taken']; ?></p>
                    </div>
                    <div class="bg-lightgray dark:bg-almostblack p-4 rounded-lg text-center shadow-md">
                        <h4 class="text-xl font-bold text-light-gray">Total Shots Made</h4>
                        <p class="text-2xl font-bold text-golden-yellow"><?php echo $stats_data['total_shots']; ?></p>
                    </div>
                    <div class="bg-lightgray dark:bg-almostblack p-4 rounded-lg text-center shadow-md">
                        <h4 class="text-xl font-bold text-light-gray">Shooting Percentage</h4>
                        <p class="text-2xl font-bold text-golden-yellow"><?php echo round($shooting_percentage, 0); ?>%</p>
                    </div>
                </div>
            </div>
            <div class="mt-8 ">
                <h3 class="text-2xl font-bold text-coral-red">Achievements</h3>
                <div class="relative grid grid-cols-6 mt-2 lg:grid-cols-10" x-data="{b1 : false, b2 : false, b3 : false, b4: false, b5 : false}">
            
            <div class=" <?php if(!$badge1) { echo 'hidden'; }?> ">
                <img x-on:click="b1 = !b1" @click.away="b1 = false" class="h-16 cursor-pointer" src="assets/icebreaker.svg" alt="badge1">    
            </div>

            <div class=" <?php if(!$badge2) { echo 'hidden'; }?> ">
                <img x-on:click="b2 = !b2" @click.away="b2 = false" class="h-16 cursor-pointer" src="assets/precision.svg" alt="badge2">
            </div>
            <div class=" <?php if(!$badge3) { echo 'hidden'; }?> ">
                <img x-on:click="b3 = !b3" @click.away="b3 = false" class="h-16 cursor-pointer" src="assets/millenium.svg" alt="badge3">
            </div>
            <div class=" <?php if(!$badge4) { echo 'hidden'; }?> ">
                <img x-on:click="b4 = !b4" @click.away="b4 = false" class="h-16 cursor-pointer" src="assets/crusher.svg" alt="badge4">
            </div>
            <div class=" <?php if(!$badge5) { echo 'hidden'; }?> ">
                <img x-on:click="b5 = !b5" @click.away="b5 = false" class="h-16 cursor-pointer" src="assets/pinpoint.svg" alt="badge5">
            </div>
            <p x-show="b1" class="absolute w-60 bg-white dark:bg-darkslate text-almostblack dark:text-lightgray top-16 p-3 rounded-lg shadow-md">Icebreaker: Take a total of over 500 shots</p>
            <p x-show="b2" class="absolute w-60 bg-white dark:bg-darkslate text-almostblack dark:text-lightgray top-16 p-3 rounded-lg shadow-md">Precision Shooter: Maintain a total average of over 40%</p>
            <p x-show="b3" class="absolute w-60 bg-white dark:bg-darkslate text-almostblack dark:text-lightgray top-16 p-3 rounded-lg shadow-md">Millenium Marksman: Make a total of over 1000 shots</p>
            <p x-show="b4" class="absolute w-60 bg-white dark:bg-darkslate text-almostblack dark:text-lightgray top-16 p-3 rounded-lg shadow-md">Goal Crusher: Beat your goal every day for at least 7 days</p>
            <p x-show="b5" class="absolute w-60 bg-white dark:bg-darkslate text-almostblack dark:text-lightgray top-16 p-3 rounded-lg shadow-md">Pinpoint Shooter: Maintain a total average of over 70%</p>
        </div>
            </div>
        </div>
    </div>
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