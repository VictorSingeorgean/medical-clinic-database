<?php
require_once "../rapoarte/fpdf/fpdf.php";

// Preluare imagini din POST
$graf = $_POST['graf'] ?? '';
$barImage = $_POST['bar'] ?? '';
$pieImage = $_POST['pie'] ?? '';

if (!$graf || !$barImage || !$pieImage) {
    exit("Date insuficiente pentru generarea PDF-ului.");
}

// Funcție ajutătoare: convertește base64 în fișier PNG temporar
function saveBase64ImageAsPNG($base64) {
    $data = explode(',', $base64);
    $decoded = base64_decode($data[1]);
    // Fișier temporar cu extensia .png
    $tmp = tempnam(sys_get_temp_dir(), 'chart') . '.png';
    file_put_contents($tmp, $decoded);
    return $tmp;
}

$barTmp = saveBase64ImageAsPNG($barImage);
$pieTmp = saveBase64ImageAsPNG($pieImage);

// Creare PDF
$pdf = new FPDF();
$pdf->AddPage();

// Fonturi DejaVu pentru diacritice
$pdf->AddFont('DejaVu','','DejaVuSans.php');    
$pdf->AddFont('DejaVu','B','DejaVuSans-Bold.php');
$pdf->SetFont('DejaVu','B',16);

$title = ($graf == 'specialitati') ? "Număr pacienți pe specialitate" : "Pacienți cu boli cronice";
$pdf->Cell(0,10,iconv('UTF-8','CP1250//TRANSLIT',$title),0,1,'C');
$pdf->Ln(5);

// Adaugă imagine Bar Chart
$pdf->SetFont('DejaVu','',12);
$pdf->Cell(0,8,"Grafic tip Bar:",0,1);
$pdf->Image($barTmp, null, null, 140);
$pdf->Ln(10);

// Adaugă imagine Pie Chart
$pdf->Cell(0,8,"Grafic tip Pie:",0,1);
$pdf->Image($pieTmp, null, null, 140);

// Șterge fișierele temporare
@unlink($barTmp);
@unlink($pieTmp);

// Trimite PDF în browser
$pdf->Output("I", "statistici_{$graf}.pdf");
