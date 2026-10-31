<?php
session_start(); // Inițializează sesiunea pentru a o putea închide

// Ștergem toate variabilele din sesiune
$_SESSION = array();

// Distrugem sesiunea complet
session_destroy();

// Redirecționăm utilizatorul către pagina de login
// Presupunând că login.php este tot în folderul principal 'clinica'
header("Location: login.php");
exit;
?>