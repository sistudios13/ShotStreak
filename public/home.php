<?php
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


$user_id = $_SESSION['id'];
$user_name = $_SESSION['name'];

// Fetch today's shot goal
$date_today = date('Y-m-d');
$sql_today_goal = "SELECT shots_goal FROM user_goals WHERE user_id = ? ";
$stmt = $conn->prepare($sql_today_goal);
$stmt->bind_param("i", $user_id,);
$stmt->execute();
$result_today_goal = $stmt->get_result();
$today_goal = $result_today_goal->fetch_assoc()['shots_goal'] ?? 0;

// Fetch today's shots made
$sql_today_shots = "SELECT SUM(shots_made) AS total_shots_made FROM user_shots WHERE user_id = ? AND shot_date = ?";
$stmt = $conn->prepare($sql_today_shots);
$stmt->bind_param("is", $user_id, $date_today);
$stmt->execute();
$result_today_shots = $stmt->get_result();
$today_shots_made = $result_today_shots->fetch_assoc()['total_shots_made'] ?? 0;

// Fetch today's shots taken
$sql_today_shots_taken = "SELECT SUM(shots_taken) AS total_shots_taken FROM user_shots WHERE user_id = ? AND shot_date = ?";
$stmt = $conn->prepare($sql_today_shots_taken);
$stmt->bind_param("is", $user_id, $date_today);
$stmt->execute();
$result_today_shots_taken = $stmt->get_result();
$today_shots_taken = $result_today_shots_taken->fetch_assoc()['total_shots_taken'] ?? 0;
// Calculate shots remaining
$shots_remaining = $today_goal - $today_shots_taken;

// Fetch data for the progress chart (last 7 days)
$sql_chart = "SELECT shot_date, shots_made, shots_taken FROM user_shots 
            WHERE user_id = ? 
            ORDER BY shot_date DESC 
            LIMIT 7";
$stmt = $conn->prepare($sql_chart);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result_chart = $stmt->get_result();

$chart_data = [];
while ($row = $result_chart->fetch_assoc()) {
    $chart_data[] = $row;
}

// Best day %
function getBestShootingDay($conn, $user_id) {
    $query = "SELECT shot_date, 
                     (shots_made / shots_taken) * 100 AS shooting_percentage
              FROM user_shots
              WHERE user_id = ? AND shots_taken > 0
              ORDER BY shooting_percentage DESC
              LIMIT 1";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        return $row; // Returns an associative array with 'shot_date' and 'shooting_percentage'
    } else {
        return null; // No valid records found
    }
}
$best_day = getBestShootingDay($conn, $user_id);
// Fetch quick stats
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
    <link rel="shortcut icon" href="assets/isoLogo.svg" type="image/x-icon">
</head>
<body class="bg-light-gray font-sans min-h-screen">

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

    <!-- Main Content -->
    <div class="container mx-auto px-6 py-8">
        <!-- Welcome Banner -->
        <div class="bg-coral text-white rounded-lg p-6 mb-8">
            <h2 class="text-xl font-bold">Welcome back, <?php echo htmlspecialchars($user_name); ?>!</h2>
            <p class="mt-2">Here's your progress for today:</p>
        </div>

        <!-- Dashboard Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

            <!-- Daily Summary Card -->
            <div class="bg-white p-6 rounded-lg shadow-md flex flex-col gap-4">
                <h3 class="text-lg font-semibold text-almostblack mb-4">Today's Goal</h3>
                <div class="flex flex-col items-start justify-between gap-4 md:flex-row md:gap-0">
                    <div>
                        <p class="text-4xl font-bold text-golden"><?php echo $today_goal; ?></p>
                        <p class="text-almostblack">Daily Shot Goal</p>
                    </div>
                    <div>
                        <p class="text-4xl font-bold text-almostblack"><?php echo $today_shots_made; ?></p>
                        <p class="text-almostblack">Shots Made</p>
                    </div>
					<div>
                        <p class="text-4xl font-bold text-coral"><?php echo $today_shots_taken; ?></p>
                        <p class="text-almostblack">Shots Taken</p>
                    </div>
                </div>
                <p class=" text-almostblack"><?php echo $shots_remaining > 0 ? "You need <b class='text-coral'>$shots_remaining</b> more shots to meet your goal!" : "Goal achieved!"; ?></p>
                <div class="flex flex-row justify-between">
                    <a href="shotgoal.php"><button class="mt-1 text-white p-2 w-fit mx-auto border bg-coral rounded-md md:hover:bg-coralhov">Change Goal</button></a>
                    <a href="dailyshots.php"><button class="mt-1 text-white p-2 w-fit mx-auto border bg-coral rounded-md md:hover:bg-coralhov">Input Today's Shots</button></a>

                </div>
                            </div>

            <!-- Progress Chart Card -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-lg font-semibold text-almostblack mb-4">Progress Chart</h3>
                <canvas id="progressChart" width="400" height="200"></canvas>
            </div>

            <!-- Quick Stats Card -->
            <div class="bg-white p-6 rounded-lg shadow-md">
        <h3 class="text-lg font-semibold text-almostblack mb-4">Quick Stats</h3>
        <ul class="space-y-2">
            <li class="flex justify-between text-almostblack">
                <span>Total Shots Made:</span>
                <span class="font-semibold text-dark-gray"><?php echo $stats_data['total_shots']; ?></span>
            </li>
			<li class="flex justify-between text-almostblack">
                <span>Total Shots Taken:</span>
                <span class="font-semibold text-dark-gray"><?php echo $stats_data['total_taken']; ?></span>
            </li>
            <li class="flex justify-between text-almostblack">
                <span>Best Shooting Day:</span>
                <span class="font-semibold text-dark-gray"><?php echo round($best_day['shooting_percentage'], 0) ?>% Accuracy</span>
            </li>
            <li class="flex justify-between text-almostblack">
                <span>Goal Reached:</span>
                <span class="font-semibold text-dark-gray"><?php echo $stats_data['days_count']; ?> Days</span>
            </li>
            <li class="flex justify-between text-almostblack">
                <span>Goal Achievement Rate:</span>
                <span class="font-semibold text-dark-gray"><?php echo round($stats_data['goal_achievement_rate'], 0); ?>%</span>
            </li>
        </ul>
    </div>
    <footer class="bg-white pt-8 text-almostblack static md:py-8 md:absolute bottom-0 left-0 w-full">
          <p class="text-sm text-center">© 2024 ShotStreak. All rights reserved.</p>
    </footer>


    <!-- Chart.js Script -->
    <script>
        const ctx = document.getElementById('progressChart').getContext('2d');
        const progressChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_reverse(array_column($chart_data, 'shot_date'))); ?>,
                datasets: [{
                    label: 'Shooting Accuracy (%)',
                    data: <?php echo json_encode(array_reverse(array_map(function($row) {
                        return ($row['shots_made'] / $row['shots_taken']) * 100;
                    }, $chart_data))); ?>,
                    borderColor: '#FF6F61',
                    backgroundColor: 'rgba(255, 90, 95, 0.2)',
                    borderWidth: 2,
                    fill: true,
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>

</body>
</html>