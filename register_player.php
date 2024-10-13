<?php

require 'db/db_connect.php';



try {
    $pdo = new PDO("mysql:host=$DATABASE_HOST;dbname=$DATABASE_NAME", $DATABASE_USER, $DATABASE_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database: " . $e->getMessage());
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
$username = $_POST['username'];
$email = $_POST['email'];
$password = $_POST['password'];
$coach_id = $_POST['coach_id'];
$token = $_POST['invite_token'];

// Validate input
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
die("Invalid email format."); //ADD ERROR PAGE
}

//MORE VALIDATION

// Hash the password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

if ($stmt = $con->prepare('SELECT id, password FROM accounts WHERE email = ?')) {
	// Bind parameters (s = string, i = int, b = blob, etc), hash the password using the PHP password_hash function.
	$stmt->bind_param('s',  $_POST['email']);
	$stmt->execute();
	$stmt->store_result();
	// Store the result so we can check if the account exists in the database.
	if ($stmt->num_rows > 0) {
		// Username already exists
		echo "<script>setTimeout(() => window.location.href = 'error.php?a=User already exists&b=index.html', 700);</script>";
        exit();
	} else {

// Prepare SQL and bind parameters
$stmt = $pdo->prepare("INSERT INTO players (player_name, email, password, coach_id) VALUES (:player_name, :email,
:password, :coach_id)");
$stmt->bindParam(':player_name', $username);
$stmt->bindParam(':email', $email);
$stmt->bindParam(':password', $hashed_password);
$stmt->bindParam(':coach_id', $coach_id);

// Execute the statement
try {
$stmt->execute();




// add the player information to the accounts tables
if ($stmt = $con->prepare('INSERT INTO accounts (username, password, email, user_type) VALUES (?, ?, ?, "player")')) {
    // We do not want to expose passwords in our database, so hash the password and use password_verify when a user logs in.
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); 
    $stmt->bind_param('sss', $username, $password, $email);
    $stmt->execute();
    
}

if ($stmt = $con->prepare('UPDATE invitations SET status = "accepted" WHERE token = ?')) {
    $stmt->bind_param('s', $token);
    $stmt->execute();
    echo "<script>setTimeout(() => window.location.href = 'success.php?b=login.html', 700);</script>";

}



else {
    // Something is wrong with the SQL statement, so you must check to make sure your accounts table exists with all three fields.
    echo 'Could not prepare statement!'; // ERROR PAGE
}
} 

catch (PDOException $e) {
    if ($e->getCode() == 23000) { // Duplicate entry
        echo "<script>setTimeout(() => window.location.href = 'error.php?a=User already exists&b=index.html', 700);</script>";
        exit(); //ERROR PAGE
    } else {
    die("An error occurred: " . $e->getMessage());
    }
    }
    }
//
}


} 


