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


$player_id = $_POST['player_id'];
$coach_id = $_SESSION['coach_id'];
$player_email = $_POST['player_email'];


if ($stmt = $con->prepare('DELETE FROM players WHERE id = ? AND coach_id = ?')) {
    $stmt->bind_param('ii', $player_id, $coach_id);
    $stmt->execute();
}

else {
    exit('An error occured');
}
if ($stmt = $con->prepare('DELETE FROM accounts WHERE user_type = "player" AND email = ?')) {
    $stmt->bind_param('i',  $player_email);
    $stmt->execute();
}

else {
    exit('An error occured');
}


header('Location: coach_dashboard.php');