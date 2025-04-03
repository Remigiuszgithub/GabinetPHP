<?php

class Availability
{
    private $conn; // Prywatna zmienna do przechowywania połączenia z bazą danych
    private $table_name = "availability"; // Nazwa tabeli w bazie danych

    // Publiczne zmienne reprezentujące atrybuty dostępności
    public $availability_id;
    public $dentist_id;
    public $start_time;
    public $end_time;

    // Konstruktor klasy
    public function __construct($db)
    {
        $this->conn = $db; // Przypisanie połączenia do zmiennej
    }

    // Dodawanie nowej dostępności
    public function create()
    {
        // Zapytanie SQL do wstawienia nowej dostępności do bazy danych
        $query = "INSERT INTO " . $this->table_name . " (dentist_id, start_time, end_time) VALUES (:dentist_id, :start_time, :end_time)";

        $stmt = $this->conn->prepare($query); // Przygotowanie zapytania

        // Oczyszczanie i przypisywanie danych
        $this->dentist_id = htmlspecialchars(strip_tags($this->dentist_id));
        $this->start_time = htmlspecialchars(strip_tags($this->start_time));
        $this->end_time = htmlspecialchars(strip_tags($this->end_time));

        // Przypisanie danych do zapytania
        $stmt->bindParam(':dentist_id', $this->dentist_id);
        $stmt->bindParam(':start_time', $this->start_time);
        $stmt->bindParam(':end_time', $this->end_time);

        // Wykonanie zapytania i logowanie
        if ($stmt->execute()) {
            error_log("Nowa dostępność została dodana: Dentysta ID " . $this->dentist_id . ", Czas rozpoczęcia: " . $this->start_time . ", Czas zakończenia: " . $this->end_time);
            return true;
        }

        return false;
    }

    // Aktualizacja istniejącej dostępności
    public function update()
    {
        // Zapytanie SQL do aktualizacji istniejącej dostępności
        $query = "UPDATE " . $this->table_name . " SET start_time = :start_time, end_time = :end_time WHERE availability_id = :availability_id";

        $stmt = $this->conn->prepare($query); // Przygotowanie zapytania

        // Oczyszczanie i przypisywanie danych
        $this->availability_id = htmlspecialchars(strip_tags($this->availability_id));
        $this->start_time = htmlspecialchars(strip_tags($this->start_time));
        $this->end_time = htmlspecialchars(strip_tags($this->end_time));

        // Przypisanie danych do zapytania
        $stmt->bindParam(':availability_id', $this->availability_id);
        $stmt->bindParam(':start_time', $this->start_time);
        $stmt->bindParam(':end_time', $this->end_time);

        // Wykonanie zapytania i logowanie
        if ($stmt->execute()) {
            error_log("Dostępność została zaktualizowana: Dentysta ID " . $this->dentist_id . ", Czas rozpoczęcia: " . $this->start_time . ", Czas zakończenia: " . $this->end_time);
            return true;
        }

        return false;
    }

    // Pobieranie wszystkich dostępności dla danego dentysty
    public function getAllAvailability($dentist_id)
    {
        // Pobieranie bieżącej daty i czasu
        $currentDateTime = date('Y-m-d H:i:s');

        // Zapytanie SQL do pobrania wszystkich dostępności dla danego dentysty
        $query = "SELECT * FROM " . $this->table_name . " WHERE dentist_id = :dentist_id AND end_time >= :currentDateTime ORDER BY start_time ASC";

        $stmt = $this->conn->prepare($query); // Przygotowanie zapytania
        $stmt->bindParam(':dentist_id', $dentist_id); // Przypisanie ID dentysty
        $stmt->bindParam(':currentDateTime', $currentDateTime); // Przypisanie bieżącej daty i czasu
        $stmt->execute(); // Wykonanie zapytania

        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Zwrócenie wyników w formie tablicy asocjacyjnej
    }

    // Pobieranie przyszłych dostępności dla danego dentysty
    public function getFutureAvailability()
    {
        // Pobieranie bieżącej daty i czasu
        $currentDate = date('Y-m-d H:i:s');

        // Zapytanie SQL do pobrania przyszłej dostępności
        $query = "SELECT a.availability_id, a.dentist_id, a.start_time, a.end_time, d.first_name, d.last_name 
          FROM " . $this->table_name . " a 
          JOIN dentists d ON a.dentist_id = d.dentist_id 
          WHERE a.start_time > :currentDate 
          ORDER BY a.start_time ASC";

        $stmt = $this->conn->prepare($query); // Przygotowanie zapytania
        $stmt->bindParam(':currentDate', $currentDate); // Przypisanie bieżącej daty
        $stmt->execute(); // Wykonanie zapytania

        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Zwrócenie wyników w formie tablicy asocjacyjnej
    }

    // Usuwanie dostępności danego dentysty
    public function delete()
    {
        // Zapytanie SQL do usunięcia dostępności
        $query = "DELETE FROM " . $this->table_name . " WHERE availability_id = :availability_id";

        $stmt = $this->conn->prepare($query); // Przygotowanie zapytania

        // Oczyszczanie i przypisanie ID dostępności
        $this->availability_id = htmlspecialchars(strip_tags($this->availability_id));
        $stmt->bindParam(':availability_id', $this->availability_id); // Przypisanie ID dostępności

        // Wykonanie zapytania i zwrócenie wyniku
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }


}
