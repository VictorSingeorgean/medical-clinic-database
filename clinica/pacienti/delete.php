<?php
require_once "../config/db.php";
if (!isset($_SESSION['doctor_id'])) { header("Location: ../login.php"); exit; }

$cnp = $_GET['cnp'] ?? null;
if (!$cnp) exit("CNP lipsă");

if (isset($_POST['confirm'])) {
    $stmt = $pdo->prepare("DELETE FROM pacienti WHERE cnp=?");
    $stmt->execute([$cnp]);
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Ștergere</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <div class="alert alert-danger text-center shadow">
        <h4>Confirmați ștergerea?</h4>
        <p>Această acțiune este ireversibilă.</p>
        <form method="post">
            <button type="submit" name="confirm" class="btn btn-danger">Da, șterge</button>
            <a href="index.php" class="btn btn-secondary">Renunță</a>
        </form>
    </div>
</body>
</html>