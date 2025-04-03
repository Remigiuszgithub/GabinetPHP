<?php
session_start();

// Dołączenie plików konfiguracyjnych bazy danych i modelu 'availability'
require_once '../../config/database.php';
require_once '../models/availability.php';

// Sprawdzenie, czy użytkownik jest zalogowany i ma uprawnienia dentysty
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== 'dentist') {
    header("location: dentist_login.php"); // Przekierowanie do strony logowania, jeśli nie ma uprawnień
    exit;
}

// Utworzenie połączenia z bazą danych
$database = new Database();
$db = $database->getConnection();
$availability = new Availability($db);

// Pobranie danych dostępności dla zalogowanego dentysty
$data = $availability->getAllAvailability($_SESSION['user_id']);

// Ustawienie nagłówków HTTP dla eksportu danych w formacie CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="dostepnosc.csv"');

// Otworzenie strumienia wyjściowego dla pliku CSV
$output = fopen('php://output', 'w');

// Definiowanie i zapis nagłówków kolumn w pliku CSV
fputcsv($output, array('ID Dostępności', 'Dentysta ID', 'Czas Rozpoczęcia', 'Czas Zakończenia'));

// Iteracja przez dane i zapis każdego wiersza do pliku CSV
foreach ($data as $row) {
    fputcsv($output, $row); // Zapis wiersza danych do pliku CSV
}

// Zamknięcie strumienia wyjściowego
fclose($output);
exit; // Zakończenie skryptu
