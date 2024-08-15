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
// We don't have the password or email info stored in sessions, so instead, we can get the results from the database.
$stmt = $con->prepare('SELECT password, email FROM accounts WHERE id = ?');
// In this case we can use the account ID to get the account info.
$stmt->bind_param('i', $_SESSION['id']);
$stmt->execute();
$stmt->bind_result($password, $email);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Profile Page</title>
		<link href="profile.css" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer">
        <script src="//unpkg.com/alpinejs" defer></script>
    </head>
	<body class="loggedin">
		<nav class="nav">
			
				<h3>Simon Sites</h3>
                <div class="nav-content">
                    <a href="home.php"><i class="fa fa-home" aria-hidden="true" id="i"></i> Home</a>
				    <a href="logout.php"><i class="fas fa-sign-out-alt" id="i"></i> Logout</a>
                </div>
    
			
		</nav>
		<div class="content">
			<h2>Profile</h2>
			<div class="account-content">
                <div class="account-cont">
                    <h3>Account Details</h3>
                    <table class="info-table">
                        <tr>
                            <td>Username:</td>
                            <td><?=htmlspecialchars($_SESSION['name'], ENT_QUOTES)?></td>
                        </tr>
                        <tr>
                            <td>Email:</td>
                            <td><?=htmlspecialchars($email, ENT_QUOTES)?></td>
                        </tr>
                    </table>
                </div>
				<div x-data="{change: false}">
                    <h3>
                        Edit Account
                    </h3>
                    <div class="change" @click.away="change = false">
                        <a @click="change = !change" >Change Password</a>
                        <form action="change.php" method="POST" x-show="change" x-cloak x-transition>
                            <input autofocus type="password" name="newpassword" minlength="5" maxlength="20" id="newpassword" placeholder="New Password">
                            <input type="submit" value="Change">
                        </form>
                    </div>
                </div>
                
			</div>
		</div>
	</body>
</html>