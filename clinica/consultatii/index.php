<?php
session_start();
require_once "../config/db.php";

// Verificare autentificare medic
if (!isset($_SESSION['doctor_id'])) { 
    header("Location: ../login.php"); 
    exit; 
}

// Interogare pentru a aduce datele consultațiilor (folosind coloana medicamentatie)
$sql = "SELECT c.nr_consultatie, c.data_consultatie, c.diagnostic, c.medicamentatie, p.nume, p.prenume 
        FROM consultatii c 
        JOIN pacienti p ON c.cnp_pacient = p.cnp 
        ORDER BY c.data_consultatie DESC";
$consultatii = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Consultații - Clinică</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .navbar { background-color: #1a1d20; }
        .btn-edit { background-color: #6610f2; color: white; border: none; }
        .btn-edit:hover { background-color: #520dc2; color: white; }
    </style>
</head>
<body class="container mt-4 bg-light">

<nav class="navbar navbar-expand-lg navbar-dark mb-4 rounded px-3 shadow">
    <div class="container-fluid">
        <span class="navbar-brand">🩺 Clinica</span>
        <div class="navbar-nav me-auto">
            <a class="nav-link" href="../pacienti/index.php">Pacienți</a>
            <a class="nav-link active" href="index.php">Consultații</a>
            <a class="nav-link" href="../statistici/statistici.php">Statistici</a>
        </div>
        <div class="navbar-nav ms-auto">
            <span class="nav-link disabled text-light">Dr. <?= htmlspecialchars($_SESSION['doctor_nume']) ?></span>
            <a class="nav-link text-danger fw-bold" href="../logout.php">Logout</a>
        </div>
    </div>
</nav>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>📋 Registru Consultații</h2>
    <a href="add_consultatie.php" class="btn btn-success shadow-sm">+ Adaugă Consultație</a>
</div>

<div class="bg-white p-3 rounded shadow-sm border-0">
    <table class="table table-hover">
        <thead class="table-dark">
            <tr>
                <th>Data</th>
                <th>Pacient</th>
                <th>Diagnostic</th>
                <th>Medicamentație</th>
                <th class="text-center">Acțiuni</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($consultatii as $c): ?>
            <tr class="align-middle">
                <td><?= date('d.m.Y', strtotime($c['data_consultatie'])) ?></td>
                <td class="fw-bold"><?= htmlspecialchars($c['nume']) ?> <?= htmlspecialchars($c['prenume']) ?></td>
                <td><span class="badge bg-info text-dark"><?= htmlspecialchars($c['diagnostic']) ?></span></td>
                <td><small><?= htmlspecialchars($c['medicamentatie']) ?></small></td>
                <td class="text-center">
                    <div class="btn-group">
                        <a href="edit_consultatie.php?id=<?= $c['nr_consultatie'] ?>" class="btn btn-edit btn-sm">Edit</a>
                        <a href="../fisa_afectiune.php?afectiune=<?= urlencode($c['diagnostic']) ?>" 
                           target="_blank" 
                           class="btn btn-outline-primary btn-sm">
                           PDF
                        </a>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>