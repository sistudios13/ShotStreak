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
$userid = $_SESSION['id'];
//Get current

$sql = 'SELECT shots_made, shots_taken FROM user_shots WHERE user_id = ? AND shot_date = CURDATE()';
$getshots = $conn->prepare($sql);
$getshots->bind_param('i', $userid);
$getshots->execute();

$getshots->bind_result($today_shots_made, $today_shots_taken);
$getshots->fetch();
$getshots->close();

$added_taken = $today_shots_taken + $_POST['shotstaken'];
$added_made = $today_shots_made + $_POST['shotsmade'];
// Insert or update the shots data for the user

$query = "INSERT INTO user_shots (user_id, shot_date, shots_taken, shots_made) 
            VALUES (?, CURDATE(), ?, ?)
            ON DUPLICATE KEY UPDATE shots_taken = ?, shots_made = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("iiiii", $userid, $_POST['shotstaken'], $_POST['shotsmade'], $added_taken, $added_made);
$stmt->execute();
$stmt->close();

header('Location: home.php');