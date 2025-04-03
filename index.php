<?php
session_start();
?>

<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GABINET</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css' rel='stylesheet'>
    <link rel="stylesheet" href="public/css/styles.css">
    <link rel="stylesheet" href="public/css/index.css">
    <style>
        #myVideo {
            filter: brightness(60%);
        }
    </style>
</head>

<body>
    <?php include 'app/views/shared_navbar.php'; ?>

    <div class="container">
        <div class="row">
            <h1 class="text-center">GABINET</h1>
            <h2 class="text-center">Profesjonalna opieka medyczna</h2>
            <br>
            <div class="col-lg-5 p-4">
                <h2>Zarejestruj się w naszym gabinecie i zarezerwuj swoją pierwszą wizytę!</h2>
                <br>
                <a href="/gabinet/app/views/patient_register.php" class="btn btn-lg btn-primary m-2">Zarejestruj się teraz</a>
                <a href="/gabinet/app/views/patient_login.php" class="btn btn-lg btn-secondary m-2">Zaloguj się</a>
            </div>
            <div class=" col-lg-7 p-4">
                <video autoplay muted loop id="myVideo" class="w-100">
                    <source src="../gabinet/public/videos/start_video2.mp4" type="video/mp4">
                    Twoja przeglądarka nie obsługuje tagu video.
                </video>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>

</body>

</html>