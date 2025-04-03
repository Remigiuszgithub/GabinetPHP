<?php
session_start();

// Wczytanie konfiguracji bazy danych i modelu appointment
require_once '../../config/database.php';
require_once '../models/appointment.php';

// Sprawdzenie, czy żądanie jest typu POST i czy zawiera ID wizyty
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['appointment_id'])) {
    $appointment_id = $_POST['appointment_id']; // Pobranie ID wizyty z danych POST

    // Utworzenie połączenia z bazą danych
    $database = new Database();
    $db = $database->getConnection();

    // Utworzenie obiektu appointment i przypisanie mu ID wizyty
    $appointment = new Appointment($db);
    $appointment->appointment_id = $appointment_id;

    // Próba odwołania wizyty przez lekarza
    if ($appointment->cancelByDentist()) {
        // Jeśli się powiedzie, zwróć komunikat o sukcesie
        echo json_encode(["message" => "Zaplanowana wizyta została odwołana"]);
    } else {
        // W przeciwnym przypadku, zwróć komunikat o błędzie
        echo json_encode(["message" => "Wystąpił błąd. Nie udało się odwołać wizyty"]);
    }
}
