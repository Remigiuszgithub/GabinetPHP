<?php
require_once '../../config/database.php';
require_once '../models/availability.php';
require_once '../models/appointment.php';

// Utworzenie połączenia z bazą danych
$database = new Database();
$db = $database->getConnection();

// Inicjalizacja obiektów modeli
$availability = new Availability($db);
$appointments = new Appointment($db);

// Pobranie przyszłych dostępnych terminów
$availableSlots = $availability->getFutureAvailability();

// Pobranie przyszłych zarezerwowanych wizyt
$bookedAppointments = $appointments->getFutureAppointments();

// Filtracja dostępności, biorąc pod uwagę zarezerwowane wizyty
$filteredAvailability = [];
foreach ($availableSlots as $slot) {
    $startTime = new DateTime($slot['start_time']);
    $endTime = new DateTime($slot['end_time']);

    // Dzielenie dostępności na sesje 25-minutowe z 5-minutowymi przerwami
    while ($startTime < $endTime) {
        $sessionEnd = clone $startTime;
        $sessionEnd->add(new DateInterval('PT25M')); // Długość sesji

        $nextSessionStart = clone $sessionEnd;
        $nextSessionStart->add(new DateInterval('PT5M')); // Przerwa między sesjami

        // Sprawdzenie, czy termin jest zarezerwowany
        $isBooked = false;
        foreach ($bookedAppointments as $appointment) {
            $appointmentTime = new DateTime($appointment['appointment_date']);

            // Sprawdzenie, czy termin wizyty pokrywa się z sesją
            if (
                $slot['dentist_id'] == $appointment['dentist_id'] &&
                $appointmentTime >= $startTime && $appointmentTime < $sessionEnd &&
                $appointment['status'] === 'scheduled'
            ) {
                $isBooked = true;
                break;
            }
        }

        // Jeśli termin nie jest zarezerwowany, dodaj do listy dostępności
        if (!$isBooked) {
            $filteredAvailability[] = [
                'dentist_id' => $slot['dentist_id'],
                'start_time' => $startTime->format('Y-m-d H:i:s'),
                'end_time' => $sessionEnd->format('Y-m-d H:i:s'),
                'first_name' => $slot['first_name'],
                'last_name' => $slot['last_name']
            ];
        }

        $startTime = $nextSessionStart; // Przejście do następnej sesji
    }
}

// Zwrócenie sformatowanych danych dostępności w formacie JSON
echo json_encode($filteredAvailability);
