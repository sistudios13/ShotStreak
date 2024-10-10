<?php

require 'db/db_connect.php';


// Get the token from the URL
$token = $_GET['token'];



// Verify the token
$query = "SELECT coach_id, player_email, player_name FROM invitations WHERE token = ? AND status = 'pending'";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $invite = $result->fetch_assoc();
    $coach_id = $invite['coach_id'];
    $player_email = $invite['player_email'];
    $player_name = $invite['player_name'];
    
    $stmt = $conn->prepare('SELECT team_name, coach_name FROM coaches WHERE coach_id = ?');
    $stmt->bind_param('i', $coach_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $names = $res->fetch_assoc();
    $team_name = $names['team_name'];
    $coach_name = $names['coach_name'];

    // Show registration form for the player (populate email, and let them set a password)
    // After registering, associate the player with the coach and update the invitation status
} else {
    header('Location: error.php?a=Invalid or exipred invitation');
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Register - ShotStreak</title>
        <script src="https://cdn.tailwindcss.com"></script>
    <script src="tailwindextras.js"></script>

        

        <link rel="stylesheet" href="main.css">
        <link rel="shortcut icon" href="assets/isoLogo.svg" type="image/x-icon">
    </head>
    <body class="bg-light-gray">

        <!-- Registration Form Container -->
        <div class="flex items-center justify-center min-h-screen">
            <div class="bg-white p-8 rounded-lg shadow-lg max-w-md w-full">
                <!-- Logo -->
                <div class="text-center mb-6">
                    <img src="assets/isoLogo.svg" alt="ShotStreak Logo" class="mx-auto h-16">
                    <h1 class="text-2xl font-bold text-almostblack mt-4">Join "<?php echo htmlspecialchars($team_name); ?>"</h1>
                </div>
    
                <!-- Registration Form -->
                <form id="registerForm" class="space-y-4" action="register_player.php" method="POST" autocomplete="off">
                    <input type="hidden" name="coach_id" value="<?php echo htmlspecialchars($coach_id); ?>">
                    <input type="hidden" name="invite_token" value="<?php echo htmlspecialchars($token); ?>">
                <!-- Name Input -->
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700">Name</label>
                        <input type="text" name="username"  id="username" class="mt-1 p-2 w-full border rounded-md focus-visible:outline-coral" value="<?php echo htmlspecialchars($player_name); ?>" readonly>
                    </div>
    
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email:</label>
                        <input type="email" name="email"  id="email" class="mt-1 p-2 w-full border rounded-md focus-visible:outline-coral" value="<?php echo htmlspecialchars($player_email); ?>" readonly>
                    </div>

                    <!-- Password Input -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        <input type="password" name="password"  id="password" minlength="5" maxlength="20" class="mt-1 p-2 w-full border rounded-md focus-visible:outline-coral" required>
                    </div>
    
                    <!-- Confirm Password Input -->
                    <div>
                        <label for="confirm-password" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                        <input type="password" id="confirm-password" name="confirm-password" class="mt-1 p-2 w-full border rounded-md focus-visible:outline-coral" required>
                    </div>
    
                    <!-- Submit Button -->
                    <button type="submit" class="w-full md:hover:bg-coralhov bg-coral text-white py-2 rounded-md font-semibold hover:bg-coral-red-light transition-colors">Register</button>
                </form>
    
                
            </div>
        </div>
        <footer class="bg-darkslate py-8 text-white">
            <div class="container mx-auto grid grid-cols-1 md:grid-cols-2 gap-4">
              <div class="footer-links flex flex-col justify-center items-center">
                <a href="index.html" class="block mb-2 text-center">Landing</a>
                <a href="register.html" class="block mb-2 text-center">Register</a>
                <a href="login.html" class="block mb-2 text-center">Login</a>
                <!-- Add more links -->
              </div> 
              <div class="text-center flex justify-center items-center">
                <p class="text-xs">© 2024 ShotStreak. All rights reserved.</p>
              </div>
            </div>
          </footer>
        <script src="confirmpass.js"></script>

    </body>
    </html>