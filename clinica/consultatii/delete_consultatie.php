<?php
require_once "../config/db.php";

// Protecție login
if (!isset($_SESSION['doctor_id'])) {
    header("Location: ../login.php");
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) exit("ID consultație lipsă");

// Preluare detalii pentru confirmare
$stmt = $pdo->prepare("SELECT c.nr_consultatie, p.nume, p.prenume, d.nume AS nume_doctor, d.prenume AS prenume_doctor 
                       FROM consultatii c
                       INNER JOIN pacienti p ON c.cnp_pacient = p.cnp
                       INNER JOIN doctori d ON c.cnp_doctor = d.cnp_doctor
                       WHERE c.nr_consultatie = ?");
$stmt->execute([$id]);
$consultatie = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$consultatie) exit("Consultație inexistentă");

if (isset($_POST['confirm'])) {
    $stmt = $pdo->prepare("DELETE FROM consultatii WHERE nr_consultatie = ?");
    $stmt->execute([$id]);
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Confirmă Ștergerea</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">

<div class="card border-danger mx-auto shadow-sm" style="max-width: 550px;">
    <div class="card-header bg-danger text-white">
        <h5 class="mb-0">Ștergere Consultație #<?= $id ?></h5>
    </div>
    <div class="card-body text-center">
        <p>Sunteți sigur că doriți să ștergeți consultația pacientului:</p>
        <h4 class="text-primary"><?= $consultatie['nume'] ?> <?= $consultatie['prenume'] ?></h4>
        <p>efectuată de <strong>Dr. <?= $consultatie['nume_doctor'] ?> <?= $consultatie['prenume_doctor'] ?></strong>?</p>
        
        <form method="post" class="mt-4">
            <button type="submit" name="confirm" class="btn btn-danger px-4">Da, șterge consultația</button>
            <a href="index.php" class="btn btn-secondary px-4">Anulează</a>
        </form>
    </div>
</div>

</body>
</html>