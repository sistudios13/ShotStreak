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

// Fetch data for the progress chart (last 14 days)
$asql_chart = "SELECT shot_date, shots_made, shots_taken FROM user_shots 
            WHERE user_id = ? 
            ORDER BY shot_date DESC 
            LIMIT 14";
$astmt = $conn->prepare($asql_chart);
$astmt->bind_param("i", $user_id);
$astmt->execute();
$aresult_chart = $astmt->get_result();

$achart_data = [];
while ($arow = $aresult_chart->fetch_assoc()) {
    $achart_data[] = $arow;
}

// Fetch data for the progress chart (last 90 days)
$bsql_chart = "SELECT shot_date, shots_made, shots_taken FROM user_shots 
            WHERE user_id = ? 
            ORDER BY shot_date DESC 
            LIMIT 90";
$bstmt = $conn->prepare($bsql_chart);
$bstmt->bind_param("i", $user_id);
$bstmt->execute();
$bresult_chart = $bstmt->get_result();

$bchart_data = [];
while ($brow = $bresult_chart->fetch_assoc()) {
    $bchart_data[] = $brow;
}

// Best day %

    $query = "SELECT (shots_made / shots_taken) * 100 AS shooting_percentage
              FROM user_shots
              WHERE user_id = ? AND shots_taken > 0
              ORDER BY shooting_percentage DESC
              LIMIT 1";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $best_day = $result->fetch_assoc()['shooting_percentage'] ?? 0;


// Fetch quick stats
$sql_stats = "SELECT SUM(shots_made) AS total_shots, 
			  SUM(shots_taken) AS total_taken,
               
              SUM(IF(shots_taken >= goal, 1, 0))  AS days_count
              FROM user_shots 
              WHERE user_id = ?";
$stmt = $conn->prepare($sql_stats);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result_stats = $stmt->get_result();
$stats_data = $result_stats->fetch_assoc();


// Badges

$badge1 = false;
$badge2 = false;
$badge3 = false;
$badge4 = false;
$badge5 = false;


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


if ($stats_data['total_taken'] == 0) {
    $badge5 = false;
} else {
if (($stats_data['total_shots'] / $stats_data['total_taken']) *100 >= 70 ) {
    $badge5 = true;
}
}
//Leaderboard


$leaderquery = "
    SELECT 
    u.id,
    u.username,
    SUM(s.shots_made) AS total_shots_made,
    SUM(s.shots_taken) AS total_shots_taken,
    (SUM(s.shots_made) / SUM(s.shots_taken)) * 100 AS shooting_percentage
FROM 
    accounts u
JOIN 
    user_shots s ON u.id = s.user_id
GROUP BY 
    u.id, u.username
HAVING 
    total_shots_taken > 0  -- Ensure users who have taken shots are considered
ORDER BY 
    shooting_percentage DESC
LIMIT 10;
";

$result = $conn->query($leaderquery);

$leaderboard = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $leaderboard[] = $row;
    }
} else {
    echo "No leaderboard data available.";
}

//Streak

// Fetch the user's daily shot records and goal data from the database
$query = "SELECT shots_taken, shot_date, goal FROM user_shots WHERE user_id = ? ORDER BY shot_date DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$streak = 0;
$previous_day = null;

// Check each record to calculate the streak
while ($row = $result->fetch_assoc()) {
    $shots_taken = $row['shots_taken'];
    $shot_date = $row['shot_date'];
    $goal = $row['goal'];
    
    // If the user met their goal on that day
    if ($shots_taken >= $goal) {
        // If this is the first day we're checking
        if ($previous_day === null) {
            $streak++;  // Start the streak
        } else {
            // Check if the previous day is exactly one day before the current day
            $days_diff = (strtotime($previous_day) - strtotime($shot_date)) / (60 * 60 * 24);
            if ($days_diff == 1) {
                $streak++;  // Continue the streak
            } else {
                break;  // Break the streak if there's a gap
            }
        }
        $previous_day = $shot_date;  // Update the last day checked
    } else {
        break;  // End the streak if the goal wasn't met
    }
}

