<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Success - Shotstreak</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="tailwindextras.js"></script>
    <link rel="stylesheet" href="main.css">
    <link rel="shortcut icon" href="assets/isoLogo.svg" type="image/x-icon">

    <script>
        function next() {document.location.href = "<?php echo $_GET['b'] ?? 'index.html' ?>";}setTimeout(() => next(),  2000);
    </script>
</head>
<body class="container mx-auto">
    <div class="flex flex-row gap-4 mt-6 justify-center items-center">
        <img src="assets/isoLogo.svg" class="size-24" alt="logo">
        <h1 class="text-2xl font-bold text-center">Shotstreak</h1>
    </div>
    <div>
        <h1 class="text-2xl mt-6 font-bold text-center">Success</h1>
        <p class="text-xl mt-6 text-center text-gray-600">Operation successful, redirecting you now!</p>
    </div>

</body>
</html>