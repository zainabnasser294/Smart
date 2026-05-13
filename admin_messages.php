<?php
include 'config/db.php';
session_start();
if ($_SESSION['role'] !== 'admin') exit('Access Denied');

$messages = $pdo->query("SELECT * FROM contact_messages ORDER BY id DESC")->fetchAll();
include 'includes/header.php';
?>
<div class="container mt-5">
    <h2 class="fw-bold mb-4"><i class="fas fa-envelope-open-text"></i> Customer Inquiries</h2>
    <div class="row">
        <?php foreach ($messages as $msg): ?>
        <div class="col-md-6 mb-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 border-start border-4 border-info">
                <div class="d-flex justify-content-between">
                    <h6 class="fw-bold text-primary"><?php echo $msg['subject']; ?></h6>
                    <small class="text-muted"><?php echo $msg['created_at']; ?></small>
                </div>
                <p class="small text-dark mb-2"><?php echo $msg['message']; ?></p>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="small text-muted">From: <?php echo $msg['email']; ?></span>
                    <a href="mailto:<?php echo $msg['email']; ?>" class="btn btn-sm btn-info text-white rounded-pill">Reply</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>