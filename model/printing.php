<?php

include('../controller/connection.php');
require('fpdf.php'); // Include the FPDF library

session_start();
$conn = connect();

$date = date('Y-m-d', strtotime('-7 days'));
$sql = "SELECT * FROM products WHERE updated_at > '$date'";
$prod = $conn->query($sql);

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'Inventory Management', 0, 1, 'C');

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(60, 10, 'Product Name', 1);
$pdf->Cell(30, 10, 'Stock In', 1);
$pdf->Cell(30, 10, 'Sold', 1);
$pdf->Cell(50, 10, 'Available Stock', 1);
$pdf->Ln();

$pdf->SetFont('Arial', '', 12);
if (mysqli_num_rows($prod) > 0) {
    while ($row = mysqli_fetch_assoc($prod)) {
        $stock = $row['bought'] - $row['sold'];
        $pdf->Cell(60, 10, $row['name'], 1);
        $pdf->Cell(30, 10, $row['bought'], 1);
        $pdf->Cell(30, 10, $row['sold'], 1);
        $pdf->Cell(50, 10, $stock, 1);
        $pdf->Ln();
    }
} else {
    $pdf->Cell(0, 10, 'No recent products found!', 1, 1, 'C');
}

$pdf->Output('I', 'inventory_report.pdf'); // Forces download of the PDF
?>
