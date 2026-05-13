<?php
include 'config/db.php';
$stmt = $pdo->query("DESCRIBE contact_messages");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
?>
