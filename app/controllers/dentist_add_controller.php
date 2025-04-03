<?php
session_start();

// Sprawdzenie, czy użytkownik jest zalogowany jako administrator
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== 'administrator') {
    header("location: login.php"); // Przekierowanie do strony logowania, jeśli użytkownik nie ma uprawnień
    exit;
}

// Dołączenie plików konfiguracyjnych i modeli
require_once '../../config/database.php';
require_once '../models/dentist.php';

// Utworzenie połączenia z bazą danych
$database = new Database();
$db = $database->getConnection();
$dentist = new Dentist($db);

// Inicjalizacja zmiennych do przechowywania danych i ewentualnych błędów
$first_name = $last_name = $email = $password = $specialization = "";
$first_name_err = $last_name_err = $email_err = $password_err = $specialization_err = "";

// Obsługa żądania typu POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Pobieranie danych z formularza i usuwanie zbędnych spacji
    $first_name = trim($_POST["first_name"]);
    $last_name = trim($_POST["last_name"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $specialization = trim($_POST["specialization"]);

    // Walidacja adresu e-mail
    if ($dentist->isEmailExists($email)) {
        $email_err = "Adres email jest już używany."; // Ustawienie błędu, jeśli email już istnieje
    }

    // Sprawdzenie, czy nie ma błędów walidacji
    if (empty($first_name_err) && empty($last_name_err) && empty($email_err) && empty($password_err) && empty($specialization_err)) {
        // Ustawienie danych dentysty
        $dentist->first_name = $first_name;
        $dentist->last_name = $last_name;
        $dentist->email = $email;
        $dentist->password = $password;
        $dentist->specialization = $specialization;

        // Próba utworzenia nowego rekordu dentysty
        if ($dentist->create()) {
            $_SESSION['success_message'] = "Pomyślnie dodano nowego lekarza!";
            header("location: ../views/admin_panel.php"); // Przekierowanie do panelu administracyjnego
            exit;
        } else {
            echo "Wystąpił błąd podczas dodawania lekarza."; // Wyświetlenie błędu, jeśli nie uda się dodać dentysty
        }
    } else {
        $_SESSION['error_message'] = "Nie udało się dodać lekarza. " . $email_err; // Ustawienie komunikatu o błędzie
        header("location: ../views/admin_panel.php"); // Przekierowanie do panelu administracyjnego
        exit;
    }
}
