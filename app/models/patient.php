<?php

// Klasa 'Patient' odpowiada za obsługę pacjentów
class Patient
{
    private $db; // Prywatna zmienna do przechowywania połączenia z bazą danych
    private $table_name = "patients"; // Nazwa tabeli w bazie danych

    // Konsktruktor klasy
    public function __construct($db)
    {
        $this->db = $db; // Przypisanie połączenia do zmiennej
    }

    // Funkcja, która logiuje pacjenta
    public function addNewPatient($firstName, $lastName, $email, $password)
    {
        // Sprawdzenie, czy email już istnieje
        if ($this->isEmailExists($email)) {
            error_log("Email " . $email . " już istnieje w bazie danych.");
            return false; // Adres email już istnieje
        }

        // Zapytanie SQL do wstawienia danych
        $query = "INSERT INTO " . $this->table_name . " (first_name, last_name, email, password) VALUES (:first_name, :last_name, :email, :password)";

        // Przygotowanie zapytania
        $stmt = $this->db->prepare($query);

        // Oczyszczenie i bindowanie
        $firstName = htmlspecialchars(strip_tags($firstName));
        $lastName = htmlspecialchars(strip_tags($lastName));
        $email = htmlspecialchars(strip_tags($email));
        $passwordHashed = password_hash($password, PASSWORD_DEFAULT); // Hashowanie hasła

        // Przypisanie wartości do zapytania
        $stmt->bindParam(':first_name', $firstName);
        $stmt->bindParam(':last_name', $lastName);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $passwordHashed);

