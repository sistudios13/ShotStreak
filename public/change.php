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
$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if (mysqli_connect_errno()) {
	exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}
// Now we check if the data was submitted, isset() function will check if the data exists.
if (!isset($_POST['newpassword'])) {
	// Could not get the data that should have been sent.
	exit('Please complete the registration form!');
}
// Make sure the submitted registration values are not empty.
if (empty($_POST['newpassword'])) {
	// One or more values are empty.
	exit('Please complete the registration form');
}
// We don't have the password or email info stored in sessions, so instead, we can get the results from the database.
if ($stmt = $con->prepare('UPDATE accounts SET password = ? WHERE id = ?')) {
    // We do not want to expose passwords in our database, so hash the password and use password_verify when a user logs in.
    $password = password_hash($_POST['newpassword'], PASSWORD_DEFAULT);
    $stmt->bind_param('si', $password, $_SESSION['id']);
    $stmt->execute();
    
}

$stmt->close();
header('Location: logout.php')
?>