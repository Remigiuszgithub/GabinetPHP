<?php
error_log("Formularz edycji danych dentysty został wysłany."); // Logowanie aktywności

// Rozpoczęcie sesji i dołączenie plików konfiguracyjnych
session_start();
require_once '../../config/database.php';
require_once '../models/dentist.php';

// Sprawdzenie uprawnień użytkownika
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== 'administrator') {
    header("location: ../views/dentist_login.php"); // Przekierowanie do strony logowania, jeśli użytkownik nie jest administratorem
    exit;
}

// Utworzenie połączenia z bazą danych
$database = new Database();
$db = $database->getConnection();
$dentist = new Dentist($db);

// Inicjalizacja zmiennych do przechowywania danych formularza
$firstName = $lastName = $email = $specialization = "";
$update_err = "";

// Obsługa żądania typu POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $dentist_id = isset($_POST['dentist_id']) ? trim($_POST['dentist_id']) : null; // Pobranie ID dentysty

    // Walidacja danych formularza
    // Walidacja imienia
    if (empty(trim($_POST["first_name"]))) {
        $update_err = "Proszę podać imię.";
    } else {
        $firstName = trim($_POST["first_name"]);
    }

    // Walidacja nazwiska
    if (empty(trim($_POST["last_name"]))) {
        $update_err .= "\nProszę podać nazwisko.";
    } else {
        $lastName = trim($_POST["last_name"]);
    }

    // Walidacja emaila
    if (empty(trim($_POST["email"]))) {
        $update_err .= "\nProszę podać email.";
    } else {
        $email = trim($_POST["email"]);
    }

    // Walidacja specjalizacji
    if (empty(trim($_POST["specialization"]))) {
        $update_err .= "\nProszę podać specjalizację.";
    } else {
        $specialization = trim($_POST["specialization"]);
    }

    // Jeśli nie ma błędów, przystąp do aktualizacji profilu
    if (empty($update_err)) {
        // Sprawdzenie, czy email jest już używany
        if (!empty($email) && $dentist->isEmailUsedByAnotherDentist($dentist_id, $email)) {
            $_SESSION['update_err'] = "Podany adres email jest używany przez innego lekarza.";
            header("location: ../views/admin_panel.php");
            exit;
        }

        // Próba aktualizacji profilu dentysty
        if ($dentist->updateProfile($dentist_id, $firstName, $lastName, $email, $specialization)) {
            $_SESSION['update_success'] = "Dane lekarza zostały pomyślnie zaktualizowane.";
            header("location: ../views/admin_panel.php");
            exit;
        } else {
            $_SESSION['update_err'] = "Wystąpił błąd podczas aktualizacji danych.";
            header("location: ../views/admin_panel.php");
            exit;
        }
    } else {
        // W przypadku błędów walidacji, przekazanie ich do sesji
        $_SESSION['update_err'] = $update_err;
        header("location: ../views/dentist_edit.php");
        exit;
    }
}

// Zamykanie połączenia z bazą danych
unset($db);
