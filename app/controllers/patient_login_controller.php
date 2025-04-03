<?php
error_log("Formularz logowania został wysłany."); // Logowanie wysłania formularza
error_log("Email: " . $_POST['email']); // Logowanie adresu email

ini_set('display_errors', 1); // Włączenie wyświetlania błędów
ini_set('display_startup_errors', 1); // Włączenie wyświetlania błędów uruchamiania
error_reporting(E_ALL); // Ustawienie poziomu raportowania błędów

session_start(); // Rozpoczęcie nowej sesji lub wznowienie istniejącej

// Dołączenie plików konfiguracyjnych i modelu 'patient'
require_once '../../config/database.php';
require_once '../models/patient.php';

$database = new Database();
$db = $database->getConnection(); // Utworzenie połączenia z bazą danych

$email = $password = ""; // Inicjalizacja zmiennych
$email_err = $password_err = ""; // Inicjalizacja zmiennych błędów

// Przetwarzanie danych formularza po jego wysłaniu
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Walidacja emaila
    if (empty(trim($_POST["email"]))) {
        $email_err = "Proszę podać email.";
    } else {
        $email = trim($_POST["email"]);
    }

    // Walidacja hasła
    if (empty(trim($_POST["password"]))) {
        $password_err = "Proszę podać hasło.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Próba logowania po walidacji
    if (empty($email_err) && empty($password_err)) {
        $user = new Patient($db);
        if ($user->login($email, $password)) {
            // Przekierowanie do panelu pacjenta
            header("location: ../views/patient_panel.php");
            exit;
        } else {
            // Przekazanie błędu logowania do sesji i przekierowanie z powrotem do formularza logowania
            $_SESSION['login_err'] = "Niepoprawny email lub hasło.";
            header("location: ../views/patient_login.php");
            exit;
        }
    } else {
        // Przekazanie błędów walidacji do sesji
        $_SESSION['email_err'] = $email_err;
        $_SESSION['password_err'] = $password_err;
        header("location: ../views/patient_login.php");
        exit;
    }

    // Zamknięcie połączenia z bazą danych
    unset($db);
}
