<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION['doctor_id'])) { header("Location: ../login.php"); exit; }

// Luăm lista de pacienți pentru dropdown
$pacienti = $pdo->query("SELECT cnp, nume, prenume FROM pacienti ORDER BY nume")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cnp_pacient = $_POST['cnp_pacient'];
    $cnp_doctor = $_SESSION['doctor_id'];
    $data = $_POST['data_consultatie'];
    $diagnostic = $_POST['diagnostic'];
    $tratament = $_POST['tratament'];

    $sql = "INSERT INTO consultatii (cnp_pacient, cnp_doctor, data_consultatie, diagnostic, tratament) VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute([$cnp_pacient, $cnp_doctor, $data, $diagnostic, $tratament])) {
        header("Location: index.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Adăugare Consultație</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4 bg-light">
    <div class="row justify-content-center">
        <div class="col-md-8 card p-4 shadow-sm border-0">
            <h3 class="mb-4 text-success">📋 Înregistrare Consultație Nouă</h3>
            <form method="POST" action="">
                <div class="mb-3">
                    <label>Pacient</label>
                    <select name="cnp_pacient" class="form-select" required>
                        <option value="">Selectează pacientul...</option>
                        <?php foreach($pacienti as $p): ?>
                            <option value="<?= $p['cnp'] ?>"><?= $p['nume'] ?> <?= $p['prenume'] ?> (<?= $p['cnp'] ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Data Consultației</label>
                    <input type="date" name="data_consultatie" class="form-control" value="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="mb-3">
                    <label>Diagnostic</label>
                    <input type="text" name="diagnostic" class="form-control" required>
                </div>
                <div class="mb-4">
                    <label>Tratament Recomandat</label>
                    <textarea name="tratament" class="form-control" rows="3"></textarea>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Înregistrează Consultația</button>
                    <a href="index.php" class="btn btn-secondary">Înapoi</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>