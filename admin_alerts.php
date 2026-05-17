<?php
session_start();
include 'config/db.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'analyst')) {
    header("Location: login.php");
    exit();
}

$pdo->query("UPDATE nbi_alerts SET is_read = 1 WHERE is_read = 0");

$filter_abnormal = isset($_GET['abnormal_only']) && $_GET['abnormal_only'] == '1';
$filter_device = $_GET['device_id'] ?? '';

$sql = "SELECT a.*, d.device_name FROM nbi_alerts a JOIN devices d ON a.device_id = d.id WHERE 1=1";
$params = [];

if ($filter_abnormal) {
    $sql .= " AND a.severity IN ('high', 'critical')";
}
if ($filter_device) {
    $sql .= " AND a.device_id = ?";
    $params[] = $filter_device;
}

$sql .= " ORDER BY a.created_at DESC LIMIT 100";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$alerts = $stmt->fetchAll();

$devices = $pdo->query("SELECT id, device_name FROM devices")->fetchAll();


$recent_readings = [];
if ($filter_device) {
    $stmt_r = $pdo->prepare("SELECT * FROM metrics WHERE device_id = ? ORDER BY captured_at DESC LIMIT 10");
    $stmt_r->execute([$filter_device]);
    $recent_readings = $stmt_r->fetchAll();
}

include 'includes/header.php';
?>

<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <h2 class="fw-bold"><i class="fas fa-shield-virus text-danger me-2"></i> Threat Intelligence Logs</h2>
        <p class="text-muted">Reviewing all NBI detected anomalies across the network fleet.</p>
    </div>
    <div class="col-md-6">
        <form class="row g-2 justify-content-md-end">
            <div class="col-auto">
                <select name="device_id" class="form-select form-select-sm bg-dark text-white border-secondary">
                    <option value="">All Devices</option>
                    <?php foreach($devices as $d): ?>
                        <option value="<?php echo $d['id']; ?>" <?php echo ($filter_device == $d['id']) ? 'selected' : ''; ?>><?php echo $d['device_name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-auto d-flex align-items-center">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="abnormal_only" value="1" id="abnormalCheck" <?php echo $filter_abnormal ? 'checked' : ''; ?>>
                    <label class="form-check-label small text-muted" for="abnormalCheck">Priority Only</label>
                </div>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary btn-sm px-3">Filter</button>
            </div>
        </form>
    </div>
</div>

<?php if ($filter_device): ?>
<div class="mb-5">
    <h4 class="fw-bold mb-3 text-info"><i class="fas fa-history me-2"></i> Last 10 Readings for Selected Device</h4>
    <div class="card border-0 shadow-sm overflow-hidden bg-dark text-white">
        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle mb-0">
                <thead class="bg-black bg-opacity-50">
                    <tr class="small text-muted text-uppercase">
                        <th class="ps-4">CPU %</th>
                        <th>RAM %</th>
                        <th>DISK %</th>
                        <th>PING</th>
                        <th>STATUS</th>
                        <th>TIMESTAMP</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($recent_readings)): ?>
                        <tr><td colspan="6" class="text-center py-4 text-muted">No readings found for this device.</td></tr>
                    <?php else: ?>
                        <?php foreach($recent_readings as $r): ?>
                        <tr class="<?php echo $r['is_abnormal'] ? 'table-danger text-danger fw-bold' : ''; ?>">
                            <td class="ps-4"><?php echo $r['cpu_usage']; ?>%</td>
                            <td><?php echo $r['ram_usage']; ?>%</td>
                            <td><?php echo $r['disk_usage'] ?? '0'; ?>%</td>
                            <td><?php echo $r['latency']; ?>ms</td>
                            <td><?php echo $r['is_abnormal'] ? 'ABNORMAL' : 'NORMAL'; ?></td>
                            <td class="small text-muted"><?php echo $r['captured_at']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<h4 class="fw-bold mb-3 text-danger"><i class="fas fa-exclamation-circle me-2"></i> Anomalies & Intelligence Logs</h4>
<div class="card border-0 shadow-sm overflow-hidden bg-dark text-white">
    <div class="table-responsive">
        <table class="table table-dark table-hover align-middle mb-0">
            <thead class="bg-black bg-opacity-50">
                <tr class="small text-muted text-uppercase">
                    <th class="ps-4">DEVICE</th>
                    <th>THREAT TYPE</th>
                    <th>SEVERITY</th>
                    <th>INTELLIGENCE REASON</th>
                    <th>TIMESTAMP</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($alerts)): ?>
                    <tr><td colspan="5" class="text-center py-5 text-muted">No security events recorded.</td></tr>
                <?php else: ?>
                    <?php foreach($alerts as $a): ?>
                    <tr>
                        <td class="ps-4 fw-bold text-info"><?php echo htmlspecialchars($a['device_name']); ?></td>
                        <td><span class="badge bg-secondary"><?php echo $a['alert_type']; ?></span></td>
                        <td>
                            <?php $c = ($a['severity'] == 'critical') ? 'danger' : 'warning'; ?>
                            <span class="badge bg-<?php echo $c; ?> text-uppercase"><?php echo $a['severity']; ?></span>
                        </td>
                        <td class="small"><?php echo htmlspecialchars($a['description']); ?></td>
                        <td class="text-muted small"><?php echo $a['created_at']; ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
