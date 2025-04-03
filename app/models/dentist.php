<?php

class Dentist
{
    private $db; // Prywatna zmienna do przechowywania połączenia z bazą danych
    private $table_name = "dentists"; // Nazwa tabeli w bazie danych

    // Atrybuty klasy odpowiadające kolumnom w tabeli 'dentists'
    public $dentist_id;
    public $email;
    public $password;
    public $first_name;
    public $last_name;
    public $specialization;
    public $role;

    // Konstruktor z połączeniem do bazy danych
    public function __construct($db)
    {
        $this->db = $db;
    }
    
    // Funkcja do tworzenia nowego dentysty
    public function create()
    {
        // Zapytanie SQL do wstawienia nowego rekordu
        $query = "INSERT INTO " . $this->table_name . "
              SET first_name=:first_name, last_name=:last_name, email=:email, password=:password, specialization=:specialization";

        // Przygotowanie zapytania
        $stmt = $this->db->prepare($query);

        // Oczyszczenie i bindowanie danych
        $this->first_name = htmlspecialchars(strip_tags($this->first_name));
        $this->last_name = htmlspecialchars(strip_tags($this->last_name));
        $this->email = htmlspecialchars(strip_tags($this->email));
        // Hashowanie hasła
        $this->password = password_hash($this->password, PASSWORD_DEFAULT);
        $this->specialization = htmlspecialchars(strip_tags($this->specialization));

        // Bindowanie zmiennych
        $stmt->bindParam(":first_name", $this->first_name);
        $stmt->bindParam(":last_name", $this->last_name);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $this->password);
        $stmt->bindParam(":specialization", $this->specialization);

        // Wykonanie zapytania
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Funkcja, która odpowiada za logowanie dentysty
    public function login($email, $password)
    {
        // Zapytanie SQL do pobrania informacji o dentystach na podstawie emaila
        $query = "SELECT dentist_id, first_name, last_name, email, password, role FROM " . $this->table_name . " WHERE email = :email";

        $stmt = $this->db->prepare($query); // Przygotowanie zapytania
        $email = htmlspecialchars(strip_tags($email)); // Oczyszczenie i escapowanie emaila
        $stmt->bindParam(':email', $email); // Przypisanie emaila do zapytania

        $stmt->execute(); // Wykonanie zapytania

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC); // Pobranie wyników

            // Przypisanie danych do zmiennych
            $dentist_id = $row['dentist_id'];
            $hashed_password = $row['password'];
            $first_name = $row['first_name'];
            $last_name = $row['last_name'];
            $role = $row['role'];

