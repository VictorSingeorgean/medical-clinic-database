<?php
session_start();
require_once "../config/db.php"; //

// Verificare autentificare
if (!isset($_SESSION['doctor_id'])) { header("Location: ../login.php"); exit; }

$mesaj = "";
// Logica de salvare inclusă în fișier pentru a evita save.php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $sql = "INSERT INTO pacienti (cnp, nume, prenume, adresa, telefon, email, data_nasterii) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $_POST['cnp'], 
            $_POST['nume'], 
            $_POST['prenume'], 
            $_POST['adresa'], 
            $_POST['telefon'], 
            $_POST['email'],
            $_POST['data_nasterii']
        ]);
        header("Location: index.php?success=1");
        exit;
    } catch (PDOException $e) {
        $mesaj = "<div class='alert alert-danger'>Eroare la salvare: " . $e->getMessage() . "</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Adăugare Pacient</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4 bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4 rounded px-3 shadow">
    <div class="container-fluid">
        <span class="navbar-brand">🩺 Clinica</span>
        <div class="navbar-nav me-auto">
            <a class="nav-link active" href="index.php">Pacienți</a>
            <a class="nav-link" href="../consultatii/index.php">Consultații</a>
            <a class="nav-link" href="../statistici/statistici.php">Statistici</a>
        </div>
    </div>
</nav>

<div class="card shadow border-0">
    <div class="card-header bg-primary text-white">
        <h4 class="mb-0">➕ Adăugare Pacient Nou</h4>
    </div>
    <div class="card-body">
        <?= $mesaj ?>
        <form action="" method="POST" class="needs-validation" novalidate>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nume</label>
                    <input type="text" name="nume" class="form-control" required pattern="[A-Za-zȘșȚțĂăÎîÂâ -]+" title="Introduceți doar litere">
                    <div class="invalid-feedback">Numele este obligatoriu și trebuie să conțină doar litere.</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Prenume</label>
                    <input type="text" name="prenume" class="form-control" required pattern="[A-Za-zȘșȚțĂăÎîÂâ -]+">
                    <div class="invalid-feedback">Prenumele este obligatoriu.</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">CNP (13 cifre)</label>
                    <input type="text" name="cnp" class="form-control" required pattern="[0-9]{13}" maxlength="13" placeholder="Ex: 1900101123456">
                    <div class="invalid-feedback">Introduceți un CNP valid format din 13 cifre.</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Data Nașterii</label>
                    <input type="date" name="data_nasterii" class="form-control" max="<?= date('Y-m-d') ?>" required>
                    <div class="invalid-feedback">Data nașterii este invalidă.</div>
                </div>
                <div class="col-md-12">
                    <label class="form-label">Adresă</label>
                    <textarea name="adresa" class="form-control" rows="2" required></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Telefon</label>
                    <input type="tel" name="telefon" class="form-control" pattern="0[0-9]{9}" placeholder="07xxxxxxxx">
                    <div class="form-text">Format: 10 cifre (ex: 0722123456).</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control">
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="btn btn-success px-4">Salvează Pacient</button>
                <a href="index.php" class="btn btn-secondary">Anulează</a>
            </div>
        </form>
    </div>
</div>

<script>
// Validarea ta originală care colorează câmpurile în roșu/verde
(function () {
  'use strict'
  var forms = document.querySelectorAll('.needs-validation')
  Array.prototype.slice.call(forms).forEach(function (form) {
    form.addEventListener('submit', function (event) {
      if (!form.checkValidity()) {
        event.preventDefault()
        event.stopPropagation()
      }
      form.classList.add('was-validated')
    }, false)
  })
})()
</script>
</body>
</html>