<?php
session_start();

// Dołączenie plików konfiguracyjnych bazy danych i modelu 'appointment'
require_once '../../config/database.php';
require_once '../models/appointment.php';

// Sprawdzenie, czy użytkownik jest zalogowany i ma odpowiednią rolę (np. dentysta)
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] != 'dentist') {
    // Jeśli użytkownik nie jest zalogowany lub nie ma odpowiedniej roli, zwróć błąd
    echo json_encode(["error" => "Nie masz uprawnień"]);
    exit;
}

// Utworzenie połączenia z bazą danych
$database = new Database();
$db = $database->getConnection();

// Inicjalizacja obiektu Appointment
$appointment = new Appointment($db);

// Pobieranie wizyt dla zalogowanego dentysty
$dentistAppointments = $appointment->getAppointmentsByDentist($_SESSION['user_id']);

// Zwrócenie danych wizyt w formacie JSON
echo json_encode($dentistAppointments);
