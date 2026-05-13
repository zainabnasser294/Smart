<?php
session_start();
include 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$is_elevated = ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'analyst');

$sql = $is_elevated ? "SELECT * FROM devices" : "SELECT * FROM devices WHERE user_id = ?";
$stmt = $pdo->prepare($sql);
$is_elevated ? $stmt->execute() : $stmt->execute([$user_id]);
$devices = $stmt->fetchAll();

$selected_device = $_GET['device_id'] ?? '';

include 'includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card p-5 border-info shadow-lg bg-dark text-white">
            <h2 class="fw-bold text-center mb-2"><i class="fas fa-file-invoice text-success me-2"></i> Analytical Reports</h2>
            <p class="text-muted text-center mb-5">Select a node to export detailed behavior and metrics audit.</p>

            <form action="generate_report.php" method="POST">
                <div class="mb-4">
                    <label class="small fw-bold text-muted mb-2">TARGET NODE</label>
                    <select name="device_id" class="form-select p-3 bg-secondary text-white border-0" required>
                        <option value="">-- Choose machine to audit --</option>
                        <?php foreach($devices as $dev): ?>
                            <option value="<?php echo $dev['id']; ?>" <?php echo ($selected_device == $dev['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($dev['device_name']); ?> (<?php echo $dev['device_ip']; ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="row g-3 mb-5">
                    <div class="col-6">
                        <input type="radio" class="btn-check" name="format" id="pdf" value="pdf" checked>
                        <label class="btn btn-outline-danger w-100 p-4" for="pdf">
                            <i class="fas fa-file-pdf fa-2x d-block mb-2"></i> PDF Document
                        </label>
                    </div>
                    <div class="col-6">
                        <input type="radio" class="btn-check" name="format" id="excel" value="excel">
                        <label class="btn btn-outline-success w-100 p-4" for="excel">
                            <i class="fas fa-file-excel fa-2x d-block mb-2"></i> Excel / CSV
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-3 fw-bold shadow">GENERATE AUDIT REPORT</button>
            </form>
            
            <div class="alert bg-black border-secondary mt-5 small">
                <i class="fas fa-info-circle text-info me-2"></i> 
                <strong>Note:</strong> Reports include timestamped records of CPU, RAM, Latency, and NBI Intelligence notes.
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
