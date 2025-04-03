<?php

session_start(); // Rozpoczęcie sesji

// Zadeklarowanie zmiennych i ustawienie ich wartości na pusty ciąg znaków
$first_name = $first_name_err = "";
$last_name = $last_name_err = "";
$email = $email_err = "";
$password = $password_err = "";

// Sprawdzenie czy dane zostały ustawione i przypisanie ich do zmiennych
if (isset($viewData)) {
    $first_name = $viewData['first_name'];
    $first_name_err = $viewData['first_name_err'];
    $last_name = $viewData['last_name'];
    $last_name_err = $viewData['last_name_err'];
    $email = $viewData['email'];
    $email_err = $viewData['email_err'];
    $password_err = $viewData['password_err'];
}
?>

<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../public/css/register_login.css">
    <link rel="stylesheet" href="../../public/css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <title>Rejestracja</title>
</head>

<body class="bg-light">
    <?php include 'shared_navbar.php'; ?>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">

                <!-- Formularz rejestracji pacjenta -->
                <div class="card my-5">
                    <div class="card-body">
                        <h2 class="card-title text-center mb-4">Formularz rejestracji</h2>
                        <form action="../controllers/patient_add_controller.php" method="post">
                            <div class="form-group mb-3">
                                <label class="form-label">Imię</label>
                                <input type="text" name="first_name" class="form-control <?php echo (!empty($first_name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $first_name; ?>">
                                <span class="invalid-feedback"><?php echo $first_name_err; ?></span>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">Nazwisko</label>
                                <input type="text" name="last_name" class="form-control <?php echo (!empty($last_name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $last_name; ?>">
                                <span class="invalid-feedback"><?php echo $last_name_err; ?></span>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>">
                                <span class="invalid-feedback"><?php echo $email_err; ?></span>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">Hasło</label>
                                <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
                                <span class="invalid-feedback"><?php echo $password_err; ?></span>
                            </div>
                            <div class="form-group mb-4">
                                <input type="submit" class="btn btn-primary w-100" value="Zarejestruj się">
                            </div>
                            <p class="text-center">Masz już konto? <a href="/gabinet/app/views/patient_login.php">Zaloguj się</a></p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>

</html>