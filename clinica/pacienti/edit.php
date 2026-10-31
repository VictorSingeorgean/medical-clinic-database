<?php
require_once "../config/db.php";
if (!isset($_SESSION['doctor_id'])) { header("Location: ../login.php"); exit; }

$cnp = $_GET['cnp'] ?? null;
if (!$cnp) exit("CNP lipsă");

$stmt = $pdo->prepare("SELECT * FROM pacienti WHERE cnp=?");
$stmt->execute([$cnp]);
$pacient = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$pacient) exit("Pacient inexistent");

$mesaj = "";
$status = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nume = trim($_POST['nume']);
    $prenume = trim($_POST['prenume']);
    $adresa = trim($_POST['adresa']);
    $data_nasterii = $_POST['data_nasterii'];
    $varsta = intval($_POST['varsta']);
    $email = trim($_POST['email']);
    $telefon = trim($_POST['telefon']);

    $stmt = $pdo->prepare("UPDATE pacienti SET nume=?, prenume=?, adresa=?, data_nasterii=?, varsta=?, email=?, telefon=? WHERE cnp=?");
    try {
        $stmt->execute([$nume, $prenume, $adresa, $data_nasterii, $varsta, $email, $telefon, $cnp]);
        $mesaj = "Datele au fost actualizate!";
        $status = "success";
        $pacient = array_merge($pacient, $_POST); // Update afișare
    } catch (PDOException $e) {
        $mesaj = "Eroare: " . $e->getMessage();
        $status = "danger";
    }
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Editare: <?= $pacient['nume'] ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light container mt-5">
    <div class="card shadow mx-auto" style="max-width: 700px;">
        <div class="card-header bg-warning"><h4>Modifică Date Pacient</h4></div>
        <div class="card-body">
            <?php if ($mesaj): ?>
                <div class="alert alert-<?= $status ?>"><?= $mesaj ?></div>
            <?php endif; ?>
            <form method="post" class="row g-3">
                <div class="col-md-12"><label class="form-label text-muted">CNP (Nu poate fi modificat)</label><input type="text" class="form-control" value="<?= $pacient['cnp'] ?>" disabled></div>
                <div class="col-md-6"><label class="form-label">Nume</label><input type="text" name="nume" class="form-control" value="<?= $pacient['nume'] ?>" required></div>
                <div class="col-md-6"><label class="form-label">Prenume</label><input type="text" name="prenume" class="form-control" value="<?= $pacient['prenume'] ?>" required></div>
                <div class="col-md-12"><label class="form-label">Adresă</label><input type="text" name="adresa" class="form-control" value="<?= $pacient['adresa'] ?>" required></div>
                <div class="col-md-6"><label class="form-label">Data nașterii</label><input type="date" name="data_nasterii" class="form-control" value="<?= $pacient['data_nasterii'] ?>" required></div>
                <div class="col-md-6"><label class="form-label">Vârsta</label><input type="number" name="varsta" class="form-control" value="<?= $pacient['varsta'] ?>" required></div>
                <div class="col-md-6"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="<?= $pacient['email'] ?>"></div>
                <div class="col-md-6"><label class="form-label">Telefon</label><input type="text" name="telefon" class="form-control" value="<?= $pacient['telefon'] ?>"></div>
                <div class="col-12 d-flex justify-content-between">
                    <a href="index.php" class="btn btn-secondary">Anulează</a>
                    <button class="btn btn-primary">Salvează Modificările</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>