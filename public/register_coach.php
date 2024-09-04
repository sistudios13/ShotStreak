<?php

// Connect to the database
$host = 'localhost';
$dbname = 'shotstreak';
$username = 'root'; // Change this if you have a different DB username
$password = ''; // Change this if you have a DB password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database: " . $e->getMessage());
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
$coach_name = $_POST['coach_name'];
$email = $_POST['email'];
$password = $_POST['password'];
$team_name = $_POST['team_name'];

// Validate input
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
die("Invalid email format."); //ADD ERROR PAGE
}

//MORE VALIDATION

// Hash the password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Prepare SQL and bind parameters
$stmt = $pdo->prepare("INSERT INTO coaches (coach_name, email, password, team_name) VALUES (:coach_name, :email,
:password, :team_name)");
$stmt->bindParam(':coach_name', $coach_name);
$stmt->bindParam(':email', $email);
$stmt->bindParam(':password', $hashed_password);
$stmt->bindParam(':team_name', $team_name);

// Execute the statement
try {
$stmt->execute();
header("Location: coachlog.html");
} catch (PDOException $e) {
if ($e->getCode() == 23000) { // Duplicate entry
die("This email is already registered."); //ERROR PAGE
} else {
die("An error occurred: " . $e->getMessage());
}
}
}
