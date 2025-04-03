<?php
session_start();

// Sprawdzenie, czy użytkownik jest zalogowany jako dentysta
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== 'dentist') {
    header("location: dentist_login.php"); // Przekierowanie do strony logowania dentysty, jeśli użytkownik nie ma uprawnień
    exit;
}

// Dołączenie plików konfiguracyjnych i modelu dostępności
require_once '../../config/database.php';
require_once '../models/availability.php';

// Utworzenie połączenia z bazą danych
$database = new Database();
$db = $database->getConnection();
$availability = new Availability($db);

// Inicjalizacja zmiennych do przechowywania danych i ewentualnych błędów
$start_time = $end_time = "";
$start_time_err = $end_time_err = "";

// Obsługa żądania typu POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Pobieranie danych z formularza i usuwanie zbędnych spacji
    $dentist_id = $_SESSION['user_id'];
    $start_time = trim($_POST["start_time"]);
    $end_time = trim($_POST["end_time"]);

    // Walidacja czasów startu i końca
    if (empty($start_time)) {
        $start_time_err = "Proszę podać czas rozpoczęcia.";
    }

    if (empty($end_time)) {
        $end_time_err = "Proszę podać czas zakończenia.";
    }

    // Sprawdzenie, czy czas zakończenia jest późniejszy niż czas rozpoczęcia
    if (!empty($start_time) && !empty($end_time) && strtotime($start_time) >= strtotime($end_time)) {
        $end_time_err = "Czas zakończenia musi być późniejszy niż czas rozpoczęcia.";
    }

    // Sprawdzenie, czy podany czas rozpoczęcia nie jest w przeszłości
    $currentDateTime = date('Y-m-d H:i:s');
    if (!empty($start_time) && strtotime($start_time) < strtotime($currentDateTime)) {
        $start_time_err = "Czas rozpoczęcia nie może być w przeszłości.";
    }

    // Jeśli nie ma błędów, przystąp do tworzenia lub aktualizacji dostępności
    if (empty($start_time_err) && empty($end_time_err)) {
        $availability->dentist_id = $dentist_id;
        $availability->start_time = $start_time;
        $availability->end_time = $end_time;

        // Sprawdzenie, czy to aktualizacja istniejącej dostępności
        if (isset($_POST['availability_id']) && !empty($_POST['availability_id'])) {
            $availability->availability_id = $_POST['availability_id'];
            $success = $availability->update();
        } else {
            // Jeśli nie, to utwórz nową dostępność
            $success = $availability->create();
        }

        // Obsługa odpowiedzi po próbie utworzenia lub aktualizacji
        if ($success) {
            $_SESSION['success_message'] = "Dostępność została pomyślnie zaktualizowana!";
            header("location: ../views/dentist_panel.php");
            exit;
        } else {
            echo "Wystąpił błąd podczas aktualizacji dostępności.";
        }
    } else {
        // Jeśli wystąpiły błędy walidacji, ustaw odpowiednie komunikaty błędów
        $_SESSION['start_time_err'] = $start_time_err;
        $_SESSION['end_time_err'] = $end_time_err;
        header("location: ../views/dentist_panel.php");
        exit;
    }
}

// Zwolnienie zasobu połączenia z bazą danych
unset($db);
