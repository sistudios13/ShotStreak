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


$user_id = $_SESSION['id'];
$coach_name = $_SESSION['name'];
$email = $_SESSION['email'];

$tmn = $con->prepare('SELECT team_name, coach_id FROM coaches WHERE email = ?');
$tmn->bind_param('s', $email);
$tmn->execute();
$tmn->bind_result($team_name, $coach_id);

$playersquery = "
    SELECT 
    p.id,
    p.player_name,
    SUM(s.shots_made) AS total_shots_made,
    SUM(s.shots_taken) AS total_shots_taken,
    (SUM(s.shots_made) / SUM(s.shots_taken)) * 100 AS shooting_percentage
FROM 
    players p
JOIN 
    shots s ON p.id = s.player_id
WHERE 
    coach_id = ?
";

$pl = $con->prepare($playersquery);
$pl->bind_param('s', $email);
$pl->execute();
$res = $pl->get_result();


$players = [];
if ($res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        $players[] = $row;
    }
} else {
    echo "No leaderboard data available.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - ShotStreak</title>
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
                
                <a href="profile.php" class="text-almostblack dark:text-lightgray md:hover:text-coral">Profile</a>
                <a href="logout.php" class="text-almostblack dark:text-lightgray md:hover:text-coral">Logout</a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mx-auto px-6 py-8">
        <!-- Welcome Banner -->
        <div class="bg-coral text-white dark:text-lightgray rounded-lg p-6 mb-6">
            <h2 class="text-xl font-bold">Welcome back, <?php echo htmlspecialchars($coach_name); ?>!</h2>
            <p class="mt-2">Welcome to your coach dashboard</p>
        </div>
    </div>

    <!-- Players -->
    <div class="bg-white dark:bg-darkslate p-6 rounded-lg shadow-md flex flex-col gap-4">
                <h3 class="text-lg font-semibold text-almostblack dark:text-lightgray mb-4">Your Players:</h3>
                <div class="flex flex-col items-start gap-4 md:flex-row md:gap-0">
                <?php if (!empty($players)): ?>
                        <?php foreach ($players as $index => $user): ?>
                            <div class="w-full bg-lightgray p-4 rounded-lg">
                                <div class="flex justify-between ">
                                    <h2 class="text-md font-bold text-almostblack dark:text-lightgray mb-4">Simon Papp</h2>
                                    <a href="" class="text-coral underline underline-offset-4 decoration-coral">See More</a>
                                </div>
                                <div>
                                    <span>Shooting Percentage: 67%</span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <?php endif; ?>




                </div>
                
                <div class="flex flex-row justify-between">
                    <a href="shotgoal.php"><button class="mt-1 text-white p-2 w-fit mx-auto border dark:border-darkslate bg-coral rounded-md md:hover:bg-coralhov">Change Goal</button></a>
                    <a href="dailyshots.php"><button class="mt-1 text-white p-2 w-fit mx-auto border dark:border-darkslate bg-coral rounded-md md:hover:bg-coralhov">Input Today's Shots</button></a>

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