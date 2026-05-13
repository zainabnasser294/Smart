<?php
session_start();
include 'config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'analyst') {
    header("Location: login.php");
    exit();
}

$analyst_id = $_SESSION['user_id'];
$devices = $pdo->query("SELECT id, device_name FROM devices")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_report'])) {
    $device_id = $_POST['device_id'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];
    
    $stmt = $pdo->prepare("INSERT INTO analyst_reports (analyst_id, device_id, subject, message) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$analyst_id, $device_id, $subject, $message])) {
        $success = "Report sent to Admin successfully!";
    }
}

// Fetch stats for dashboard
$total_devices = count($devices);
$pending_alerts = $pdo->query("SELECT COUNT(*) FROM nbi_alerts WHERE is_read = 0")->fetchColumn();
$recent_alerts = $pdo->query("SELECT a.*, d.device_name FROM nbi_alerts a JOIN devices d ON a.device_id = d.id ORDER BY a.created_at DESC LIMIT 5")->fetchAll();

include 'includes/header.php';
?>

<style>
    /* تحسين وضوح النصوص في الوضع الليلي */
    .text-light-gray { color: #cbd5e1 !important; }
    .card-header h5 { color: #f8fafc !important; }
    .form-label { color: #94a3b8 !important; font-weight: 600; }
    .stat-label { color: #94a3b8 !important; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1px; }
    .btn-outline-info { color: #38bdf8 !important; border-color: #38bdf8 !important; }
    .btn-outline-info:hover { background-color: #38bdf8 !important; color: #0f172a !important; }
</style>

<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <h2 class="fw-bold text-white"><i class="fas fa-microscope text-info me-2"></i> Analyst Hub</h2>
        <p class="text-light-gray">Monitor threats, analyze logs, and communicate with administration.</p>
    </div>
    <div class="col-md-6 text-md-end">
        <span class="badge bg-info p-2 px-3 rounded-pill text-dark fw-bold"><i class="fas fa-clock me-2"></i> Session Active</span>
    </div>
</div>

<?php if(isset($success)): ?>
    <div class="alert alert-success border-0 shadow-sm mb-4"><?php echo $success; ?></div>
<?php endif; ?>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card bg-dark border-secondary p-4 shadow-sm">
            <div class="stat-label mb-2">Total Fleet</div>
            <h2 class="fw-bold mb-0 text-white"><?php echo $total_devices; ?></h2>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-dark border-secondary p-4 shadow-sm">
            <div class="stat-label mb-2">Pending Alerts</div>
            <h2 class="fw-bold mb-0 text-danger"><?php echo $pending_alerts; ?></h2>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Left Column: Quick Actions & Communication -->
    <div class="col-md-5">
        <div class="card bg-dark border-secondary shadow-sm mb-4">
            <div class="card-header bg-black bg-opacity-50 border-secondary py-3">
                <h5 class="mb-0 fw-bold"><i class="fas fa-paper-plane text-primary me-2"></i> Send Report to Admin</h5>
            </div>
            <div class="card-body p-4">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Target Device</label>
                        <select name="device_id" class="form-select bg-dark text-white border-secondary" required>
                            <option value="">Select Device...</option>
                            <?php foreach($devices as $d): ?>
                                <option value="<?php echo $d['id']; ?>"><?php echo htmlspecialchars($d['device_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Subject</label>
                        <input type="text" name="subject" class="form-control bg-dark text-white border-secondary" placeholder="e.g., Anomaly detected on Node-X" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Findings / Message</label>
                        <textarea name="message" class="form-control bg-dark text-white border-secondary" rows="5" placeholder="Describe the findings..." required></textarea>
                    </div>
                    <button type="submit" name="send_report" class="btn btn-primary w-100 py-2 fw-bold">
                        <i class="fas fa-send me-2"></i> Submit Intelligence Report
                    </button>
                </form>
            </div>
        </div>

        <div class="card bg-dark border-secondary shadow-sm">
            <div class="card-header bg-black bg-opacity-50 border-secondary py-3">
                <h5 class="mb-0 fw-bold"><i class="fas fa-tools text-warning me-2"></i> Analyst Tools</h5>
            </div>
            <div class="card-body p-4">
                <div class="d-grid gap-3">
                    <a href="admin_alerts.php" class="btn btn-outline-info text-start py-2">
                        <i class="fas fa-shield-virus me-2"></i> Detailed Alert Logs
                    </a>
                    <a href="view_master.php" class="btn btn-outline-info text-start py-2">
                        <i class="fas fa-desktop me-2"></i> Fleet Overview
                    </a>
                    <a href="reports.php" class="btn btn-outline-info text-start py-2">
                        <i class="fas fa-file-pdf me-2"></i> Generate Exports
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Recent Activity -->
    <div class="col-md-7">
        <div class="card bg-dark border-secondary shadow-sm h-100">
            <div class="card-header bg-black bg-opacity-50 border-secondary py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold"><i class="fas fa-list-ul text-info me-2"></i> Recent Intelligence Logs</h5>
                <a href="admin_alerts.php" class="btn btn-sm btn-link text-info text-decoration-none">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-dark table-hover mb-0">
                        <thead>
                            <tr class="small text-muted border-secondary">
                                <th class="ps-4 py-3">DEVICE</th>
                                <th class="py-3">FINDINGS</th>
                                <th class="py-3 text-end pe-4">SEVERITY</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($recent_alerts as $ra): ?>
                            <tr class="border-secondary">
                                <td class="ps-4 py-3 fw-bold text-white"><?php echo $ra['device_name']; ?></td>
                                <td class="small text-light-gray py-3"><?php echo $ra['description']; ?></td>
                                <td class="text-end pe-4 py-3">
                                    <span class="badge bg-<?php echo ($ra['severity']=='critical'?'danger':'warning'); ?> small px-2">
                                        <?php echo strtoupper($ra['severity']); ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
