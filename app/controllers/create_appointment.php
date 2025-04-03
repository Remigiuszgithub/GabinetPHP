<?php
// Rozpoczęcie nowej sesji lub wznowienie istniejącej
session_start();

// Wymagane pliki: konfiguracja bazy danych i model 'appointment'
require_once '../../config/database.php';
require_once '../models/appointment.php';

// Ustawienie nagłówka odpowiedzi na typ zawartości JSON
header('Content-Type: application/json');

// Sprawdzenie, czy metoda żądania to POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Odczytanie danych JSON przesłanych w żądaniu
    $data = json_decode(file_get_contents('php://input'), true);

    // Sprawdzenie, czy użytkownik jest zalogowany
    if (!isset($_SESSION['user_id'])) {
        // Jeśli nie, zwróć błąd
        echo json_encode(["status" => "error", "message" => "Musisz być zalogowany, aby dokonać rezerwacji."]);
        exit;
    }

    // Pobranie danych pacjenta, dentysty i daty wizyty z danych JSON
    $patient_id = $_SESSION['user_id'];
    $dentist_id = $data['dentist_id'] ?? null;
    $appointment_date = $data['appointment_date'] ?? null;
    if ($appointment_date) {
        $dateTime = new DateTime($appointment_date);
        $formattedDate = $dateTime->format('Y-m-d H:i:s');
        $appointment_date = $formattedDate;
    }

    try {
        // Utworzenie nowego połączenia z bazą danych
        $database = new Database();
        $db = $database->getConnection();

        // Utworzenie nowego obiektu Appointment i ustawienie jego właściwości
        $appointment = new Appointment($db);
        $appointment->patient_id = $patient_id;
        $appointment->dentist_id = $dentist_id;
        $appointment->appointment_date = $appointment_date;
        $appointment->status = 'scheduled';

        // Próba utworzenia nowej wizyty
        if ($appointment->create()) {
            // Zwrócenie pozytywnego komunikatu
            echo json_encode(["status" => "success", "message" => "Wizyta została pomyślnie zarezerwowana!"]);
        } else {
            // W przypadku niepowodzenia, zwrócenie błędu
            echo json_encode(["status" => "error", "message" => "Nie udało się zarezerwować wizyty."]);
        }
    } catch (PDOException $e) {
        // Logowanie wyjątku i zwrócenie komunikatu o błędzie
        error_log('Błąd przy tworzeniu wizyty: ' . $e->getMessage());
        echo json_encode(["status" => "error", "message" => "Wystąpił błąd przy rezerwacji wizyty."]);
    }
}
