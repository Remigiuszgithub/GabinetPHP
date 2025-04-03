<?php
session_start();

// Dołączenie plików konfiguracyjnych bazy danych i modelu 'appointment'
require_once '../../config/database.php';
require_once '../models/appointment.php';

if (!isset($_SESSION['user_id'])) {
    // Jeśli użytkownik nie jest zalogowany, zwróć błąd
    echo json_encode(["error" => "Nie jesteś zalogowany"]);
    exit;
}

// Utwórz nowy obiekt bazy danych i połączenie z bazą danych
$database = new Database();
$db = $database->getConnection();

// Utwórz nowy obiekt 'appointment'
$appointment = new Appointment($db);

// Pobierz wizyty dla zalogowanego pacjenta
$patientAppointments = $appointment->getPatientAppointments($_SESSION['user_id']);

// Zwróć wizyty w formacie JSON
echo json_encode($patientAppointments);
