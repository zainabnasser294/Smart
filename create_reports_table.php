<?php
include 'config/db.php';
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS analyst_reports (
        id INT AUTO_INCREMENT PRIMARY KEY, 
        analyst_id INT, 
        device_id INT, 
        subject VARCHAR(255), 
        message TEXT, 
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
        status ENUM('pending', 'reviewed') DEFAULT 'pending'
    )");
    echo "Table analyst_reports created successfully.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
