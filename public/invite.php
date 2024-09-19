<?php
session_start();
$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'shotstreak';
$conn = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if (mysqli_connect_errno()) {
	exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}


// Assuming coach is logged in and has a session
$coach_id = $_SESSION['coach_id']; // Coach's user ID
$coach_name = $_SESSION['name'];




// Always set content-type when sending HTML email
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $player_email = $_POST['player_email'];
    $player_name = $_POST['player_name'];

    // Generate a random token for the invitation link
    $token = bin2hex(random_bytes(16));

    // Insert the invitation into the database
    $query = "INSERT INTO invitations (coach_id, player_name, player_email, token) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("isss", $coach_id, $player_name, $player_email, $token);
    if ($stmt->execute()) {
        // Send an email with the invitation link (Pseudo-code)
        $invite_link = "http://localhost/shotstreak/public/acceptinvite.php?token=" . $token;

        $message = "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta http-equiv='X-UA-Compatible' content='IE=edge'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Invitation to Join ShotStreak</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    background-color: #f4f4f4;
                    margin: 0;
                    padding: 0;
                }
                .email-container {
                    max-width: 600px;
                    margin: 0 auto;
                    background-color: #ffffff;
                    padding: 20px;
                    border-radius: 8px;
                    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                }
                .email-header {
                    text-align: center;
                    padding-bottom: 20px;
                }
                .email-header h1 {
                    color: #ff6b6b;
                }
                .email-body {
                    color: #333;
                    line-height: 1.6;
                }
                .email-body p {
                    margin: 10px 0;
                }
                .cta-button {
                    display: inline-block;
                    padding: 10px 20px;
                    background-color: #ff6b6b;
                    color: #ffffff;
                    text-decoration: none;
                    border-radius: 5px;
                    margin-top: 20px;
                    margin-bottom: 20px;
                    font-size: 16px;
                }
                .cta-button:hover {
                    background-color: #e65a5a;
                }
                .footer {
                    text-align: center;
                    color: #999;
                    font-size: 12px;
                    margin-top: 20px;
                }

                
            </style>
        </head>
        <body>

        <div class='email-container'>
            <div class='email-header'>
                <h1>Join ShotStreak!</h1>
                <img title='logo' src='https://shotstreak.simonsites.com/assets/isoLogo.svg' alt='Logo' height='200' width='200'>
            </div>
            
            <div class='email-body'>
                <p><b>Hello, $player_name</b></p>
                <p>You’ve been invited by <b>$coach_name</b> to join ShotStreak, a basketball shot tracking platform that helps you monitor your daily shot goals and performance.</p>
                <p>To get started, simply click the link below to register and join your coach's team:</p>
                
                <a href='$invite_link' class='cta-button'>Join ShotStreak</a>
                
                <p>If you did not expect this email, feel free to ignore it.</p>
                <p>Looking forward to seeing you on the court!</p>
            </div>
            
            <div class='footer'>
                <p>&copy; 2024 ShotStreak. All rights reserved.</p>
            </div>
        </div>

        </body>
        </html>

        ";

        if (mail($player_email, "You've been invited to join ShotStreak!", $message, $headers)) {
            header("Location: coach_dashboard.php");
        }
        else {
            echo "Email failed to send";
        }

        
    } else {
        echo "An error occured";
    }
}
?>
