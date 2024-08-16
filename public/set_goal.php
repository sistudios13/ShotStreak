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

// Insert or update the daily goal for the user
function setDailyGoal($conn, $user_id, $shots_goal) {
    $query = "INSERT INTO user_goals (user_id, shots_goal) 
              VALUES (?, ?)
              ON DUPLICATE KEY UPDATE shots_goal = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iii", $user_id, $shots_goal, $shots_goal);
    $stmt->execute();
    $stmt->close();
    header("Location: home.php");
}

setDailyGoal($conn, $_SESSION['id'], $_POST['shotgoal']);