<?php
session_start();
require_once "../config/db.php";
if (!isset($_SESSION['doctor_id'])) { header("Location: ../login.php"); exit; }

$search = $_GET['search'] ?? "";
$sql = "SELECT * FROM pacienti WHERE cnp LIKE ? OR nume LIKE ? OR prenume LIKE ? ORDER BY nume ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute(["%$search%", "%$search%", "%$search%"]);
$pacienti = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Pacienți - Ajutor Integrat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .btn-edit { background-color: #6610f2; color: white; }
        .btn-edit:hover { background-color: #520dc2; color: white; }
        .help-section { background-color: #e9ecef; border-left: 5px solid #0d6efd; }
    </style>
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
        <div class="navbar-nav ms-auto">
            <span class="nav-link disabled text-light">Dr. <?= htmlspecialchars($_SESSION['doctor_nume']) ?></span>
            <a class="nav-link text-danger" href="../logout.php">Logout</a>
        </div>
    </div>
</nav>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Listă Pacienți</h2>
    <div>
        <button class="btn btn-outline-info me-2" data-bs-toggle="collapse" data-bs-target="#helpPanel">❓ Cum funcționează?</button>
        <a href="add.php" class="btn btn-success" title="Click aici pentru a înregistra un pacient nou">+ Adaugă Pacient</a>
    </div>
</div>

<div class="collapse mb-4" id="helpPanel">
    <div class="card card-body help-section shadow-sm">
        <h5>💡 Ghid Rapid de Gestiune:</h5>
        <ul>
            <li><strong>Adăugare:</strong> Folosiți butonul verde "+" pentru pacienți noi.</li>
            <li><strong>Căutare:</strong> Introduceți numele sau CNP-ul în bara de căutare pentru filtrare rapidă.</li>
            <li><strong>Editare:</strong> Butonul violet permite modificarea datelor (ex. schimbare adresă/telefon).</li>
            <li><strong>Ștergere:</strong> Butonul roșu elimină pacientul. Atenție: cere confirmare pentru a evita greșelile!</li>
        </ul>
    </div>
</div>

<form method="get" class="mb-4">
    <div class="input-group">
        <input type="text" name="search" class="form-control" placeholder="Căutare după nume sau CNP..." value="<?= htmlspecialchars($search) ?>">
        <button class="btn btn-primary" type="submit">Filtrează</button>
    </div>
</form>

<?php foreach($pacienti as $p): ?>
<div class="card mb-2 border-0 shadow-sm">
    <div class="card-body d-flex justify-content-between align-items-center">
        <div>
            <strong><?= htmlspecialchars($p['nume']) ?> <?= htmlspecialchars($p['prenume']) ?></strong>
            <div class="text-muted small">CNP: <?= htmlspecialchars($p['cnp']) ?></div>
        </div>
        <div class="btn-group">
            <a href="edit.php?cnp=<?= $p['cnp'] ?>" class="btn btn-edit btn-sm" title="Modifică datele acestui pacient">Editare</a>
            <a href="../fisa_pacient.php?cnp=<?= $p['cnp'] ?>" target="_blank" class="btn btn-info btn-sm text-white" title="Vezi fișa medicală completă">PDF</a>
            <a href="delete.php?cnp=<?= $p['cnp'] ?>" class="btn btn-danger btn-sm" 
               onclick="return confirm('ATENȚIE: Ștergerea este permanentă. Continuați?')" 
               title="Șterge definitiv înregistrarea">Șterge</a>
        </div>
    </div>
</div>
<?php endforeach; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Inițializare Tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'))
    var tooltipList = tooltipTriggerList.map(function (el) { return new bootstrap.Tooltip(el) })
</script>
</body>
</html>