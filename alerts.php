<?php
include 'config/db.php'; 
include 'includes/header.php'; 


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$device_id = isset($_GET['id']) ? intval($_GET['id']) : 0;


if ($device_id == 0) {
    $find_device = $pdo->prepare("SELECT id FROM devices WHERE user_id = ? LIMIT 1");
    $find_device->execute([$user_id]);
    $device_row = $find_device->fetch();
    if ($device_row) {
        $device_id = $device_row['id'];
    }
}


$metrics_stmt = $pdo->prepare("SELECT * FROM metrics WHERE device_id = ? ORDER BY captured_at DESC LIMIT 10");
$metrics_stmt->execute([$device_id]);
$all_metrics = $metrics_stmt->fetchAll();
$latest = !empty($all_metrics) ? $all_metrics[0] : null;
$cpu = $latest ? ($latest['cpu_usage'] ?? 0) : 0;
$ram = $latest ? ($latest['ram_usage'] ?? 0) : 0;
$is_anomaly = ($cpu > 80 || $ram > 90);
?>

<style>
    .status-box { border-radius: 15px; padding: 40px; margin-bottom: 30px; text-align: center; transition: 0.3s; }
    .status-safe { background: #e6fffa; border: 2px solid #38a169; color: #2f855a; }
    .status-danger { background: #fff5f5; border: 2px solid #e53e3e; color: #c53030; }
    .metric-card { background: white; border-radius: 12px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); height: 100%; }
    .percentage { font-size: 2.5rem; font-weight: bold; }
    .table-container { background: white; border-radius: 15px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
</style>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-brain text-info me-2"></i> NBI Behavior Analysis</h2>
        <div>
            <span class="badge bg-dark p-2">Device ID: <?php echo $device_id; ?></span>
            <span class="badge bg-primary p-2">User ID: <?php echo $user_id; ?></span>
        </div>
    </div>

   
    <?php if ($is_anomaly): ?>
        <div class="status-box status-danger shadow-sm animate__animated animate__shakeX">
            <i class="fas fa-exclamation-triangle fa-4x mb-3"></i>
            <h3>Anomaly Detected!</h3>
            <p>System behavior is outside normal parameters (High Resource Usage).</p>
        </div>
    <?php else: ?>
        <div class="status-box status-safe shadow-sm">
            <i class="fas fa-check-circle fa-4x mb-3"></i>
            <h3>System Behavior is Normal</h3>
            <p class="mb-0">NBI Intelligence confirms all network activities are within safe limits.</p>
        </div>
    <?php endif; ?>

    <!-- عرض القراءات الحالية -->
    <div class="row mb-5">
        <div class="col-md-6">
            <div class="metric-card text-center border-top border-4 <?php echo ($cpu > 80) ? 'border-danger' : 'border-success'; ?>">
                <div class="text-muted small text-uppercase fw-bold">Current CPU Load</div>
                <div class="percentage <?php echo ($cpu > 80) ? 'text-danger' : 'text-success'; ?>"><?php echo $cpu; ?>%</div>
                <div class="progress mt-2" style="height: 10px;">
                    <div class="progress-bar <?php echo ($cpu > 80) ? 'bg-danger' : 'bg-success'; ?>" style="width: <?php echo $cpu; ?>%"></div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="metric-card text-center border-top border-4 <?php echo ($ram > 90) ? 'border-danger' : 'border-success'; ?>">
                <div class="text-muted small text-uppercase fw-bold">Current RAM Usage</div>
                <div class="percentage <?php echo ($ram > 90) ? 'text-danger' : 'text-success'; ?>"><?php echo $ram; ?>%</div>
                <div class="progress mt-2" style="height: 10px;">
                    <div class="progress-bar <?php echo ($ram > 90) ? 'bg-danger' : 'bg-success'; ?>" style="width: <?php echo $ram; ?>%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- جدول آخر 10 قراءات -->
    <div class="table-container mb-5">
        <h5 class="fw-bold mb-4"><i class="fas fa-history me-2 text-secondary"></i> Last 10 System Logs</h5>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Time</th>
                        <th>CPU (%)</th>
                        <th>RAM (%)</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($all_metrics as $row): ?>
                    <tr>
                        <td class="small text-muted"><?php echo $row['captured_at']; ?></td>
                        <td><?php echo $row['cpu_usage']; ?>%</td>
                        <td><?php echo $row['ram_usage']; ?>%</td>
                        <td>
                            <?php if ($row['cpu_usage'] > 80 || $row['ram_usage'] > 90): ?>
                                <span class="badge bg-danger">Anomaly</span>
                            <?php else: ?>
                                <span class="badge bg-success">Normal</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>