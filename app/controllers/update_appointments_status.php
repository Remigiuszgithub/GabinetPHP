<?php
session_start(); // Rozpoczęcie sesji

require_once '../../config/database.php'; // Dołączenie konfiguracji bazy danych
require_once '../models/appointment.php'; // Dołączenie modelu 'appointment'

// Sprawdzenie, czy metoda żądania to POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Sprawdzenie, czy użytkownik jest zalogowany i ma odpowiednią rolę (dentysta)
    if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== 'dentist') {
        echo json_encode(["error" => "Nieautoryzowany dostęp"]); // Zwrócenie błędu w przypadku braku uprawnień
        exit;
    }

    $database = new Database();
    $db = $database->getConnection(); // Utworzenie połączenia z bazą danych

    $appointments = new Appointment($db);

    // Aktualizacja statusu wizyt na 'zakończony'
    $updatedRows = $appointments->updateStatusToCompleted();

    // Zwrócenie informacji o liczbie zaktualizowanych wizyt
    echo json_encode(['success' => true, 'message' => "Zaktualizowano status dla $updatedRows wizyt."]);
} else {
    // Zwrócenie błędu, gdy metoda żądania nie jest POST
    echo json_encode(['error' => 'Nieobsługiwana metoda żądania']);
}
