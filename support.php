<?php
session_start();

$logged = false;

if (isset($_SESSION['loggedin'])) {
	$logged = true;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support - ShotStreak</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="tailwindextras.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="icon" type="image/png" href="assets/favicon-48x48.png" sizes="48x48" />
    <link rel="icon" type="image/svg+xml" href="assets/favicon.svg" />
    <link rel="shortcut icon" href="assets/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="assets/apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="Shotstreak" />
    <link rel="manifest" href="assets/site.webmanifest" />
    <style>
      .box {
          --mask:
            radial-gradient(178.89px at 50% calc(100% - 240px),#000 99%,#0000 101%) calc(50% - 160px) 0/320px 100%,
            radial-gradient(178.89px at 50% calc(100% + 160px),#0000 99%,#000 101%) 50% calc(100% - 80px)/320px 100% repeat-x;
          -webkit-mask: var(--mask);
          mask: var(--mask);
        }

        html {
          scroll-behavior: smooth;
        }
    </style>
</head>
<body class="bg-lightgray text-almostblack">
    <section>
        <div class="h-fit w-full bg-white flex flex-col px-4 pt-4 items-center box gap-6 pb-32">
          <div class="flex flex-row gap-4 items-center">
            <img src="assets/isoLogo.svg" class="size-24" alt="logo">
            <h1 class="text-2xl font-bold text-left">Shotstreak <br><span class="text-coral"> Support</span></h1>
          </div>
          <div class="space-y-4 flex flex-col text-center items-center xl:flex-row xl:gap-12 gap-6 xl:px-12">
            <div class="space-y-4 md:px-16 md:pt-6">
              <h1 class="text-3xl font-bold">Welcome to Shotstreak Support!</h1>
              <p class="text-xl max-w-lg">See real progress and improve your skills with powerful analytics and statistics get started with Shotstreak today!</p>
              <div class="space-y-4 flex flex-col ">
                <a href="#faq"><button class="mt-6 bg-coral text-white md:hover:bg-coralhov text-lg px-6 py-3 rounded-full md:hover:scale-110  transition-all">See our FAQ</button></a>
                <?php if ($logged): ?>
                  <a href="coachreg.php" class="text-coral text-sm font-semibold">Back to Home</a>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
    </section>

    <section id="troubleshooting">
      <div class="px-8 xl:px-24 pb-12">
        <h2 class="text-3xl font-bold">
          Troubleshooting Tips
        </h2>
        <div>
          <h3 class="text-2xl font-semibold mb-6 mt-2 text-coral">Common Issues</h3>
          <div>
            <ul class="space-y-4">
              <li>
                <p class="text-lg ml-1 font-bold">Having trouble logging in?</p>
                <p>Double-check your credentials or use the “Forgot Password” link to reset your login.</p>
              </li>
              <li>
                <p class="text-lg ml-1 font-bold">Shot tracking not saving?</p>
                <p>Make sure you’re connected to the internet and refresh your page. If the issue persists, try clearing your browser’s cache.</p>
              </li>
              <li>
                <p class="text-lg ml-1 font-bold">Leaderboard not updating?</p>
                <p>This can be due to recent changes. Wait a moment and refresh your dashboard to see the latest results.</p>
              </li>
              
            </ul>
          </div>
        </div>
      </div>
    </section>

    <section id="faq">

    </section>
    
      <footer class="bg-darkslate py-8 text-white">
        <div class="container mx-auto grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="footer-links flex flex-col justify-center items-center">
            <?php if ($logged): ?>
              <a href="index.php" class="block mb-2 text-center">Home</a>
            <?php else: ?>
              <a href="index.php" class="block mb-2 text-center">Home</a>
              <a href="register.php" class="block mb-2 text-center">Register</a>
              <a href="login.php" class="block mb-2 text-center">Login</a>
              <a href="support.php" class="block mb-2 text-center">Support</a>
            <?php endif;?>
          </div> 
          <div class="text-center flex flex-col justify-center items-center">
            <p class="text-xs">© 2024 ShotStreak. All rights reserved.</p>
            <p class="text-xs">Website Created by <a target="_blank" class="font-bold" href="https://portfolio.simonsites.com">Simon Papp</a> - <a target="_blank" class="font-bold" href="https://simonsites.com">SimonSites</a></p>
          </div>
        </div>
      </footer>
  
  
</body>
</html>