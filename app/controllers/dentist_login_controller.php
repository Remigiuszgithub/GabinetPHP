<?php
error_log("Formularz logowania został wysłany."); // Logowanie próby logowania
error_log("Email: " . $_POST['email']); // Logowanie przesłanego adresu e-mail

// Ustawienia wyświetlania błędów PHP (przydatne podczas developmentu)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Dołączenie pliku konfiguracji bazy danych i klasy modelu 'dentist'
require_once '../../config/database.php';
require_once '../models/dentist.php';

// Utworzenie połączenia z bazą danych
$database = new Database();
$db = $database->getConnection();

// Funkcja do tworzenia domyślnego administratora (jeśli nie istnieje)
function createDefaultAdmin($db) {
    $query = "SELECT COUNT(*) FROM dentists WHERE role = 'administrator'"; // Zapytanie do sprawdzenia, czy jest już administrator
    $stmt = $db->prepare($query);
    $stmt->execute();
    $count = $stmt->fetchColumn();

    if ($count == 0) {
        // Jeżeli nie ma administratora, tworzymy domyślnego
        $admin = new Dentist($db);
        $admin->first_name = "Administrator";
        $admin->last_name = "Gabinetu";
        $admin->email = "administrator@gabinet.com"; // Domyślny email
        $admin->password = "54321!"; // Domyślne hasło
        $admin->specialization = "Brak";
        $admin->role = "administrator"; // Rola administratora

        // Tworzenie administratora
        if ($admin->create()) {
            error_log("Domyślny administrator został utworzony.");
        } else {
            error_log("Błąd przy tworzeniu domyślnego administratora.");
        }
    }
}

// Tworzenie domyślnego administratora, jeśli nie istnieje
createDefaultAdmin($db);

// Inicjalizacja zmiennych do przechowywania danych logowania i ewentualnych błędów
$email = $password = "";
$email_err = $password_err = "";

// Obsługa żądania typu POST
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

    // Jeśli nie ma błędów walidacji, przystąp do logowania
    if (empty($email_err) && empty($password_err)) {
        $user = new Dentist($db);
        // Próba logowania użytkownika
        if ($user->login($email, $password)) {
            // Sprawdzenie roli użytkownika i przekierowanie do odpowiedniego panelu
            if ($_SESSION["role"] == 'dentist') {
                header("location: ../views/dentist_panel.php");
                exit;
            } elseif ($_SESSION["role"] == 'administrator') {
                header("location: ../views/admin_panel.php");
                exit;
            } else {
                // Tutaj można dodać obsługę innych ról lub domyślne przekierowanie
            }
        } else {
            // Logowanie nieudane, ustawienie komunikatu o błędzie
            $_SESSION['login_err'] = "Niepoprawny email lub hasło.";
            header("location: ../views/dentist_login.php");
            exit;
        }
    } else {
        // W przypadku błędów walidacji, przekierowanie z powrotem do formularza logowania
        $_SESSION['email_err'] = $email_err;
        $_SESSION['password_err'] = $password_err;
        header("location: ../views/dentist_login.php");
        exit;
    }

    // Zamykanie połączenia z bazą danych
    unset($db);
}
?>
