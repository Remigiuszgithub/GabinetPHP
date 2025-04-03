<?php

// Klasa 'Appointment' odpowiada za obsługę wizyt
class Appointment
{
    private $conn; // Prywatna zmienna do przechowywania połączenia z bazą danych
    private $table_name = "appointments"; // Nazwa tabeli w bazie danych

    // Publiczne zmienne reprezentujące atrybuty wizyty
    public $appointment_id;
    public $patient_id;
    public $dentist_id;
    public $appointment_date;
    public $status;
    public $notes;

    // Konstruktor klasy
    public function __construct($db)
    {
        $this->conn = $db; // Przypisanie połączenia do zmiennej
    }

    // Funkcja do anulowania wizyty przez pacjenta
    public function cancelByPatient()
    {
        // Zapytanie SQL do aktualizacji statusu wizyty
        $query = "UPDATE " . $this->table_name . " SET status = 'cancelled_by_patient' WHERE appointment_id = :appointment_id";

        $stmt = $this->conn->prepare($query); // Przygotowanie zapytania
        $stmt->bindParam(":appointment_id", $this->appointment_id); // Przypisanie ID wizyty do zapytania

        // Wykonanie zapytania i zwrócenie wyniku
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Funkcja do anulowania wizyty przez lekarza
    public function cancelByDentist()
    {
        // Zapytanie SQL do aktualizacji statusu wizyty
        $query = "UPDATE " . $this->table_name . " SET status = 'cancelled_by_dentist' WHERE appointment_id = :appointment_id";

        $stmt = $this->conn->prepare($query); // Przygotowanie zapytania
        $stmt->bindParam(":appointment_id", $this->appointment_id); // Przypisanie ID wizyty do zapytania

        // Wykonanie zapytania i zwrócenie wyniku
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Funkcja do tworzenia nowej wizyty
    public function create()
    {
        // Zapytanie SQL do wstawienia nowej wizyty do bazy danych
        $query = "INSERT INTO " . $this->table_name . " (patient_id, dentist_id, appointment_date, status) VALUES (:patient_id, :dentist_id, :appointment_date, :status)";

        $stmt = $this->conn->prepare($query); // Przygotowanie zapytania

        // Oczyszczenie i przypisanie wartości
        $this->patient_id = htmlspecialchars(strip_tags($this->patient_id));
        $this->dentist_id = htmlspecialchars(strip_tags($this->dentist_id));
        $this->appointment_date = htmlspecialchars(strip_tags($this->appointment_date));
        $this->status = htmlspecialchars(strip_tags($this->status));

        // Przypisanie parametrów do zapytania
        $stmt->bindParam(":patient_id", $this->patient_id);
        $stmt->bindParam(":dentist_id", $this->dentist_id);
        $stmt->bindParam(":appointment_date", $this->appointment_date);
        $stmt->bindParam(":status", $this->status);

        // Wykonanie zapytania i logowanie
        if ($stmt->execute()) {
            error_log("Umówiono wizytę: Pacjent ID " . $this->patient_id . ", Dentysta ID " . $this->dentist_id . ", Data: " . $this->appointment_date);
            return true;
        }

        return false;
    }

    // funkcja do pobrania przyszłych wizyt
    public function getFutureAppointments()
    {
        $currentDate = date('Y-m-d H:i:s'); // Pobranie bieżącej daty i czasu

        // Zapytanie SQL do pobrania przyszłych wizyt
        $query = "SELECT * FROM " . $this->table_name . " WHERE appointment_date >= :currentDate ORDER BY appointment_date ASC";

        $stmt = $this->conn->prepare($query); // Przygotowanie zapytania
        $stmt->bindParam(":currentDate", $currentDate); // Przypisanie bieżącej daty do zapytania

        $stmt->execute(); // Wykonanie zapytania
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Zwrócenie wyników w formie tablicy asocjacyjnej
    }

    // Funkcja do pobrania wizyt danego pacjenta
    public function getPatientAppointments($patient_id)
    {
        // Zapytanie SQL do pobrania wizyt pacjenta
        $query = "SELECT a.appointment_id, a.appointment_date, a.status, d.first_name, d.last_name 
          FROM appointments a 
          JOIN dentists d ON a.dentist_id = d.dentist_id 
          WHERE a.patient_id = :patient_id
          ORDER BY a.appointment_date ASC";

        $stmt = $this->conn->prepare($query); // Przygotowanie zapytania
        $stmt->bindParam(':patient_id', $patient_id); // Przypisanie ID pacjenta do zapytania
        $stmt->execute(); // Wykonanie zapytania

        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Zwrócenie wyników w formie tablicy asocjacyjnej
    }

    // Funkcja do pobrania wizyt danego dentysty
    public function getAppointmentsByDentist($dentistId)
    {
        // Zapytanie SQL do pobrania wizyt danego dentysty
        $query = "SELECT a.appointment_id, a.appointment_date, a.status, p.first_name, p.last_name 
          FROM appointments a 
          JOIN patients p ON a.patient_id = p.patient_id 
          WHERE a.dentist_id = :dentistId
          ORDER BY a.appointment_date ASC";

        $stmt = $this->conn->prepare($query); // Przygotowanie zapytania
        $stmt->bindParam(':dentistId', $dentistId); // Przypisanie ID dentysty do zapytania
        $stmt->execute(); // Wykonanie zapytania

        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Zwrócenie wyników w formie tablicy asocjacyjnej
    }

    // Funkcja do zmiany statusu wizyty
    public function changeStatus($appointmentId, $newStatus)
    {
        // Zapytanie SQL do zmiany statusu wizyty
        $sql = "UPDATE appointments SET status = :newStatus WHERE appointment_id = :appointmentId";
        $stmt = $this->conn->prepare($sql); // Przygotowanie zapytania
        $stmt->bindParam(':newStatus', $newStatus); // Przypisanie nowego statusu
        $stmt->bindParam(':appointmentId', $appointmentId); // Przypisanie ID wizyty
        $stmt->execute(); // Wykonanie zapytania

        return $stmt->rowCount() > 0; // Zwrócenie true, jeśli zmieniono rekordy
    }

    // Funkcja, która zmienia status wizyt z 'zaplanowany' na 'zakończony'
    public function updateStatusToCompleted()
    {
        // Ustalenie czasu, aby oznaczyć wizyty zakończone godzinę temu
        $currentTime = new DateTime();
        $currentTime->modify('-1 hour');
        $formattedCurrentTime = $currentTime->format('Y-m-d H:i:s');

        // Zapytanie SQL do aktualizacji statusu wizyt na 'zakończony'
        $sql = "UPDATE appointments 
            SET status = 'completed' 
            WHERE status = 'scheduled' AND appointment_date <= :currentTime";

        $stmt = $this->conn->prepare($sql); // Przygotowanie zapytania
        $stmt->bindParam(':currentTime', $formattedCurrentTime, PDO::PARAM_STR); // Przypisanie sformatowanego czasu
        $stmt->execute(); // Wykonanie zapytania

        return $stmt->rowCount(); // Zwrócenie liczby zaktualizowanych rekordów
    }
}