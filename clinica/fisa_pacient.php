<?php
require_once "config/db.php";
require_once "rapoarte/fpdf/fpdf.php";

// Preluare CNP pacient din URL
$cnp = $_GET['cnp'] ?? null;
if (!$cnp) exit("CNP pacient lipsă");

// Preluare date pacient
$stmt = $pdo->prepare("SELECT * FROM pacienti WHERE cnp = ?");
$stmt->execute([$cnp]);
$pacient = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$pacient) exit("Pacient inexistent");

// Preluare consultatii pacient + nume doctor
$stmt = $pdo->prepare("
    SELECT c.*, d.nume AS nume_doctor, d.prenume AS prenume_doctor
    FROM consultatii c
    INNER JOIN doctori d ON c.cnp_doctor = d.cnp_doctor
    WHERE c.cnp_pacient = ?
    ORDER BY c.data_consultatie DESC
");
$stmt->execute([$cnp]);
$consultatii = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Creare PDF
$pdf = new FPDF();
$pdf->AddPage();

// Fonturi: DejaVu Bold pentru titlu
$pdf->AddFont('DejaVu','','DejaVuSans.php');       // normal
$pdf->AddFont('DejaVu','B','DejaVuSans-Bold.php'); // bold

$pdf->SetFont('DejaVu','B',16);
$pdf->Cell(0,10, iconv('UTF-8','CP1250//TRANSLIT',"Fișa pacientului: {$pacient['nume']} {$pacient['prenume']}"),0,1);

$pdf->SetFont('DejaVu','',12);
$pdf->Cell(0,8,iconv('UTF-8','CP1250',"CNP: {$pacient['cnp']}"),0,1);
$pdf->Cell(0,8,iconv('UTF-8','CP1250',"Adresa: {$pacient['adresa']}"),0,1);
$pdf->Cell(0,8, iconv('UTF-8','CP1250//TRANSLIT',"Data nașterii: {$pacient['data_nasterii']}"),0,1);
$pdf->Cell(0,8,iconv('UTF-8','CP1250',"Vârsta: {$pacient['varsta']}"),0,1);
$pdf->Cell(0,8,iconv('UTF-8','CP1250',"Email: {$pacient['email']}"),0,1);
$pdf->Cell(0,8,iconv('UTF-8','CP1250',"Telefon: {$pacient['telefon']}"),0,1);

$pdf->Ln(5);
$pdf->SetFont('DejaVu','B',14);
$pdf->Cell(0,8,iconv('UTF-8','CP1250',"Consultatii:"),0,1);

$pdf->SetFont('DejaVu','',12);
if ($consultatii) {
    foreach ($consultatii as $c) {
        $pdf->Cell(0,8,iconv('UTF-8','CP1250',"Nr: {$c['nr_consultatie']} | Data: {$c['data_consultatie']} | Doctor: {$c['nume_doctor']} {$c['prenume_doctor']}"),0,1);
        $pdf->MultiCell(0,6,iconv('UTF-8','CP1250',"Diagnostic: {$c['diagnostic']} | Medicamentatie: {$c['medicamentatie']}"));
        $pdf->Ln(2);
    }
} else {
    $pdf->Cell(0,8,iconv('UTF-8','CP1250',"Fără consultatii."),0,1);
}
// Trimite PDF în browser
$pdf->Output("I", "fisa_pacient_{$pacient['cnp']}.pdf");
