<?php
include 'config/db.php';
session_start();

if ($_SESSION['role'] !== 'admin') { exit("Access Denied"); }

if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    
    if ($_GET['action'] === 'delete') {
  
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role != 'admin'");
        $stmt->execute([$id]);
    } 
    elseif ($_GET['action'] === 'suspend') {
        
    }
    
    header("Location: admin_dashboard.php?msg=success");
    exit();
}