//Streak Badge
if ($streak >= 3) {
    $badge4 = true;
}

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - ShotStreak</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="../tailwindextras.js"></script>
    <link rel="stylesheet" href="main.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="shortcut icon" href="assets/isoLogo.svg" type="image/x-icon">
    <script src="https://cdn.jsdelivr.net/npm/preline@2.4.1/dist/preline.min.js"></script>
    <script>
        var time = 2;
        function atime(number) {
             time = number;
             aupdate()    
        }  
    </script>
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
        <div class="bg-coral text-white dark:text-lightgray rounded-lg p-6">
            
            <h2 class="text-xl font-bold">Welcome back, <?php echo htmlspecialchars($user_name); ?>!</h2>
            
            <div class="flex flex-col-reverse md:flex-row md:justify-between">
                <p class="mt-2">Here's your progress for today:</p>
                <a href="dailyshots.php"><button class=" text-white md:-translate-y-5 font-bold mt-4 p-3 md:px-6 md:py-4 w-fit mx-auto border-2 border-golden md:hover:bg-golden md:hover:text-almostblack transition-colors rounded-md ">Input Today's Shots</button></a>
            </div>
            
            
        </div>



        
        <div>
            <h2 class="text-2xl font-bold dark:text-lightgray py-8">&#x1F525; Streak: <span class="text-coral"><?php echo htmlspecialchars($streak)?></span></h2>
        </div>

        <!-- Dashboard Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

            <!-- Daily Summary Card -->
            <div class="bg-white dark:bg-darkslate p-6 rounded-lg shadow-md flex flex-col gap-4">
                <h3 class="text-lg font-semibold text-almostblack dark:text-lightgray mb-4">Today's Goal</h3>
                <div class="flex flex-col items-start justify-between gap-4 md:flex-row md:gap-0">
                    <div class="w-full md:w-fit">
                        <p class="text-4xl font-bold text-golden"><?php echo $today_goal; ?></p>
                        <p class="text-almostblack dark:text-lightgray">Daily Shot Goal</p>
                        <hr class="mt-4 border-gray-200 dark:border-almostblack md:hidden">
                    </div>
                    
                    <div class="w-full md:w-fit">
                        <p class="text-4xl font-bold text-almostblack dark:text-lightgray"><?php echo $today_shots_made; ?></p>
                        <p class="text-almostblack dark:text-lightgray">Shots Made</p>
                        <hr class="mt-4 border-gray-200 dark:border-almostblack md:hidden">
                    </div>
					<div class="w-full md:w-fit">
                        <p class="text-4xl font-bold text-coral"><?php echo $today_shots_taken; ?></p>
                        <p class="text-almostblack dark:text-lightgray">Shots Taken</p>
                        
                    </div>
                </div>
                <div class="w-full bg-coral rounded-lg h-6 ring-2 ring-golden">
                    <div style="width: 0;" id="progressBar" class="bg-golden h-6 rounded-lg text-darkslate transition-all duration-700 ease-in-out text-sm text-center font-semibold"></div>
                </div>
                <p class=" text-almostblack dark:text-lightgray"><?php echo $shots_remaining > 0 ? "You need to take <b class='text-coral'>$shots_remaining</b> more shots to meet your goal!" : "Goal achieved!"; ?></p>
                
                <div class="flex flex-row justify-between mt-auto">
                    <a href="shotgoal.php"><button class="mt-1 text-coral font-bold p-1 px-1.5 md:px-6 md:py-4 w-fit mx-auto border-2 border-coral md:hover:bg-coral md:hover:text-white transition-colors rounded-md ">Change Goal</button></a>
                    <a href="dailyshots.php"><button class="mt-1 text-coral bg-coral font-bold p-1 px-1.5 md:px-6 md:py-4 w-fit mx-auto border-2 border-coral md:hover:bg-white md:hover:text-coral dark:md:hover:bg-darkslate text-white transition-colors rounded-md ">Input Today's Shots</button></a>

                </div>
            </div>

            <!-- Progress Chart Card -->
        <div class="bg-white dark:bg-darkslate p-6 rounded-lg shadow-md">

            
            <div class="flex justify-between mb-4">

    <h3 class="text-lg font-semibold text-almostblack dark:text-lightgray">Progress Chart</h3>
    <div class="hs-dropdown relative inline-flex">
        <button id="hs-dropdown-default" type="button" class="hs-dropdown-toggle py-3 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 focus:outline-none focus:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:text-white dark:hover:bg-neutral-700 dark:focus:bg-neutral-700" aria-haspopup="menu" aria-expanded="false" aria-label="Dropdown">
            <span id="btn-label">7 Days</span>
            <svg class="hs-dropdown-open:rotate-180 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
        </button>
        <div class="hs-dropdown-menu transition-[opacity,margin] duration hs-dropdown-open:opacity-100 hidden min-w-32 bg-white shadow-md rounded-lg p-1 space-y-0.5 mt-2 dark:bg-neutral-800 dark:border dark:border-neutral-700 dark:divide-neutral-700 after:h-4 after:absolute after:-bottom-4 after:start-0 after:w-full before:h-4 before:absolute before:-top-4 before:start-0 before:w-full" role="menu" aria-orientation="vertical" aria-labelledby="hs-dropdown-default">
            <a onclick="atime(1); " class="flex cursor-pointer items-center gap-x-3.5 py-2 px-3 rounded-lg text-sm text-gray-800 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-neutral-300 dark:focus:bg-neutral-700">
            7 Days
            </a>
            <a onclick="atime(2); " class="flex cursor-pointer items-center gap-x-3.5 py-2 px-3 rounded-lg text-sm text-gray-800 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-neutral-300 dark:focus:bg-neutral-700" >
            14 Days
            </a>
            <a onclick="atime(3); " class="flex cursor-pointer items-center gap-x-3.5 py-2 px-3 rounded-lg text-sm text-gray-800 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-neutral-300 dark:focus:bg-neutral-700" >
            90 Days
            </a>

        </div>
        </div>
        </div>
            <div id="pc1">
                <canvas id="progressChart" width="400" height="200"></canvas>
            </div>



            <div id="pc2" style="display: none;">
        
        
                <canvas id="progressChart2" width="400" height="200"></canvas>
            </div>



            <div id="pc3" style="display: none;">
        
        
                <canvas id="progressChart3" width="400" height="200"></canvas>
            </div>

        </div>

            <!-- Quick Stats Card -->
            <div class="bg-white dark:bg-darkslate p-6 rounded-lg shadow-md">
                <h3 class="text-lg font-semibold text-almostblack dark:text-lightgray mb-4">Quick Stats</h3>
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
                <span>Shooting Accuracy:</span>
                <span class="font-semibold text-dark-gray"><?php echo round($stats_data['total_shots'] / $stats_data['total_taken'] * 100, 0) ?>% Accuracy</span>
            </li>
            
        </ul>
    </div>
    <div class="bg-white dark:bg-darkslate p-6 rounded-lg shadow-md">
        <h3 class="text-lg font-semibold text-almostblack dark:text-lightgray mb-4">Badges</h3>
        <div class="relative grid grid-cols-6 lg:grid-cols-10" x-data="{b1 : false, b2 : false, b3 : false, b4: false, b5 : false}">
            
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
            <p x-show="b4" class="absolute w-60 bg-white dark:bg-darkslate text-almostblack dark:text-lightgray top-16 p-3 rounded-lg shadow-md">On a Roll: Maintain a current streak over 3 days long. Keep it up!</p>
            <p x-show="b5" class="absolute w-60 bg-white dark:bg-darkslate text-almostblack dark:text-lightgray top-16 p-3 rounded-lg shadow-md">Pinpoint Shooter: Maintain a total average of over 70%</p>
        </div>
    </div>
    <div class="container mx-auto text-almostblack">
        <div class="bg-white dark:bg-darkslate p-8 rounded-lg shadow-md max-w-md">
            <h3 class="text-lg font-semibold text-almostblack dark:text-lightgray mb-4">Leaderboard</h3>
            <table class="min-w-full table-auto text-left dark:text-lightgray">
                <thead class="">
                    <tr>
                        <th class="px-2 py-2 w-1/6">Rank</th>
                        <th class="px-2 py-2 w-2/6">Username</th>
                        <th class="px-2 py-2 w-1/6">Shots</th>

                        <th class="px-2 py-2 w-1/6">%</th>
                    </tr>
                </thead>
                <tbody class="bg-lightgray dark:bg-almostblack">
                    <?php if (!empty($leaderboard)): ?>
                        <?php foreach ($leaderboard as $index => $user): ?>
                            <tr>
                                <td class="border px-2 py-2"><?php echo $index + 1;?></td>
                                <td class="border px-2 py-2 text-coral break-all"><a href="viewprofile.php?user_id=<?php echo $user['id']; ?>"><?php echo htmlspecialchars($user['username']); ?></a></td>
                                <td class="border px-2 py-2"><?php echo $user['total_shots_taken']; ?></td>
                                <td class="border px-2 py-2"><?php echo number_format($user['shooting_percentage'], 0); ?>%</td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="border px-4 py-2 text-center">No leaderboard data available.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div> 
    </div>
    </div>
    </div>
    <footer class="bg-white py-8 text-almostblack dark:text-lightgray dark:bg-almostblack static bottom-0 left-0 w-full">
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

       
            
            const ctx2 = document.getElementById('progressChart2').getContext('2d');
            const progressChart2 = new Chart(ctx2, {
                type: 'line',
                data: {
                    labels: <?php echo json_encode(array_reverse(array_column($achart_data, 'shot_date'))); ?>,
                    datasets: [{
                        label: 'Shooting Accuracy (%)',
                        data: <?php echo json_encode(array_reverse(array_map(function($arow) {
                            return ($arow['shots_made'] / $arow['shots_taken']) * 100;
                        }, $achart_data))); ?>,
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

        
            const ctx3 = document.getElementById('progressChart3').getContext('2d');
            const progressChart3 = new Chart(ctx3, {
                type: 'line',
                data: {
                    labels: <?php echo json_encode(array_reverse(array_column($bchart_data, 'shot_date'))); ?>,
                    datasets: [{
                        label: 'Shooting Accuracy (%)',
                        data: <?php echo json_encode(array_reverse(array_map(function($brow) {
                            return ($brow['shots_made'] / $brow['shots_taken']) * 100;
                        }, $bchart_data))); ?>,
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
        
        function aupdate(){
        if (time == 1) {
            document.getElementById('pc1').style.display = 'block';
            document.getElementById('pc2').style.display = 'none';
            document.getElementById('pc3').style.display = 'none';
            document.getElementById('btn-label').innerHTML = '7 Days'
        }

        if (time == 2) {
            document.getElementById('pc1').style.display = 'none';
            document.getElementById('pc2').style.display = 'block';
            document.getElementById('pc3').style.display = 'none';
            document.getElementById('btn-label').innerHTML = '14 Days'
        }

        if (time == 3) {
            document.getElementById('pc1').style.display = 'none';
            document.getElementById('pc2').style.display = 'none';
            document.getElementById('pc3').style.display = 'block';
            document.getElementById('btn-label').innerHTML = '90 Days'
        }
    }

    
    </script>
    <script>
        function update() {
            const progress = Math.min((<?php echo $today_shots_taken ?> / <?php echo $today_goal ?>) * 100, 100).toFixed(2)
            document.getElementById('progressBar').style.width = `${progress}%`;
        }
        update();

    </script>
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