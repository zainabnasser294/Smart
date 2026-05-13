<?php
include 'config/db.php';

// 1. استقبال معرف الجهاز (ID) من الرابط
$device_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($device_id > 0) {
    // 2. استعلام يجلب البيانات الخاصة بهذا الجهاز المحدّد فقط
    $sql = "SELECT 
                cpu_usage, 
                ram_usage, 
                network_in, 
                network_out, 
                captured_at 
            FROM metrics 
            WHERE device_id = ? 
            ORDER BY id DESC 
            LIMIT 20";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$device_id]);

    // 3. جلب النتائج وعكسها للرسم البياني
    $results = array_reverse($stmt->fetchAll(PDO::FETCH_ASSOC));
} else {
    $results = [];
}

// 4. إرسال البيانات بتنسيق JSON
header('Content-Type: application/json');
echo json_encode($results);
?>