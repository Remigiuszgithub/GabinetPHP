<!-- 
**********

    Pasek nawigacyjny, który jest wyświetlany na każdej stronie 'shared_navbar.php'.

**********
-->

<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
    <div class="container-fluid">
        <a class="btn btn-light btn-lg" href="/gabinet/index.php">GABINET</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <?php if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] == false) : ?>
                    <li class="nav-item dropdown">
                        <a class="btn btn-light dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Zaloguj się
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="/gabinet/app/views/patient_login.php">Logowanie pacjenta</a>
                            <a class="dropdown-item" href="/gabinet/app/views/dentist_login.php">Logowanie personelu</a>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-light" href="/gabinet/app/views/patient_register.php">Zarejestruj się!</a>
                    </li>
                <?php else : ?>
                    <li class="nav-item dropdown">
                        <a class="btn btn-light dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Twoje konto
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                            <?php
                            // Sprawdzenie roli i wyświetlenie odpowiedniego panelu
                            if (isset($_SESSION['role'])) {
                                switch ($_SESSION['role']) {
                                    case 'administrator':
                                        echo '<a class="dropdown-item" href="/gabinet/app/views/admin_panel.php">Panel administratora</a>';
                                        break;
                                    case 'patient':
                                        echo '<a class="dropdown-item" href="/gabinet/app/views/patient_panel.php">Panel pacjenta</a>';
                                        break;
                                    case 'dentist':
                                        echo '<a class="dropdown-item" href="/gabinet/app/views/dentist_panel.php">Panel lekarza</a>';
                                        break;
                                }
                            }
                            ?>
                        </div>
                    </li>
                <?php endif; ?>
            </ul>
            <span class="nav-item">
                <?php
                // Sprawdzenie czy użytkownik jest zalogowany
                if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']) {
                    // Przypisanie zmiennej $firstName wartości z sesji lub domyślnej wartości
                    $firstName = $_SESSION['first_name'] ?? 'Gość';
                    $role = $_SESSION['role'] ?? 'nieokreślona rola';

                    // Tłumaczenie roli na język polski
                    switch ($role) {
                        case 'administrator':
                            $translatedRole = 'administrator';
                            break;
                        case 'patient':
                            $translatedRole = 'pacjent';
                            break;
                        case 'dentist':
                            $translatedRole = 'lekarz';
                            break;
                        default:
                            $translatedRole = 'nieokreślona rola';
                    }

                    // Wyświetlenie powitania
                    echo "Witaj <strong>" . htmlspecialchars($firstName) . "</strong>! Jesteś zalogowany/a jako <strong>" . htmlspecialchars($translatedRole) . "</strong>.";
                }
                ?>
            </span>
            <span class="nav-item">
                <?php
                // Wyświetlenie przycisku wylogowania jeśli użytkownik jest zalogowany
                if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']) {
                    echo '<a class="btn btn-light" href="/gabinet/app/controllers/logout_controller.php">Wyloguj się</a>';
                }
                ?>
            </span>
        </div>
    </div>
</nav>