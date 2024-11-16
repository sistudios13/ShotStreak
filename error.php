<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error - Shotstreak</title>
<script src="https://cdn.tailwindcss.com"></script>
    <script src="tailwindextras.js"></script>
    <link rel="stylesheet" href="main.css">
    <link rel="icon" type="image/png" href="assets/favicon-48x48.png" sizes="48x48" />
    <link rel="icon" type="image/svg+xml" href="assets/favicon.svg" />
    <link rel="shortcut icon" href="assets/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="assets/apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="Shotstreak" />
    <link rel="manifest" href="assets/site.webmanifest" />

    <script>
        function next() {document.location.href = "<?php echo $_GET['b'] ?? 'index.php' ?>";}setTimeout(() => next(),  3500);
    </script>
</head>
<body class="container mx-auto">
    <div class="flex flex-row gap-4 mt-6 justify-center items-center">
        <img src="assets/isoLogo.svg" class="size-24" alt="logo">
        <h1 class="text-2xl font-bold text-center">Shotstreak</h1>
    </div>
    <div>
        <h1 class="text-2xl mt-6 font-bold text-center">An Error Occurred</h1>
        <p class="text-xl text-center text-gray-600 mt-6"><?php echo $_GET['a'] ?? 'none' ?></p>
    </div>
    <div class="flex justify-center mt-6">
        <a class="text-lg text-coral text-center mx-auto font-bold" href="<?php echo $_GET['b'] ?? 'index.php' ?>">Back</a>
    </div>
    <div>
        <p class="text-center text-gray-600 mt-6">Support Page <a class="font-bold" href="support.php">Here</a></p>
    </div>
</body>
</html>