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
require 'db/db_connect.php';
$conn = $con;
// Insert or update the daily goal for the user
function setDailyGoal($conn, $user_id, $shots_goal) {
    $query = "INSERT INTO user_goals (user_id, shots_goal) 
              VALUES (?, ?)
              ON DUPLICATE KEY UPDATE shots_goal = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iii", $user_id, $shots_goal, $shots_goal);
    $stmt->execute();

    $query = "UPDATE user_shots SET goal = ? WHERE user_id = ? AND shot_date = CURDATE()";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii",  $shots_goal, $user_id);
    $stmt->execute();

    $stmt->close();
    header("Location: home.php");
}

setDailyGoal($conn, $_SESSION['id'], $_POST['shotgoal']);