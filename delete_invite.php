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
$DATABASE_USER = 'u937462812_shotstreak';
$DATABASE_PASS = 'Shott10?';
$DATABASE_NAME = 'u937462812_shotstreak';
$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if (mysqli_connect_errno()) {
	exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}


$stmt = $con->prepare('UPDATE invitations SET status = "revoked" WHERE token = ?');
$stmt->bind_param('s', $_POST['token']);
$stmt->execute();
header('Location: coach_dashboard.php');