<?php
session_start();
// Change this to your connection info.
require 'db/db_connect.php';
// Now we check if the data from the login form was submitted, isset() will check if the data exists.
if (!isset($_POST['email'], $_POST['password']) ) {
	// Could not get the data that should have been sent.
    header('Location: error.php?a=Please fill both the username and password fields!&b=login.html');
    exit('');
}
// Prepare our SQL, preparing the SQL statement will prevent SQL injection.
if ($stmt = $con->prepare('SELECT id, password, username, user_type FROM accounts WHERE email = ?')) {
	// Bind parameters (s = string, i = int, b = blob, etc), in our case the username is a string so we use "s"
	$stmt->bind_param('s', $_POST['email']);
	$stmt->execute();
	// Store the result so we can check if the account exists in the database.
	$stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $password, $usern, $type);
        $stmt->fetch();
        // Account exists, now we verify the password.
        // Note: remember to use password_hash in your registration file to store the hashed passwords.
        if (password_verify($_POST['password'], $password)) {
            // Verification success! User has logged-in!
            // Create sessions, so we know the user is logged in, they basically act like cookies but remember the data on the server.
            session_regenerate_id();
            $_SESSION['loggedin'] = TRUE;
            $_SESSION['name'] = $usern;
            $_SESSION['id'] = $id;

            if ($type === 'user') {
                
                $_SESSION['type'] = $type;
                header('Location: home.php');
            }

            if ($type === 'coach') {
                
                $_SESSION['type'] = $type;
                $_SESSION['email'] = $_POST['email'];
                header('Location: coach_dashboard.php');
            }

            if ($type === 'player') {
                $_SESSION['email'] = $_POST['email'];
                $_SESSION['type'] = $type;
                header('Location: player_dashboard.php');
            }
        } else {
            // Incorrect password
            
            header('Location: error.php?a=Invalid email or password&b=login.html');
            exit();
            
        }
    } else {
        // Incorrect username
        header('Location: error.php?a=Invalid email or password&b=login.html');
            exit();
    }

	$stmt->close();
    
}
?>