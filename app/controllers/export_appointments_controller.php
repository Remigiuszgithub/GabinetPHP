<?php
session_start();

// Dołączenie plików konfiguracji bazy danych i modelu 'appointment'
require_once '../../config/database.php';
require_once '../models/appointment.php';

// Sprawdzenie, czy użytkownik jest zalogowany i ma uprawnienia dentysty
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== 'dentist') {
    header("location: dentist_login.php"); // Przekierowanie do strony logowania, jeśli nie ma uprawnień
    exit;
}

// Utworzenie połączenia z bazą danych
$database = new Database();
$db = $database->getConnection();
$appointments = new Appointment($db);

// Pobieranie danych wizyt dla zalogowanego dentysty
$data = $appointments->getAppointmentsByDentist($_SESSION['user_id']);

// Ustawienie nagłówków HTTP do eksportu danych w formacie CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="wizyty.csv"');

// Otworzenie strumienia wyjściowego dla pliku CSV
$output = fopen('php://output', 'w');

// Definiowanie i zapis nagłówków kolumn w pliku CSV
fputcsv($output, array('ID wizyty', 'Data i czas', 'Status', 'Imię', 'Nazwisko'));

// Iteracja przez dane i zapis każdego wiersza do pliku CSV
foreach ($data as $row) {
    fputcsv($output, $row); // Zapis wiersza danych
}

// Zamknięcie strumienia wyjściowego
fclose($output);
exit; // Zakończenie skryptu
