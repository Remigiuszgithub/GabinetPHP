<?php
session_start(); // Start sesji

// Sprawdzanie, czy użytkownik ma uprawnienia administracyjne
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== 'administrator') {
    header("location: dentist_login.php");
    exit;
}

// Komunikat o błędzie edycji danych
$update_err = "";
if (isset($_SESSION['update_err'])) {
    $update_err = $_SESSION['update_err'];
    unset($_SESSION['update_err']); // Czyszczenie błędu z sesji
}

// Pobranie danych dentysty o podanym ID
if (isset($_GET['dentist_id'])) {
    $dentist_id = $_GET['dentist_id'];

    // Zaimportowanie pliku konfiguracyjnego bezy danych i modelu dentysty
    require_once '../../config/database.php';
    require_once '../models/dentist.php';

    // Inicjalizacja połączenia z bazą danych
    $database = new Database();
    $db = $database->getConnection();
    
    // Inicjalizacja obiektu dentysty
    $dentist = new Dentist($db);

    // Pobranie danych dentysty o podanym ID
    $dentist_data = $dentist->getDentistById($dentist_id);

    // Sprawdzenie, czy dentysta o podanym ID istnieje
    if ($dentist_data === false) {
        exit('Lekarz o podanym ID nie został znaleziony.');
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="../../public/css/styles.css">
    <link rel="stylesheet" href="../../public/css/admin_panel.css">
    <title>Edycja danych lekarza</title>
</head>

<body>
    <div class="container">
        <?php include 'shared_navbar.php'; ?>

        <!-- Formularz edycji danych dentysty -->
        <div class="card mt-4 col-md-6" id="add-dentist">
            <div class="card-header">
                Formularz edycji danych lekarza
                <!-- Komunikat o błędzie edycji danych -->
                <?php if (!empty($update_err)) : ?>
                    <div class="alert alert-danger">
                        <?php echo $update_err; ?></div>
                <?php endif; ?>

                <!-- Komunikat o błędzie zmiany hasła -->
                <?php if (isset($_SESSION['password_err'])) : ?>
                    <div class="alert alert-danger">
                        <?php
                        echo $_SESSION['password_err'];
                        unset($_SESSION['password_err']);
                        ?>
                    </div>
                <?php endif; ?>

                <!-- Komunikat o sukcesie -->
                <?php if (isset($_SESSION['update_success'])) : ?>
                    <div class="alert alert-success">
                        <?php
                        echo $_SESSION['update_success'];
                        unset($_SESSION['update_success']);
                        ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <form action="../controllers/dentist_update_controller.php" method="post">
                    <input type="hidden" name="dentist_id" value="<?php echo htmlspecialchars($dentist_id); ?>">
                    <!-- Dane osobowe -->
                    <div class="mb-3">
                        <label for="first_name" class="form-label">Imię</label>
                        <input type="text" class="form-control" id="first_name" name="first_name" required value="<?php echo htmlspecialchars($dentist_data['first_name']); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="last_name" class="form-label">Nazwisko</label>
                        <input type="text" class="form-control" id="last_name" name="last_name" required value="<?php echo htmlspecialchars($dentist_data['last_name']); ?>">
                    </div>
                    <!-- Kontakt i dane logowania -->
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required value="<?php echo htmlspecialchars($dentist_data['email']); ?>">
                    </div>
                    <!-- Specjalizacja -->
                    <div class="mb-3">
                        <label for="specialization" class="form-label">Specjalizacja</label>
                        <input type="text" class="form-control" id="specialization" name="specialization" value="<?php echo htmlspecialchars($dentist_data['specialization']); ?>">
                    </div>
                    <button type="submit" class="btn btn-primary m-1">Potwierdź</button>
                    <a href="../views/admin_panel.php" class="btn btn-secondary m-1">Anuluj</a>
                </form>
            </div>
        </div>
    </div>
</body>

</html>