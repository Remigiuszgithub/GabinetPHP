<?php
session_start(); // Rozpoczęcie sesji

require_once '../../config/database.php'; // Dołączenie pliku konfiguracji bazy danych
require_once '../models/appointment.php'; // Dołączenie modelu 'appointment'

// Sprawdzenie, czy żądanie jest typu POST i czy ID wizyty zostało przesłane
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['appointment_id'])) {
    $appointment_id = $_POST['appointment_id']; // Pobranie ID wizyty z formularza

    $database = new Database();
    $db = $database->getConnection(); // Utworzenie połączenia z bazą danych

    $appointment = new Appointment($db); // Utworzenie obiektu Appointment
    $appointment->appointment_id = $appointment_id; // Przypisanie ID wizyty do obiektu

    // Próba anulowania wizyty przez pacjenta
    if ($appointment->cancelByPatient()) {
        // Jeśli anulowanie się powiedzie, zwróć informację o sukcesie
        echo json_encode(["message" => "Twoja wizyta została anulowana"]);
    } else {
        // W przypadku niepowodzenia, zwróć informację o błędzie
        echo json_encode(["message" => "Nie udało się anulować wizyty"]);
    }
}
