<?php
session_start();
include 'config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$reports = $pdo->query("SELECT r.*, u.username as analyst_name, d.device_name 
                        FROM analyst_reports r 
                        JOIN users u ON r.analyst_id = u.id 
                        JOIN devices d ON r.device_id = d.id 
                        ORDER BY r.created_at DESC")->fetchAll();

if (isset($_GET['mark_reviewed'])) {
    $stmt = $pdo->prepare("UPDATE analyst_reports SET status = 'reviewed' WHERE id = ?");
    $stmt->execute([$_GET['mark_reviewed']]);
    header("Location: admin_analyst_reports.php");
    exit();
}

include 'includes/header.php';
?>

<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <h2 class="fw-bold text-white"><i class="fas fa-file-signature text-purple me-2"></i> Analyst Intelligence Reports</h2>
        <p class="text-muted">Reviewing reports and findings submitted by the security analysts.</p>
    </div>
</div>

<div class="row g-4">
    <?php if(empty($reports)): ?>
        <div class="col-12 text-center py-5 opacity-50">
            <i class="fas fa-inbox fa-3x mb-3"></i>
            <p>No reports submitted by analysts yet.</p>
        </div>
    <?php else: ?>
        <?php foreach ($reports as $r): ?>
        <div class="col-md-6">
            <div class="card bg-dark border-secondary h-100 shadow-sm">
                <div class="card-header border-secondary d-flex justify-content-between align-items-center">
                    <span class="badge <?php echo ($r['status'] == 'pending' ? 'bg-warning' : 'bg-success'); ?> text-uppercase">
                        <?php echo $r['status']; ?>
                    </span>
                    <small class="text-muted"><?php echo $r['created_at']; ?></small>
                </div>
                <div class="card-body">
                    <h5 class="fw-bold text-info mb-1"><?php echo htmlspecialchars($r['subject']); ?></h5>
                    <p class="small text-muted mb-3">Reported by: <span class="text-white"><?php echo $r['analyst_name']; ?></span> | Device: <span class="text-white"><?php echo $r['device_name']; ?></span></p>
                    <div class="p-3 bg-black bg-opacity-25 rounded border border-secondary mb-3 text-white-50">
                        <?php echo nl2br(htmlspecialchars($r['message'])); ?>
                    </div>
                </div>
                <?php if($r['status'] == 'pending'): ?>
                <div class="card-footer border-secondary text-end">
                    <a href="admin_analyst_reports.php?mark_reviewed=<?php echo $r['id']; ?>" class="btn btn-sm btn-success">
                        <i class="fas fa-check me-1"></i> Mark as Reviewed
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<style>
    .text-purple { color: #a78bfa; }
</style>

<?php include 'includes/footer.php'; ?>
