<?php
session_start();

// Sprawdzenie, czy użytkownik jest zalogowany jako administrator
// Jeśli nie, przekierowuje do strony logowania dentysty
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== 'administrator') {
    header("location: ../views/dentist_login.php");
    exit;
}

// Wczytanie konfiguracji bazy danych i modelu dentysty
require_once '../../config/database.php';
require_once '../models/dentist.php';

// Utworzenie połączenia z bazą danych
$database = new Database();
$db = $database->getConnection();
$dentist = new Dentist($db);

// Sprawdzenie, czy ID dentysty zostało przekazane i czy jest niepuste
if (isset($_GET["dentist_id"]) && !empty(trim($_GET["dentist_id"]))) {
    $dentist_id = trim($_GET["dentist_id"]);  // Usunięcie zbędnych spacji dla bezpieczeństwa

    // Sprawdzenie, czy dentysta nie jest administratorem
    if ($dentist->isAdministrator($dentist_id)) {
        // Jeśli tak, ustawienie komunikatu o błędzie i przekierowanie do panelu admina
        $_SESSION['error_message'] = "Nie można usunąć dentysty z rolą administratora.";
        header("location: ../views/admin_panel.php");
        exit;
    }

    // Próba usunięcia dentysty
    if ($dentist->delete($dentist_id)) {
        // Logowanie akcji i ustawienie komunikatu o sukcesie
        error_log("Usunięto dentystę o ID: $dentist_id");
        $_SESSION['success_message'] = "Pomyślnie usnięto lekarza.";
        header("location: ../views/admin_panel.php");
        exit;
    } else {
        // W przypadku błędu, ustawienie komunikatu i przekierowanie
        $_SESSION['error_message'] = "Wystąpił błąd podczas usuwania lekarza.";
        header("location: ../views/admin_panel.php");
        exit;
    }
} else {
    // Przekierowanie do panelu admina, jeśli ID dentysty nie zostało przekazane
    error_log("Nie przekazano ID lekarza");
    header("location: ../views/admin_panel.php");
    exit;
}
