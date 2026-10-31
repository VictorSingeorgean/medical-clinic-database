<?php
session_start();
require_once "../config/db.php";

// Logout logic
if (isset($_GET['logout'])) { session_unset(); session_destroy(); header("Location: statistici.php"); exit; }

// Login logic (Corectat pentru parolele tale din DB)
$mesaj = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $parola = trim($_POST['parola']);
    $stmt = $pdo->prepare("SELECT * FROM doctori WHERE email = ?");
    $stmt->execute([$email]);
    $doctor = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($doctor && $parola === $doctor['parola']) { 
        if (strtolower($doctor['rol']) === 'director') {
            $_SESSION['doctor_cnp'] = $doctor['cnp_doctor'];
            $_SESSION['doctor_rol'] = 'director';
            $_SESSION['doctor_nume'] = $doctor['nume'] . ' ' . $doctor['prenume'];
            header("Location: statistici.php"); exit;
        }
    } else { $mesaj = "Date incorecte!"; }
}

$is_director = (isset($_SESSION['doctor_rol']) && $_SESSION['doctor_rol'] === 'director');
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Statistici Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="container mt-4 bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4 rounded px-3 shadow">
    <div class="container-fluid">
        <span class="navbar-brand">🩺 Clinica</span>
        <div class="navbar-nav me-auto">
            <a class="nav-link" href="../pacienti/index.php">Pacienți</a>
            <a class="nav-link" href="../consultatii/index.php">Consultații</a>
            <a class="nav-link active" href="statistici.php">Statistici</a>
        </div>
        <div class="navbar-nav ms-auto">
            <?php if(isset($_SESSION['doctor_nume'])): ?>
                <span class="nav-link disabled text-light">Dr. <?= htmlspecialchars($_SESSION['doctor_nume']) ?></span>
                <a class="nav-link text-danger" href="?logout=1">Logout</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<?php if ($is_director): ?>
    <div class="alert alert-info shadow-sm mb-4">
        <h5>📘 Centru de Ajutor</h5>
        <p>Graficele de mai jos arată activitatea pe specialități și incidența bolilor. Click pe <strong>PDF</strong> pentru raport.</p>
    </div>

    <?php
    // SQL 1: Specialități (Asigurăm legătura corectă între tabele)
    $sql1 = "SELECT s.nume AS specialitate, COUNT(c.nr_consultatie) AS total 
             FROM specialitati s 
             LEFT JOIN doctori d ON s.id = d.specialitate_id 
             LEFT JOIN consultatii c ON d.cnp_doctor = c.cnp_doctor 
             GROUP BY s.nume";
    $data1 = $pdo->query($sql1)->fetchAll(PDO::FETCH_ASSOC);

    // SQL 2: Boli Cronice (Folosim LOWER pentru a găsi diagnosticele indiferent de scriere)
    $sql2 = "
        SELECT 'Diabet' AS boala, COUNT(*) AS nr FROM consultatii WHERE LOWER(diagnostic) LIKE '%diabet%'
        UNION ALL
        SELECT 'Hipertensiune', COUNT(*) FROM consultatii WHERE LOWER(diagnostic) LIKE '%hipertensiune%'
        UNION ALL
        SELECT 'Astm', COUNT(*) FROM consultatii WHERE LOWER(diagnostic) LIKE '%astm%'
        UNION ALL
        SELECT 'Cancer', COUNT(*) FROM consultatii WHERE LOWER(diagnostic) LIKE '%cancer%'
    ";
    $data2 = $pdo->query($sql2)->fetchAll(PDO::FETCH_ASSOC);

    $spec_labels = []; $spec_values = [];
    foreach($data1 as $r){ $spec_labels[] = $r['specialitate']; $spec_values[] = $r['total']; }

    $boli_labels = []; $boli_values = [];
    foreach($data2 as $r){ $boli_labels[] = $r['boala']; $boli_values[] = $r['nr']; }
    ?>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h5 class="card-title text-primary">Cazuri pe Specialități</h5>
                    <canvas id="barSpec"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h5 class="card-title text-danger text-center">Boli Cronice</h5>
                    <canvas id="pieBoli"></canvas>
                </div>
            </div>
        </div>
    </div>

    <form method="POST" action="pdf_statistici.php" id="pdfForm">
        <input type="hidden" name="graf" value="general">
        <input type="hidden" name="bar" id="barData">
        <input type="hidden" name="pie" id="pieData">
        <button type="button" onclick="submitWithCharts()" class="btn btn-info text-white">Generare Raport PDF</button>
    </form>

    <script>
    const barCtx = document.getElementById('barSpec').getContext('2d');
    const pieCtx = document.getElementById('pieBoli').getContext('2d');

    const chartBar = new Chart(barCtx, {
        type: 'bar',
        data: { 
            labels: <?= json_encode($spec_labels) ?>, 
            datasets: [{ label: 'Consultații', data: <?= json_encode($spec_values) ?>, backgroundColor: '#3498db' }] 
        }
    });

    const chartPie = new Chart(pieCtx, {
        type: 'pie',
        data: { 
            labels: <?= json_encode($boli_labels) ?>, 
            datasets: [{ data: <?= json_encode($boli_values) ?>, backgroundColor: ['#2ecc71', '#e74c3c', '#f1c40f', '#9b59b6'] }] 
        }
    });

    function submitWithCharts() {
        document.getElementById('barData').value = document.getElementById('barSpec').toDataURL();
        document.getElementById('pieData').value = document.getElementById('pieBoli').toDataURL();
        document.getElementById('pdfForm').submit();
    }
    </script>

<?php else: ?>
    <div class="row justify-content-center pt-5">
        <div class="col-md-4 card p-4 shadow border-0">
            <h4 class="text-center mb-4">🔐 Login Director</h4>
            <?php if($mesaj): ?> <div class="alert alert-danger"><?= $mesaj ?></div> <?php endif; ?>
            <form method="POST">
                <input type="hidden" name="login" value="1">
                <input type="email" name="email" class="form-control mb-2" placeholder="director@clinica.ro" required>
                <input type="password" name="parola" class="form-control mb-3" placeholder="Parolă" required>
                <button class="btn btn-primary w-100">Autentificare</button>
            </form>
        </div>
    </div>
<?php endif; ?>

</body>
</html>