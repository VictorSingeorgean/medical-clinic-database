<?php
require_once "config/db.php";

// Dacă doctorul este deja logat, îl trimitem la pacienți
if (isset($_SESSION['doctor_id'])) {
    header("Location: pacienti/index.php");
    exit;
}

$eroare = "";

// Verificăm dacă formularul a fost trimis
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Verificăm dacă cheile există în $_POST înainte de a le folosi
    $email = isset($_POST['email']) ? trim($_POST['email']) : "";
    $parola_introdusa = isset($_POST['parola']) ? $_POST['parola'] : "";

    if (!empty($email) && !empty($parola_introdusa)) {
        // Căutăm doctorul după email
        $stmt = $pdo->prepare("SELECT * FROM doctori WHERE email = ?");
        $stmt->execute([$email]);
        $doctor = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verificare parolă (text simplu conform structurii tale)
        if ($doctor && ($parola_introdusa === $doctor['parola'])) {
            $_SESSION['doctor_id'] = $doctor['cnp_doctor'];
            $_SESSION['doctor_nume'] = $doctor['nume'] . " " . $doctor['prenume'];
            $_SESSION['rol'] = $doctor['rol'];
            
            header("Location: pacienti/index.php");
            exit;
        } else {
            $eroare = "Email sau parolă incorectă!";
        }
    } else {
        $eroare = "Vă rugăm să completați toate câmpurile!";
    }
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Clinic</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f7f6; height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-card { width: 100%; max-width: 400px; padding: 30px; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); background: white; }
    </style>
</head>
<body>

<div class="login-card">
    <div class="text-center mb-4">
        <h2 class="fw-bold text-primary">🩺 Clinica Med</h2>
        <p class="text-muted">Autentificare Personal Medical</p>
    </div>

    <?php if ($eroare): ?>
        <div class="alert alert-danger text-center small"><?= $eroare ?></div>
    <?php endif; ?>

    <form method="post" action="login.php">
        <div class="mb-3">
            <label class="form-label">Adresă Email</label>
            <input type="email" name="email" class="form-control" placeholder="nume@clinica.ro" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Parolă</label>
            <input type="password" name="parola" class="form-control" placeholder="********" required>
        </div>
        <div class="d-grid gap-2 mt-4">
            <button type="submit" class="btn btn-primary btn-lg">Conectare</button>
        </div>
    </form>
    
    <div class="mt-4 text-center">
        <small class="text-muted border-top pt-3 d-block">Sistem de Gestiune v1.0</small>
    </div>
</div>

</body>
</html>