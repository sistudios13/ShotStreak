<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error - Shotstreak</title>
<script src="https://cdn.tailwindcss.com"></script>
    <script src="tailwindextras.js"></script>
    <link rel="stylesheet" href="main.css">
    <link rel="shortcut icon" href="assets/isoLogo.svg" type="image/x-icon">

    <script>
        function next() {document.location.href = "<?php echo $_GET['b'] ?? 'index.html' ?>";}setTimeout(() => next(),  3500);
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
        <a class="text-lg text-coral text-center mx-auto font-bold" href="<?php echo $_GET['b'] ?? 'index.html' ?>">Back</a>
    </div>
</body>
</html>