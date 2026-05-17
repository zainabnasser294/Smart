<?php
header("Content-Type: application/json");


include '../config/db.php'; 


$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);


if (!$data || !isset($data['api_key'])) {
    echo json_encode(["status" => "error", "message" => "Invalid Request"]);
    exit;
}

$api_key = $data['api_key'];
$sender_ip = $_SERVER['REMOTE_ADDR']; 

// 3. التحقق من وجود الجهاز وصلاحية المفتاح
$stmt = $pdo->prepare("SELECT id FROM devices WHERE api_key = ?");
$stmt->execute([$api_key]);
$device = $stmt->fetch();

if ($device) {
    $device_id = $device['id'];
    
    // استخراج البيانات القادمة من البايثون
    $cpu    = $data['cpu_usage'] ?? 0;
    $ram    = $data['ram_usage'] ?? 0;
    $net_in = $data['network_in'] ?? 0;
    $net_out = $data['network_out'] ?? 0;
    $pkts   = $data['packet_count'] ?? 0;
    $disk   = $data['disk_usage'] ?? 0;
    $latency = $data['latency'] ?? 0;
    $active_conn = $data['active_connections'] ?? 0;
    $uptime = $data['uptime'] ?? 'N/A';

    $is_abnormal = 0;
    $reason = "Normal Activity";
    $severity_level = 'low'; // Default

    // --- Professional NBI Intelligence Engine (Tiered Analysis) ---
    
    // 1. CPU Analysis (Tiered)
    if ($cpu >= 90) {
        $is_abnormal = 1;
        $severity_level = 'critical';
        $reason = "Critical: Intensive activity or possible malware threat (CPU: $cpu%)";
    } elseif ($cpu >= 70) {
        $is_abnormal = 1;
        $severity_level = 'medium';
        $reason = "Warning: High CPU load detected (CPU: $cpu%)";
    }

    // 2. RAM Analysis (Tiered)
    if ($ram >= 90) {
        $is_abnormal = 1;
        $severity_level = 'critical';
        $reason = "Critical: Memory exhaustion or heavy load (RAM: $ram%)";
    } elseif ($ram >= 75 && !$is_abnormal) {
        $is_abnormal = 1;
        $severity_level = 'medium';
        $reason = "Warning: RAM usage is approaching limits (RAM: $ram%)";
    }

    // 3. Network Latency Analysis (Tiered)
    if ($latency >= 200) {
        $is_abnormal = 1;
        $severity_level = 'critical';
        $reason = "Critical: Severe network instability or congestion (Ping: $latency ms)";
    } elseif ($latency >= 100 && !$is_abnormal) {
        $is_abnormal = 1;
        $severity_level = 'low';
        $reason = "Warning: Network latency is slightly high (Ping: $latency ms)";
    }

    try {
        // 4. إدخال البيانات في جدول المراقبة (metrics)
        $sql = "INSERT INTO metrics (device_id, cpu_usage, ram_usage, disk_usage, network_in, network_out, uptime, latency, active_connections, is_abnormal, reason) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$device_id, $cpu, $ram, $disk, $net_in, $net_out, $uptime, $latency, $active_conn, $is_abnormal, $reason]);

        if ($is_abnormal) {
            $alert_sql = "INSERT INTO nbi_alerts (device_id, alert_type, severity, description, is_read) 
                          VALUES (?, ?, ?, ?, 0)";
            $alert_stmt = $pdo->prepare($alert_sql);
            $alert_stmt->execute([$device_id, 'NBI Anomaly', $severity_level, $reason]);
        }

        // 5. تحديث حالة الجهاز في جدول (devices) ليظهر كـ Online
        $update_sql = "UPDATE devices SET 
                        status = 'online', 
                        last_seen = NOW(), 
                        ip_address = ? 
                      WHERE id = ?";
        $update_stmt = $pdo->prepare($update_sql);
        $update_stmt->execute([$sender_ip, $device_id]);

        echo json_encode([
            "status" => "success", 
            "message" => "NBI analysis successful",
            "intelligence" => $reason,
            "status_code" => $is_abnormal
        ]);

    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
    }

} else {
    echo json_encode(["status" => "error", "message" => "Unauthorized: API Key not found"]);
}
?>