            // Weryfikacja hasła
            if (password_verify($password, $hashed_password)) {
                // Ustawienie zmiennych sesji
                $_SESSION["loggedin"] = true;
                $_SESSION["user_id"] = $dentist_id;
                $_SESSION["email"] = $email;
                $_SESSION["first_name"] = $first_name;
                $_SESSION["last_name"] = $last_name;
                $_SESSION["role"] = $role;

                return true;
            } else {
                return false; // Błędne hasło
            }
        } else {
            return false; // Brak dentysty o podanym emailu
        }
    }

    // Funkcja, która pobiera informacje o dentyście na podstawie ID
    public function getDentistById($dentist_id)
    {
        // Zapytanie SQL do pobrania informacji o dentystach na podstawie ID
        $query = "SELECT * FROM dentists WHERE dentist_id = :dentist_id";

        $stmt = $this->db->prepare($query); // Przygotowanie zapytania
        $stmt->bindParam(':dentist_id', $dentist_id); // Przypisanie ID dentysty do zapytania
        $stmt->execute(); // Wykonanie zapytania

        if ($stmt->rowCount() == 1) {
            return $stmt->fetch(PDO::FETCH_ASSOC); // Zwrócenie informacji o dentyście
        } else {
            return false; // Brak dentysty o podanym ID
        }
    }

    // Funkcja, która informacje o wszystkich dentystach
    public function readAll()
    {
        // Zapytanie SQL do pobrania wszystkich dentystów
        $query = "SELECT dentist_id, first_name, last_name, email, role, specialization FROM " . $this->table_name . " ORDER BY last_name, first_name";

        // Przygotowanie zapytania
        $stmt = $this->db->prepare($query);

        // Wykonanie zapytania
        $stmt->execute();

        return $stmt; // Zwrócenie wyników zapytania
    }

    // Funkcja, która aktualizuje dane dentysty
    public function updateProfile($dentistId, $firstName, $lastName, $email, $specialization)
    {
        // Sprawdzenie, czy email jest już używany przez innego lekarza
        if ($this->isEmailUsedByAnotherDentist($dentistId, $email)) {
            return false; // Jeśli tak, zwróć false
        }

        // Zapytanie SQL do aktualizacji profilu dentysty
        $query = "UPDATE " . $this->table_name . " 
          SET first_name = :first_name, 
              last_name = :last_name, 
              email = :email,
              specialization = :specialization
          WHERE dentist_id = :dentist_id";

        // Przygotowanie zapytania
        $stmt = $this->db->prepare($query);
        // Oczyszczanie i przypisywanie danych
        $firstName = htmlspecialchars(strip_tags($firstName));
        $lastName = htmlspecialchars(strip_tags($lastName));
        $email = htmlspecialchars(strip_tags($email));
        // Przypisywanie danych do zapytania
        $stmt->bindParam(':first_name', $firstName);
        $stmt->bindParam(':last_name', $lastName);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':specialization', $specialization);
        $stmt->bindParam(':dentist_id', $dentistId);

        // Wykonanie zapytania
        if ($stmt->execute()) {
            return true; // Powodzenie aktualizacji
        } else {
            return false; // Niepowodzenie aktualizacji
        }
    }



    // Funkcja, która usuwa lekarza z bazy danych
    public function delete($dentistId)
    {
        // Zapytanie SQL do usunięcia dentysty
        $query = "DELETE FROM " . $this->table_name . " WHERE dentist_id = :dentist_id";

        // Przygotowanie zapytania
        $stmt = $this->db->prepare($query);

        // Oczyszczenie i bindowanie danych
        $this->dentist_id = htmlspecialchars(strip_tags($dentistId));
        $stmt->bindParam(
            ':dentist_id',
            $this->dentist_id
        );

        // Wykonanie zapytania
        if ($stmt->execute()) {
            return true; // Powodzenie aktualizacji
        }
        return false; // Niepowodzenie aktualizacji
    }

    // Funkcja sprawdzająca czy dany dentysta ma rolę administrator
    public function isAdministrator($dentist_id)
    {
        $query = "SELECT role FROM " . $this->table_name . " WHERE dentist_id = :dentist_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':dentist_id', $dentist_id);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row['role'] === 'administrator') {
                return true;
            }
        }
        return false;
    }

    // Funkcja sprawdzająca czy podany email istnieje w bazie danych
    public function isEmailExists($email)
    {
        // Zapytanie SQL do sprawdzenia, czy email istnieje
        $query = "SELECT COUNT(*) FROM " . $this->table_name . " WHERE email = :email";
        // Przygotowanie zapytania
        $stmt = $this->db->prepare($query);

        // Oczyszczenie i bindowanie danych
        $stmt->bindParam(":email", $email);
        $stmt->execute(); // Wykonanie zapytania

        // Pobranie liczby wierszy
        if ($stmt->fetchColumn() > 0) {
            return true; // Email już istnieje
        } else {
            return false; // Email nie istnieje
        }
    }

    // Funkcja sprawdzająca czy podany email jest używany przez innego lekarza
    public function isEmailUsedByAnotherDentist($dentistId, $email)
    {
        // Zapytanie SQL do sprawdzenia, czy email jest używany przez innego lekarza
        $query = "SELECT COUNT(*) FROM " . $this->table_name . " 
                  WHERE email = :email AND dentist_id != :dentist_id";

        // Przygotowanie zapytania
        $stmt = $this->db->prepare($query);
        // Oczyszczenie i bindowanie danych
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':dentist_id', $dentistId);

        // Wykonanie zapytania
        $stmt->execute();
        // Pobranie liczby wierszy
        $count = $stmt->fetchColumn();

        // Jeśli liczba wierszy jest większa od 0, to znaczy, że email jest używany przez innego lekarza
        return $count > 0;
    }
}
