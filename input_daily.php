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
$userid = $_SESSION['id'];
//Get current

//Get master Goal
$stmt = $conn->prepare('SELECT shots_goal FROM user_goals WHERE user_id = ?');
$stmt->bind_param('i', $userid);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$master_goal = $row['shots_goal'];

$sql = 'SELECT shots_made, shots_taken FROM user_shots WHERE user_id = ? AND shot_date = CURDATE()';
$getshots = $conn->prepare($sql);
$getshots->bind_param('i', $userid);
$getshots->execute();
$gotshots = $getshots->get_result();
$shots = $gotshots->fetch_assoc();
$today_shots_made = $shots['shots_made'];
$today_shots_taken = $shots['shots_taken'];

$added_taken = $today_shots_taken + $_POST['shotstaken'];
$added_made = $today_shots_made + $_POST['shotsmade'];
// Insert or update the shots data for the user
if (!is_null($today_shots_made)){ //DUP
	$query = "UPDATE user_shots SET shots_taken = ?, shots_made = ?
            WHERE user_id = ? AND shot_date = CURDATE()";
	$stmt = $conn->prepare($query);
	$stmt->bind_param("iii",  $added_taken, $added_made, $userid);
	$stmt->execute();
	$stmt->close();
	
}

if (is_null($today_shots_made)){ //NEW
	$query = "INSERT INTO user_shots (user_id, shot_date, shots_taken, shots_made) 
            VALUES (?, CURDATE(), ?, ?)";
	$stmt = $conn->prepare($query);
	$stmt->bind_param("iii", $userid, $_POST['shotstaken'], $_POST['shotsmade']);
	$stmt->execute();

	$query2 = "UPDATE user_shots SET goal = ? WHERE user_id = ? AND shot_date = CURDATE()";
    $stmt = $conn->prepare($query2);
    $stmt->bind_param("ii",  $master_goal, $userid);
    $stmt->execute();
	$stmt->close();
}

header('Location: home.php');