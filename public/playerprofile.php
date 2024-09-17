<?php
// We need to use sessions, so you should always start sessions using the below code.
session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
	header('Location: index.html');
	exit;
}

if ($_SESSION['type'] != 'coach') {
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



$player_id = $_GET['player_id'];
$user_id = $_SESSION['id'];
$coach_name = $_SESSION['name'];
$email = $_SESSION['email'];
$coach_id = $_SESSION['coach_id'];

$stmt = $con->prepare('SELECT coach_id FROM players WHERE id = ?');
$stmt->bind_param( 's', $player_id);
$stmt->execute();
$fetchid = $stmt->get_result();
$fetched_coach = $fetchid->fetch_assoc();

if ($fetched_coach['coach_id'] != $coach_id) {
    header('Location: coach_dashboard.php');
}

//Get Player Data

$p_sql = "SELECT player_name, email
FROM players 
WHERE id = ?";

$stmt = $con->prepare($p_sql);
$stmt->bind_param("i", $player_id);
$stmt->execute();
$p_result = $stmt->get_result();
$player_data = $p_result->fetch_assoc();

$player_name = $player_data['player_name'];
$player_email = $player_data['email'];

//Get SHooting Data

$s_sql = "SELECT SUM(shots_made) as total_shots_made, SUM(shots_taken) as total_shots_taken 
FROM shots 
WHERE player_id = ?";

$stmt = $con->prepare($s_sql);
$stmt->bind_param("i", $player_id);
$stmt->execute();
$s_result = $stmt->get_result();
$shot_data = $s_result->fetch_assoc();

// Calculate shooting percentage
$shots_made = $shot_data['total_shots_made'];
$shots_taken = $shot_data['total_shots_taken'];

if ($shots_taken > 0) {
$shooting_percentage = ($shots_made / $shots_taken) * 100;
} else {
$shooting_percentage = 0;
}


//Ntt done
$sql_stats = "SELECT SUM(shots.shots_made) AS total_shots, 
			  SUM(shots.shots_taken) AS total_taken,
               
              SUM(IF(shots.shots_taken >= user_goals.shots_goal, 1, 0))  AS days_count,
              SUM(IF(shots.shots_taken >= user_goals.shots_goal, 1, 0)) / COUNT(DISTINCT user_shots.shot_date) * 100 AS goal_achievement_rate 
              FROM shots 
              JOIN user_goals ON user_shots.user_id = user_goals.user_id
              WHERE user_shots.user_id = ?";
$stmt = $conn->prepare($sql_stats);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result_stats = $stmt->get_result();
$stats_data = $result_stats->fetch_assoc();


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php echo("<title>".$player_name."'s Profile - Shotstreak</title>")?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Nunito:ital,wght@0,200..1000;1,200..1000&family=PT+Sans:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="../tailwindextras.js"></script>
    <link rel="stylesheet" href="main.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="shortcut icon" href="assets/isoLogo.svg" type="image/x-icon">
</head>
<body class="bg-lightgray dark:bg-almostblack min-h-screen">

    <!-- Navbar -->
    <nav class="bg-white dark:bg-darkslate shadow-md py-4">
        <div class="container mx-auto flex justify-between items-center px-6">
            <a href="#" class="text-2xl font-bold text-coral">ShotStreak</a>
            <div class="flex items-center gap-2">
                <button id="theme-toggle"><img class="size-5 dark:hidden" src="assets/dark.svg" alt="dark"><img class="size-5 hidden dark:block" src="assets/light.svg" alt="dark"></button>
                
                <a href="coach_dashboard.php" class="text-almostblack dark:text-lightgray md:hover:text-coral">Dashboard</a>
                <a href="logout.php" class="text-almostblack dark:text-lightgray md:hover:text-coral">Logout</a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mx-auto px-6 py-8">
        <!-- Welcome Banner -->
        <div class="bg-coral text-white dark:text-lightgray rounded-lg p-6 mb-6">
            <h2 class="text-xl font-bold"><?php echo($player_name."'s Profile")?></h2>

        </div>
    </div>

    
    <div class="container mx-auto px-6 py-8">
        <div class="bg-white dark:bg-darkslate p-6 rounded-lg shadow-md flex flex-col gap-4">
            <!-- Quick Stats Card -->
                <h3 class="text-lg font-semibold text-almostblack dark:text-lightgray mb-4">Stats</h3>
                <ul class="space-y-2">
            <li class="flex justify-between text-almostblack dark:text-lightgray">
                <span>Total Shots Made:</span>
                <span class="font-semibold text-dark-gray"><?php echo $stats_data['total_shots']; ?></span>
            </li>
			<li class="flex justify-between text-almostblack dark:text-lightgray">
                <span>Total Shots Taken:</span>
                <span class="font-semibold text-dark-gray"><?php echo $stats_data['total_taken']; ?></span>
            </li>
            <li class="flex justify-between text-almostblack dark:text-lightgray">
                <span>Best Shooting Day:</span>
                <span class="font-semibold text-dark-gray"><?php echo round($best_day, 0) ?>% Accuracy</span>
            </li>
            <li class="flex justify-between text-almostblack dark:text-lightgray">
                <span>Goal Reached:</span>
                <span class="font-semibold text-dark-gray"><?php echo $stats_data['days_count']; ?> Days</span>
            </li>
            <li class="flex justify-between text-almostblack dark:text-lightgray">
                <span>Goal Achievement Rate:</span>
                <span class="font-semibold text-dark-gray"><?php echo round($stats_data['goal_achievement_rate'], 0); ?>%</span>
            </li>
        </ul>
        </div>
    </div>


        
    <footer class="bg-white py-8 text-almostblack dark:text-lightgray dark:bg-almostblack static bottom-0 left-0 w-full">
          <p class="text-sm text-center">© 2024 ShotStreak. All rights reserved.</p>
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