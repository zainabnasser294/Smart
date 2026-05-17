<?php
session_start();
include 'config/db.php';

if (!isset($_SESSION['user_id'])) { exit("Access Denied"); }

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: reports.php");
    exit();
}

$device_id = intval($_POST['device_id']);
$format = $_POST['format'];

$stmt_dev = $pdo->prepare("SELECT * FROM devices WHERE id = ?");
$stmt_dev->execute([$device_id]);
$device = $stmt_dev->fetch();

if (!$device) { exit("Node not found."); }

$metrics_stmt = $pdo->prepare("SELECT * FROM metrics WHERE device_id = ? ORDER BY captured_at DESC LIMIT 100");
$metrics_stmt->execute([$device_id]);
$data = $metrics_stmt->fetchAll();

$libraries_installed = file_exists('vendor/autoload.php');

if ($format == 'pdf') {
    if (!$libraries_installed) {
        die("PDF Library not installed. Please run composer install.");
    }
    
    require 'vendor/autoload.php';
    $dompdf = new \Dompdf\Dompdf();
    
    $html = "
    <style>
        body { font-family: sans-serif; color: #333; }
        h1 { color: #1e293b; border-bottom: 2px solid #38bdf8; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 12px; }
        th { background-color: #f1f5f9; }
    </style>
    <h1>NBI Smart Audit Report</h1>
    <p>Device: {$device['device_name']} ({$device['device_ip']})</p>
    <table>
        <thead>
            <tr>
                <th>Timestamp</th>
                <th>CPU %</th>
                <th>RAM %</th>
                <th>Latency</th>
                <th>Intelligence Note</th>
            </tr>
        </thead>
        <tbody>";
    foreach($data as $r) {
        $html .= "<tr>
            <td>{$r['captured_at']}</td>
            <td>{$r['cpu_usage']}%</td>
            <td>{$r['ram_usage']}%</td>
            <td>{$r['latency']}ms</td>
            <td>{$r['reason']}</td>
        </tr>";
    }
    $html .= "</tbody></table>";

    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();
    $dompdf->stream("NBI_Report_".$device['device_name']."_".date('Ymd').".pdf");

} else {
   
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="NBI_Report_'.$device['device_name'].'_'.date('Ymd').'.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['Timestamp', 'CPU %', 'RAM %', 'Latency (ms)', 'Disk %', 'Uptime', 'Reason']);
    foreach($data as $r) {
        fputcsv($out, [
            $r['captured_at'], $r['cpu_usage'], $r['ram_usage'], $r['latency'], 
            $r['disk_usage'], $r['uptime'], $r['reason']
        ]);
    }
    fclose($out);
}
?>
