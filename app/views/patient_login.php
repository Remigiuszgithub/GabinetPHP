<?php
session_start();
// Sprawdzanie, czy są błędy logowania do wyświetlenia
$email_err = $password_err = $login_err = "";

if (isset($_SESSION['login_err'])) {
    $login_err = $_SESSION['login_err'];
    unset($_SESSION['login_err']);
}

if (isset($_SESSION['email_err'])) {
    $email_err = $_SESSION['email_err'];
    unset($_SESSION['email_err']);
}

if (isset($_SESSION['password_err'])) {
    $password_err = $_SESSION['password_err'];
    unset($_SESSION['password_err']);
}
?>

<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../public/css/register_login.css">
    <link rel="stylesheet" href="../../public/css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Logowanie</title>
</head>

<body class="bg-light">
    <?php include 'shared_navbar.php'; ?>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card my-5">
                    <div class="card-body">
                        <h2 class="card-title text-center">Logowanie</h2>
                        <?php
                        // Wyświetlenie komunikatu o błędzie logowania
                        if (!empty($login_err)) {
                            echo '<div class="alert alert-danger">' . $login_err . '</div>';
                        }
                        ?>

                        <!-- Formularz logowania pacjenta -->
                        <form action="../controllers/patient_login_controller.php" method="post">
                            <div class="form-group mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="">
                                <span class="invalid-feedback"><?php echo $email_err; ?></span>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">Hasło</label>
                                <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
                                <span class="invalid-feedback"><?php echo $password_err; ?></span>
                            </div>
                            <div class="form-group mb-4">
                                <input type="submit" class="btn btn-primary w-100" value="Zaloguj się">
                            </div>
                            <p class="text-center">Nie masz konta? <a href="patient_register.php">Zarejestruj się!</a></p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>

</html>