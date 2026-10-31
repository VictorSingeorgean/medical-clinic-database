<?php
require_once "../config/db.php";

// Protecție login
if (!isset($_SESSION['doctor_id'])) {
    header("Location: ../login.php");
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) exit("ID consultație lipsă");

// Preluare lista doctori și pacienți pentru dropdown
$doctori = $pdo->query("SELECT cnp_doctor, nume, prenume FROM doctori ORDER BY nume")->fetchAll(PDO::FETCH_ASSOC);
$pacienti = $pdo->query("SELECT cnp, nume, prenume FROM pacienti ORDER BY nume")->fetchAll(PDO::FETCH_ASSOC);

// Preluare date consultatie
$stmt = $pdo->prepare("SELECT * FROM consultatii WHERE nr_consultatie = ?");
$stmt->execute([$id]);
$consultatie = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$consultatie) exit("Consultație inexistentă");

$mesaj = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $cnp_pacient = $_POST['cnp_pacient'];
    $cnp_doctor = $_POST['cnp_doctor'];
    $data_consultatie = $_POST['data_consultatie'];
    $diagnostic = $_POST['diagnostic'];
    $medicamentatie = $_POST['medicamentatie'];

    $stmt = $pdo->prepare("UPDATE consultatii SET cnp_pacient=?, cnp_doctor=?, data_consultatie=?, diagnostic=?, medicamentatie=? WHERE nr_consultatie=?");
    try {
        $stmt->execute([$cnp_pacient, $cnp_doctor, $data_consultatie, $diagnostic, $medicamentatie, $id]);
        $mesaj = "Modificări salvate cu succes!";
        // Refresh date
        $consultatie = array_merge($consultatie, $_POST);
    } catch (PDOException $e) {
        $mesaj = "Eroare la modificare: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Modifică Consultație</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow mx-auto" style="max-width: 650px;">
        <div class="card-header bg-warning">
            <h4 class="mb-0">Editează Consultația #<?= $id ?></h4>
        </div>
        <div class="card-body">
            <?php if ($mesaj): ?>
                <div class="alert alert-info"><?= $mesaj ?></div>
            <?php endif; ?>

            <form method="post" class="row g-3">
                <div class="col-md-12">
                    <label class="form-label">Pacient:</label>
                    <select name="cnp_pacient" class="form-select" required>
                        <?php foreach($pacienti as $p): ?>
                            <option value="<?= $p['cnp'] ?>" <?= $p['cnp']==$consultatie['cnp_pacient']?'selected':'' ?>>
                                <?= $p['nume'] ?> <?= $p['prenume'] ?> (<?= $p['cnp'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-12">
                    <label class="form-label">Doctor:</label>
                    <select name="cnp_doctor" class="form-select" required>
                        <?php foreach($doctori as $d): ?>
                            <option value="<?= $d['cnp_doctor'] ?>" <?= $d['cnp_doctor']==$consultatie['cnp_doctor']?'selected':'' ?>>
                                Dr. <?= $d['nume'] ?> <?= $d['prenume'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-12">
                    <label class="form-label">Data consultației:</label>
                    <input type="date" name="data_consultatie" class="form-control" value="<?= $consultatie['data_consultatie'] ?>" required>
                </div>

                <div class="col-12">
                    <label class="form-label">Diagnostic:</label>
                    <textarea name="diagnostic" class="form-control" rows="2" required><?= htmlspecialchars($consultatie['diagnostic']) ?></textarea>
                </div>

                <div class="col-12">
                    <label class="form-label">Medicamentație:</label>
                    <textarea name="medicamentatie" class="form-control" rows="3" required><?= htmlspecialchars($consultatie['medicamentatie']) ?></textarea>
                </div>

                <div class="col-12 d-flex justify-content-between mt-4">
                    <a href="index.php" class="btn btn-secondary">Înapoi</a>
                    <button type="submit" class="btn btn-primary px-4">Actualizează Consultația</button>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>