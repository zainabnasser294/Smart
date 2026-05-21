<?php
session_start();
include 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$is_elevated = ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'analyst');
$user_id = $_SESSION['user_id'];

if ($is_elevated) {
    $devices = $pdo->query("SELECT d.*, u.username FROM devices d JOIN users u ON d.user_id = u.id ORDER BY d.status DESC")->fetchAll();
} else {
    $devices = $pdo->prepare("SELECT d.*, u.username FROM devices d JOIN users u ON d.user_id = u.id WHERE d.user_id = ? ORDER BY d.status DESC");
    $devices->execute([$user_id]);
    $devices = $devices->fetchAll();
}

include 'includes/header.php';
?>

<meta http-equiv="refresh" content="5">

<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <h2 class="fw-bold text-white"><i class="fas fa-desktop text-info me-2"></i> View Master: Fleet Status</h2>
        <p class="text-muted">Overview of all monitored devices and access to historical reports.</p>
    </div>
</div>

<div class="row g-4">
    <?php foreach($devices as $dev): ?>
    <div class="col-md-4">
        <div class="card bg-dark border-secondary shadow-sm h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h5 class="fw-bold text-white mb-0"><?php echo htmlspecialchars($dev['device_name']); ?></h5>
                        <small class="text-muted">Owner: <?php echo htmlspecialchars($dev['username']); ?></small>
                    </div>
                    <?php $st = ($dev['status'] == 'online') ? 'success' : 'danger'; ?>
                    <span class="badge bg-<?php echo $st; ?>"><?php echo strtoupper($dev['status']); ?></span>
                </div>
                
                <hr class="border-secondary">
                
                <div class="d-grid gap-2 mt-4">
                    <a href="view_metrics.php?id=<?php echo $dev['id']; ?>" class="btn btn-sm btn-outline-info">
                        <i class="fas fa-chart-line me-2"></i> Live Metrics
                    </a>
                    <a href="alerts.php?id=<?php echo $dev['id']; ?>" class="btn btn-sm btn-outline-success">
                        <i class="fas fa-brain me-2"></i> Behavior Analysis
                    </a>
                    <a href="reports.php?device_id=<?php echo $dev['id']; ?>" class="btn btn-sm btn-outline-warning">
                        <i class="fas fa-file-invoice me-2"></i> Behavioral Report
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php include 'includes/footer.php'; ?>
