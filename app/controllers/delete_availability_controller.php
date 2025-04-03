<?php
// Zainicjowanie sesji
session_start();

// Sprawdzenie czy użytkownik jest zalogowany, jeśli nie to przekierowanie do strony logowania
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== 'dentist') {
    header("location: dentist_login.php");
    exit;
}

// Sprawdzenie czy parametr availability_id został przekazany
if (isset($_GET['availability_id'])) {

    // Dołączenie pliku z konfiguracją połączenia do bazy danych
    require_once '../../config/database.php';

    // Dołączenie pliku availability.php zawierającego definicję klasy Availability
    require_once '../models/availability.php';

    // Utworzenie połączenia do bazy danych
    $database = new Database();
    $db = $database->getConnection();

    // Utworzenie obiektu klasy Availability
    $availability = new Availability($db);

    // Ustawienie availability_id obiektu klasy Availability na podstawie parametru availability_id przekazanego w adresie URL
    $availability->availability_id = $_GET['availability_id'];

    // Usunięcie dostępności
    if ($availability->delete()) {
        // Ustawienie komunikatu o sukcesie
        $_SESSION['success_message'] = "Dostępność została usunięta.";
    } else {
        // Ustawienie komunikatu o błędzie
        $_SESSION['error_message'] = "Nie udało się usunąć dostępności.";
    }
}

// Przekierowanie do strony z panelem dentysty
header("location: ../views/dentist_panel.php");
exit;
