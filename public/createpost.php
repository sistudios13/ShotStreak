<?php
// We need to use sessions, so you should always start sessions using the below code.
session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
	header('Location: index.html');
	exit;
}
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Profile Page</title>
		<link href="createpost.css" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer">
        <script src="//unpkg.com/alpinejs" defer></script>
    </head>
	<body class="loggedin">
		<nav class="nav">
			
				<h3>Simon Sites</h3>
                <div class="nav-content">
                    <a href="home.php"><i class="fa fa-home" aria-hidden="true" id="i"></i> Home</a>
                    <a href="profile.php"><i class="fas fa-user-circle" id="i"></i> Profile</a>
				    <a href="logout.php"><i class="fas fa-sign-out-alt" id="i"></i> Logout</a>
                </div>
    
			
		</nav>
		<div class="content">
			<h2>Create a Post</h2>
            <form class="container" action="sendpost.php" method="POST">
				<div class="form-content">
					<label for="title">Title:</label>
					<input name="title" id="title" type="text" maxlength="75">
				</div>
				<div class="form-content">
					<label for="body">Body:</label>
					<textarea name="body" id="body" type="text" maxlength="3000"></textarea>
				</div>
                <input type="submit" value="Submit">
            </form>
			
		</div>
	</body>
</html>