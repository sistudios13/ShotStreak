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


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php echo("<title>".$player_name."'s Profile - Shotstreak</title>")?>
</head>
<body>
    <?php echo($shooting_percentage) ?>
</body>
</html>