        // Wykonanie zapytania
        if ($stmt->execute()) {
            error_log("Pacjent " . $firstName . " " . $lastName . " został dodany do bazy danych.");
            return true; // Powodzenie dodania
        } else {
            error_log("Błąd dodawania pacjenta: " . implode(";", $stmt->errorInfo()));
            return false; // Niepowodzenie dodania
        }
    }

    // Funkcja, która logiuje pacjenta
    public function login($email, $password)
    {
        // Zapytanie SQL do pobrania informacji o pacjentach na podstawie emaila
        $query = "SELECT patient_id, first_name, last_name, email, password, role FROM " . $this->table_name . " WHERE email = :email";

        // Przygotowanie zapytania
        $stmt = $this->db->prepare($query);

        // Oczyszczenie i bindowanie
        $email = htmlspecialchars(strip_tags($email));
        $stmt->bindParam(':email', $email);

        $stmt->execute(); // Wykonanie zapytania

        // Sprawdzenie, czy znaleziono więcej niż jeden rekord
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC); // Pobranie wiersza z bazy danych i zapisanie go w tablicy asocjacyjnej

            // Przypisanie wartości do zmiennych
            $patient_id = $row['patient_id'];
            $hashed_password = $row['password'];
            $first_name = $row['first_name'];
            $last_name = $row['last_name'];
            $role = $row['role'];

            // Sprawdzenie, czy hasło jest prawidłowe i przypisanie wartości do zmiennych sesji
            if (password_verify($password, $hashed_password)) {
                $_SESSION["loggedin"] = true;
                $_SESSION["user_id"] = $patient_id;
                $_SESSION["email"] = $email;
                $_SESSION["first_name"] = $first_name;
                $_SESSION["last_name"] = $last_name;
                $_SESSION["role"] = $role;

                return true; // Zalogowano pomyślnie
            } else {
                return false; // Nieprawidłowe hasło
            }
        } else {
            return false; // Nie znaleziono użytkownika o podanym adresie email
        }
    }

    // Funkcja, która sprawdza czy email jest już używany
    public function isEmailExists($email)
    {
        // Zapytanie SQL do sprawdzenia, czy email jest już używany
        $query = "SELECT COUNT(*) FROM " . $this->table_name . " WHERE email = :email";

        // Przygotowanie zapytania
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':email', $email);

        // Wykonanie zapytania
        $stmt->execute();
        $count = $stmt->fetchColumn();

        // Jeśli count > 0, email już istnieje
        return $count > 0;
    }

    // Funkcja, która sprawdza czy email jest już używany przez innego pacjenta
    public function isEmailUsedByAnotherPatient($patientId, $email)
    {
        // Zapytanie SQL do sprawdzenia, czy email jest już używany przez innego pacjenta
        $query = "SELECT COUNT(*) FROM " . $this->table_name . " 
                  WHERE email = :email AND patient_id != :patient_id";

        // Przygotowanie zapytania
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':patient_id', $patientId);

        // Wykonanie zapytania
        $stmt->execute();
        $count = $stmt->fetchColumn();

        // Sprawdzenie, czy znaleziono więcej niż jeden rekord
        return $count > 0;
    }

    // Funkcja, która aktualizuje dane osobowe pacjenta
    public function updateProfile($patientId, $firstName, $lastName, $email)
    {
        // Sprawdzenie, czy email jest już używany przez innego pacjenta
        if ($this->isEmailUsedByAnotherPatient($patientId, $email)) {
            error_log("Podany adres email jest używany."); // Zapisanie informacji o błędzie w pliku error.log
            return false; // Email jest już używany
        }

        // Zapytanie SQL do aktualizacji danych osobowych pacjenta
        $query = "UPDATE " . $this->table_name . " 
                  SET first_name = :first_name, 
                      last_name = :last_name, 
                      email = :email 
                  WHERE patient_id = :patient_id";

        // Przygotowanie zapytania
        $stmt = $this->db->prepare($query);

        // Oczyszczenie i bindowanie
        $firstName = htmlspecialchars(strip_tags($firstName));
        $lastName = htmlspecialchars(strip_tags($lastName));
        $email = htmlspecialchars(strip_tags($email));
        $patientId = htmlspecialchars(strip_tags($patientId));

        // Przypisanie danych do zapytania
        $stmt->bindParam(':first_name', $firstName);
        $stmt->bindParam(':last_name', $lastName);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':patient_id', $patientId);

        // Wykonanie zapytania
        if ($stmt->execute()) {
            error_log("Pacjent " . $_SESSION['first_name'] . " " . $_SESSION['last_name'] . " - zaktualizował dane osobowe.");
            return true; // Powodzenie aktualizacji
        } else {
            error_log("Błąd aktualizacji danych: " . implode(";", $stmt->errorInfo()));
            return false; // Niepowodzenie aktualizacji
        }
    }

    // Funkcja, która aktualizuje hasło pacjenta
    public function changePassword($patientId, $currentPassword, $newPassword)
    {
        // Pobranie aktualnego hasła użytkownika
        $query = "SELECT password FROM " . $this->table_name . " WHERE patient_id = :patient_id";
        $stmt = $this->db->prepare($query); // Przygotowanie zapytania
        $stmt->bindParam(':patient_id', $patientId); // Przypisanie ID pacjenta do zapytania
        $stmt->execute(); // Wykonanie zapytania

        // Sprawdzenie, czy znaleziono więcej niż jeden rekord
        if ($stmt->rowCount() == 1) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC); // Pobranie wiersza z bazy danych i zapisanie go w tablicy asocjacyjnej
            $hashed_password = $row['password']; // Przypisanie hasła do zmiennej

            // Sprawdzenie, czy aktualne hasło jest prawidłowe
            if (password_verify($currentPassword, $hashed_password)) {
                // Aktualizacja hasła
                $newHashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

                // Zapytanie SQL do aktualizacji hasła
                $updateQuery = "UPDATE " . $this->table_name . " SET password = :new_password WHERE patient_id = :patient_id";
                $updateStmt = $this->db->prepare($updateQuery);

                // Oczyszczenie i bindowanie
                $updateStmt->bindParam(':new_password', $newHashedPassword);
                $updateStmt->bindParam(':patient_id', $patientId);

                // Jeśli aktualizacja przebiegła pomyślnie, zwróć true
                if ($updateStmt->execute()) {
                    error_log("Hasło zostało pomyślnie zaktualizowane.");
                    return true; // Hasło zostało pomyślnie zaktualizowane
                }
                if (!$updateStmt->execute()) { 
                    // Jeśli aktualizacja nie powiodła się, zapisz błąd w pliku error.log
                    error_log("Błąd aktualizacji hasła: " . implode(";", $updateStmt->errorInfo()));
                }
            }
        }
        return false; // Aktualne hasło jest nieprawidłowe lub wystąpił inny błąd
    }
}
