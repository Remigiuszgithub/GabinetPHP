
// Oczekiwanie na załadowanie całej treści dokumentu
document.addEventListener('DOMContentLoaded', async function () {
    await updateAppointmentsStatus(); // Aktualizacja statusów wizyt
    loadAppointments(undefined, true); // Ładowanie wizyt
});

// Funkcja, która anuluje wizytę
function cancelAppointment(appointmentId) {
    Swal.fire({
        // Wykorzystanie biblioteki SweetAlert2 do wyświetlenia okna dialogowego
        // Konfiguracja okna dialogowego
        title: 'Czy na pewno chcesz odwołać wizytę?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Tak, odwołaj',
        cancelButtonText: 'Nie'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                // Wywołanie AJAX do anulowania wizyty
                url: '/gabinet/app/controllers/dentist_cancel_appointment.php',
                type: 'POST',
                data: { appointment_id: appointmentId },
                success: function (response) {
                    const data = JSON.parse(response);
                    Swal.fire(
                        'Odwołano!',
                        data.message,
                        'success'
                    );
                    loadAppointments();
                },
                error: function (error) {
                    Swal.fire(
                        'Błąd!',
                        'Nie udało się odwołać wizyty.',
                        'error'
                    );
                }
            });
        }
    });
}

// Funkcja, która zmienia status wizyty
function changeAppointmentStatus(appointmentId, newStatus) {
    // Wywołanie alertu z biblioteki SweetAlert2
    Swal.fire({
        title: 'Zmień status wizyty',
        text: `Potwierdź, że pacjent nie stawił się na wizytę`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Tak, pacjenta nie było',
        cancelButtonText: 'Nie'
    }).then((result) => {
        // Jeśli użytkownik potwierdził zmianę statusu
        if (result.isConfirmed) {
            $.ajax({
                // Wysłanie zapytania AJAX do serwera
                url: '/gabinet/app/controllers/appointment_change_status.php',
                type: 'POST',
                data: { appointment_id: appointmentId, new_status: newStatus },
                success: function (response) {
                    // Jeśli zapytanie zakończyło się sukcesem to wyświetl komunikat
                    const data = JSON.parse(response);
                    Swal.fire(
                        'Zmieniono!',
                        data.message,
                        'success'
                    );
                    loadAppointments(); // Ponowne załadowanie wizyt
                },
                // Jeśli zapytanie zakończyło się błędem to wyświetl komunikat
                error: function (error) {
                    Swal.fire(
                        'Błąd!',
                        'Nie udało się zmienić statusu wizyty.',
                        'error'
                    );
                }
            });
        }
    });
}

// Zadeklarowanie zmiennej globalnej, która będzie przechowywać wszystkie wizyty
var globalAppointments = [];

// Funkcja, która ładuje wizyty
function loadAppointments(filterStatus = 'scheduled', isInitialLoad = false, filterText = 'zaplanowane') {
    document.getElementById('appointmentsHeader').textContent = 'Wizyty ' + filterText;
    $.ajax({
        // Wysłanie zapytania AJAX do serwera
        url: '/gabinet/app/controllers/get_dentist_appointments.php',
        type: 'GET',
        success: function (response) {
            // Jeśli zapytanie zakończyło się sukcesem to zapisz wizyty do zmiennej globalnej
            globalAppointments = JSON.parse(response);
            var statusToFilter = isInitialLoad ? 'scheduled' : filterStatus;
            renderTable(globalAppointments, statusToFilter); // Wywołanie funkcji, która wyświetla wizyty w tabeli
        },
        error: function (error) {
            console.log('Błąd podczas ładowania wizyt', error);
        }
    });
}

// Funkcja, która sortuje wizyty
function sortAppointments(sortKey) {
    var sortedAppointments = [...globalAppointments]; // Skopiowanie wszystkich wizyt do nowej tablicy
    sortedAppointments.sort(function (a, b) {
        // Sortowanie wizyt po dacie lub nazwisku pacjenta
        if (sortKey === 'date') {
            return new Date(a.appointment_date) - new Date(b.appointment_date);
        } else if (sortKey === 'patient') {
            return a.first_name.localeCompare(b.first_name) || a.last_name.localeCompare(b.last_name);
        }
    });
    renderTable(sortedAppointments);
}

// Funkcja, która filtruje wizyty
function renderTable(appointments, filterStatus = 'scheduled') {
    var html = '';
    appointments.forEach(function (appointment) {
        // Jeśli filterStatus jest pusty lub równy statusowi wizyty, dodaj wizytę do tabeli
        if (filterStatus === '' || appointment.status === filterStatus) {
            html += '<tr id="appointment-row-' + appointment.appointment_id + '">';
            html += '<td>' + appointment.appointment_date + '</td>';
            html += '<td>' + appointment.first_name + ' ' + appointment.last_name + '</td>';
            html += '<td>' + formatAppointmentStatus(appointment.status) + '</td>';

            // Jeśli wizyta jest zaplanowana, dodaj przycisk do anulowania wizyty
            if (appointment.status === 'scheduled') {
                html += '<td><button class="btn btn-danger" onclick="cancelAppointment(' + appointment.appointment_id + ')">Odwołaj</button></td>';
            }
            // Jeśli wizyta jest wykonana, dodaj przycisk do zmiany statusu na "Pacjent nie stawił się"
            else if (appointment.status === 'completed') {
                html += '<td><button class="btn btn-warning" onclick="changeAppointmentStatus(' + appointment.appointment_id + ', \'no_show\')">Pacjent nie stawił się</button></td>';
            } else {
                html += '<td></td>'; // Puste pole dla pozostałych statusów
            }

            html += '</tr>';
        }
    });
    $('#appointments-table tbody').html(html);
}


// Funkcja pomocnicza do formatowania statusu wizyty
function formatAppointmentStatus(status) {
    switch (status) {
        case 'scheduled':
            return 'Zaplanowana';
        case 'completed':
            return 'Wykonana';
        case 'no_show':
            return 'Pacjent nie stawił się';
        case 'cancelled_by_patient':
            return 'Odwołana przez pacjenta';
        case 'cancelled_by_dentist':
            return 'Odwołana przez lekarza';
        default:
            return 'Inny status';
    }
}

// Asynchroniczna funkcja, która aktualizuje statusy wizyt
async function updateAppointmentsStatus() {
    try {
        // Wysłanie zapytania do serwera
        const response = await fetch('/gabinet/app/controllers/update_appointments_status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            }
        });

        const data = await response.json(); // Przypisanie odpowiedzi do zmiennej

        // Jeśli zapytanie zakończyło się sukcesem, wyświetl komunikat w konsoli
        if (data.success) {
            console.log(data.message);
        } else {
            // Jeśli zapytanie zakończyło się błędem, wyświetl komunikat w konsoli
            console.error(data.error || 'Nieznany błąd');
        }
    } catch (error) {
        // Jeśli wystąpił błąd podczas komunikacji z serwerem, wyświetl komunikat w konsoli
        console.error('Wystąpił błąd podczas komunikacji z serwerem:', error);
    }
}
