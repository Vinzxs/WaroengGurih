<?php
// Panggil TCPDF library
require_once('tcpdf/tcpdf.php');

// Mulai session
session_start();
if (!isset($_SESSION['operator'])) {
    header("Location: login.php");
    exit();
}

// Include database connection
include "connect/connect.php";

// Retrieve orders from the database grouped by table_number
$sql = "SELECT table_number, GROUP_CONCAT(item_name SEPARATOR ', ') AS items, SUM(item_price) AS total_price, status FROM orders GROUP BY table_number, status ORDER BY id DESC";
$result = $conn->query($sql);

$orders = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
}

$conn->close();

// Function to escape special characters for HTML output
function htmlEscape($value) {
    return htmlspecialchars($value, ENT_QUOTES);
}

// Function to format number as currency (Rupiah)
function formatCurrency($value) {
    return 'Rp ' . number_format($value);
}

// Create new PDF document
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Waroeng Gurih');
$pdf->SetTitle('Laporan Pesanan');
$pdf->SetSubject('Laporan Pesanan');
$pdf->SetKeywords('TCPDF, PDF, laporan, pesanan');

// Set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// Set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// Set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// Set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// Add a page
$pdf->AddPage();

// Content
$html = '
<style>
    h1 {
        text-align: center;
        font-size: 24px;
        margin-bottom: 20px;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }
    th {
        background-color: #f2f2f2;
        font-weight: bold;
        text-align: center;
        padding: 8px;
        border: 1px solid #ddd;
    }
    td {
        padding: 8px;
        border: 1px solid #ddd;
        text-align: center;
    }
</style>
<h1>Laporan Pesanan</h1>
<table>
    <tr>
        <th>Nomor Meja</th>
        <th>Items</th>
        <th>Total Harga</th>
        <th>Status Pembayaran</th>
    </tr>';

foreach ($orders as $order) {
    $html .= '<tr>';
    $html .= '<td>' . htmlEscape($order['table_number']) . '</td>';
    $html .= '<td>' . htmlEscape($order['items']) . '</td>';
    $html .= '<td>' . formatCurrency($order['total_price']) . '</td>';
    $html .= '<td>' . htmlEscape($order['status']) . '</td>';
    $html .= '</tr>';
}

$html .= '</table>';

// Write HTML content to PDF
$pdf->writeHTML($html, true, false, true, false, '');

// Output PDF to browser
$pdf->Output('laporan_pesanan.pdf', 'I');
?>
