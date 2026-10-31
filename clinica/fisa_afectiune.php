<?php
require_once "config/db.php";
require_once "rapoarte/fpdf/fpdf.php";

// Preluare parametru diagnostic din URL
$afectiune = $_GET['afectiune'] ?? "";

if (!$afectiune) {
    exit("Eroare: Trebuie specificată o afecțiune pentru a genera raportul!");
}

// Interogare SQL corectată pentru coloana 'medicamentatie'
$sql = "
    SELECT c.*, 
           p.nume AS nume_pacient, p.prenume AS prenume_pacient, 
           d.nume AS nume_doctor, d.prenume AS prenume_doctor
    FROM consultatii c
    INNER JOIN pacienti p ON c.cnp_pacient = p.cnp
    INNER JOIN doctori d ON c.cnp_doctor = d.cnp_doctor
    WHERE c.diagnostic = ?
    ORDER BY c.data_consultatie DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute([$afectiune]);
$consultatii = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$consultatii) {
    exit("Nu există înregistrări pentru diagnosticul: " . htmlspecialchars($afectiune));
}

// Inițializare FPDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);

// Titlu Raport
$pdf->Cell(0, 10, "Raport Consultatii - Diagnostic: " . $afectiune, 0, 1, 'C');
$pdf->Ln(10);

foreach ($consultatii as $c) {
    // Secțiune Pacient
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 8, "Pacient: " . $c['nume_pacient'] . " " . $c['prenume_pacient'], 0, 1);
    
    $pdf->SetFont('Arial', '', 11);
    $pdf->Cell(0, 7, "CNP: " . $c['cnp_pacient'], 0, 1);
    $pdf->Cell(0, 7, "Data: " . date('d.m.Y', strtotime($c['data_consultatie'])), 0, 1);
    $pdf->Cell(0, 7, "Doctor: " . $c['nume_doctor'] . " " . $c['prenume_doctor'], 0, 1);
    
    // Afișare Medicamentație (coloana corectă din DB)
    $pdf->SetFont('Arial', 'I', 11);
    $pdf->MultiCell(0, 7, "Medicamentatie: " . ($c['medicamentatie'] ?: "Nespecificat"), 0, 1);
    
    // Linie de separare între pacienți
    $pdf->Ln(5);
    $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
    $pdf->Ln(5);
}

$pdf->Output();