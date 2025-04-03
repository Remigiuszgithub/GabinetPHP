<?php
// Rozpoczęcie nowej sesji lub wznowienie istniejącej
session_start();

// Wymagane pliki: konfiguracja bazy danych i model 'appointment'
require_once '../../config/database.php';
require_once '../models/appointment.php';

// Sprawdzenie, czy metoda żądania to POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sprawdzenie, czy użytkownik jest zalogowany i ma rolę 'dentist'
    if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== 'dentist') {
        // Jeśli nie, zwróć błąd
        echo json_encode(["error" => "Nieautoryzowany dostęp"]);
        exit;
    }

    // Pobranie ID wizyty i nowego statusu z danych POST
    $appointmentId = $_POST['appointment_id'];
    $newStatus = $_POST['new_status'];

    // Utworzenie nowego połączenia z bazą danych
    $database = new Database();
    $db = $database->getConnection();

    // Utworzenie nowego obiektu Appointment
    $appointment = new Appointment($db);

    // Próba zmiany statusu wizyty
    $result = $appointment->changeStatus($appointmentId, $newStatus);

    // Sprawdzenie, czy operacja się powiodła
    if ($result) {
        // Jeśli tak, zwróć wiadomość o sukcesie
        echo json_encode(['success' => true, 'message' => "Status wizyty został zmieniony."]);
    } else {
        // Jeśli nie, zwróć błąd
        echo json_encode(['error' => 'Nie udało się zmienić statusu wizyty']);
    }
} else {
    // Jeśli metoda żądania nie jest POST, zwrócenie błędu
    echo json_encode(['error' => 'Nieobsługiwana metoda żądania']);
}
