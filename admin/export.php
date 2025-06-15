<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Check if user is logged in
$is_logged_in = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;

if (!$is_logged_in) {
    header('Location: index.php');
    exit;
}

// Get format parameter (csv or excel)
$format = isset($_GET['format']) ? $_GET['format'] : 'csv';

// Get all RSVPs
$rsvps = getAllRSVPs();

// Set headers based on format
if ($format === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="rsvp_data_' . date('Y-m-d') . '.csv"');
    
    // Open output stream
    $output = fopen('php://output', 'w');
    
    // Add BOM for UTF-8 encoding in Excel
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // Add header row
    fputcsv($output, ['No', 'Nama', 'Kehadiran', 'Jumlah Tamu', 'Pesan', 'Tanggal Submit']);
    
    // Add data rows
    foreach ($rsvps as $index => $rsvp) {
        fputcsv($output, [
            $index + 1,
            $rsvp['name'],
            $rsvp['attending'] === 'yes' ? 'Hadir' : 'Tidak Hadir',
            $rsvp['attending'] === 'yes' ? $rsvp['guest_count'] : '-',
            $rsvp['message'] ?: '-',
            formatIndonesianDate($rsvp['created_at'])
        ]);
    }
    
    fclose($output);
    
} elseif ($format === 'excel') {
    // For Excel, we'll use a library like PhpSpreadsheet in a production environment
    // For simplicity, here we'll create an HTML file that can be opened in Excel
    
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="rsvp_data_' . date('Y-m-d') . '.xls"');
    
    echo '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>RSVP Data</title>
</head>
<body>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Kehadiran</th>
                <th>Jumlah Tamu</th>
                <th>Pesan</th>
                <th>Tanggal Submit</th>
            </tr>
        </thead>
        <tbody>';
    
    foreach ($rsvps as $index => $rsvp) {
        echo '<tr>
            <td>' . ($index + 1) . '</td>
            <td>' . htmlspecialchars($rsvp['name']) . '</td>
            <td>' . ($rsvp['attending'] === 'yes' ? 'Hadir' : 'Tidak Hadir') . '</td>
            <td>' . ($rsvp['attending'] === 'yes' ? $rsvp['guest_count'] : '-') . '</td>
            <td>' . htmlspecialchars($rsvp['message'] ?: '-') . '</td>
            <td>' . formatIndonesianDate($rsvp['created_at']) . '</td>
        </tr>';
    }
    
    echo '</tbody>
    </table>
</body>
</html>';
}

exit;
?>
