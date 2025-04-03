<?php
session_start(); // Rozpoczęcie sesji

// Wyczyszczenie tablicy sesji
$_SESSION = array();

// Zniszczenie sesji
session_destroy();

// Przekierowanie użytkownika do strony głównej
header("location: /gabinet/index.php");
exit; // Zakończenie skryptu
