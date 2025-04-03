<!-- Lista lekarzy dentystów, która jest plikiem udostępnianym 'shared' -->

<?php

// Załadowanie pliku konfiguracyjnego bazy danych i klas modelu
require_once '../../config/database.php';
require_once '../models/dentist.php';

// Inicjalizacja obiektu bazy danych i konfiguracja połączenia
$database = new Database();
$db = $database->getConnection();

// Inicjalizacja obiektu dentysty
$dentist = new Dentist($db);

// Pobranie listy dentystów
$stmt = $dentist->readAll();

// Rozpoczęcie kontenera responsywnego
echo "<div class='table-responsive'>";
echo "<table class='table table-striped'>";
echo "<thead class='thead-dark'>";
echo "<tr><th>ID</th><th>Imię</th><th>Nazwisko</th><th>Email</th><th>Specjalizacja</th><th>Akcje</th></tr>";
echo "</thead>";
echo "<tbody>";

// Iterowanie przez wyniki i wyświetlanie każdego dentysty
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    extract($row);
    echo "<tr>";
    echo "<td>{$dentist_id}</td>";
    echo "<td>{$first_name}</td>";
    echo "<td>{$last_name}</td>";
    echo "<td>{$email}</td>";
    echo "<td>{$specialization}</td>";
    echo "<td>";
    echo "<a href='dentist_edit.php?dentist_id={$dentist_id}' class='btn btn-primary'>Edytuj</a>";
    echo " <a href='#' data-id='{$dentist_id}' class='btn btn-danger delete-btn'>Usuń</a>";
    echo "</td>";
    echo "</tr>";
}

echo "</tbody>";
echo "</table>";
echo "</div>";
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.0/dist/sweetalert2.all.min.js" integrity="sha256-IW9RTty6djbi3+dyypxajC14pE6ZrP53DLfY9w40Xn4=" crossorigin="anonymous"></script>

<script>
    // Zapytanie potwierdzające usunięcie lekarza
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const dentistId = this.getAttribute('data-id');

            // Wyświetlenie okna dialogowego pytającego o potwierdzenie usunięcia
            Swal.fire({
                title: "Jesteś pewien?",
                text: "Tej operacji nie można cofnąć.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Tak, usuń lekarza",
                cancelButtonText: "Anuluj"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '../controllers/delete_dentist_controller.php?dentist_id=' + dentistId;
                }
            });
        });
    });
</script>