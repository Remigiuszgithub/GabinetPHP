<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Rozpoczęcie sesji
session_start();

// Sprawdzenie, czy użytkownik jest zalogowany
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php"); // Przekierowanie do strony logowania, jeśli użytkownik nie jest zalogowany
    exit;
}

require_once '../../config/database.php'; // Dołączenie konfiguracji bazy danych
require_once '../models/patient.php'; // Dołączenie modelu pacjenta

$database = new Database();
$db = $database->getConnection();
$patient = new Patient($db);

// Inicjalizacja zmiennych do przechowywania haseł i błędów
$current_password = $new_password = $confirm_new_password = "";
$password_err = $new_password_err = $confirm_new_password_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Walidacja obecnego hasła
    if (empty(trim($_POST["current_password"]))) {
        $password_err = "Proszę podać aktualne hasło.";
    } else {
        $current_password = trim($_POST["current_password"]);
    }

    // Walidacja nowego hasła
    if (empty(trim($_POST["new_password"]))) {
        $password_err = "Proszę podać nowe hasło.";
    } elseif (strlen(trim($_POST["new_password"])) < 6) {
        $password_err = "Hasło musi mieć przynajmniej 6 znaków.";
    } else {
        $new_password = trim($_POST["new_password"]);
    }

    // Walidacja potwierdzenia nowego hasła
    if (empty(trim($_POST["confirm_new_password"]))) {
        $password_err = "Proszę potwierdzić nowe hasło.";
    } else {
        $confirm_new_password = trim($_POST["confirm_new_password"]);
        if (empty($password_err) && ($new_password != $confirm_new_password)) {
            $password_err = "Hasła nie pasują do siebie.";
        }
    }

    // Sprawdzenie, czy są błędy przed aktualizacją w bazie danych
    if (empty($password_err)) {
        // Próba zmiany hasła w bazie danych
        if ($patient->changePassword($_SESSION['patient_id'], $current_password, $new_password)) {
            // Informacja o sukcesie zmiany hasła
            $_SESSION['update_success'] = "Hasło zostało pomyślnie zmienione.";
            header("location: ../views/patient_panel.php");
            exit;
        } else {
            // Informacja o błędzie przy zmianie hasła
            $_SESSION['password_err'] = "Nieprawidłowe aktualne hasło.";
            header("location: ../views/patient_panel.php");
            exit;
        }
    } else {
        // Przekazanie informacji o błędach walidacji do sesji
        $_SESSION['password_err'] = $password_err;
        $_SESSION['new_password_err'] = $new_password_err;
        $_SESSION['confirm_new_password_err'] = $confirm_new_password_err;
        header("location: ../views/patient_panel.php");
        exit;
    }
}

// Zamykanie połączenia z bazą danych
unset($db);
