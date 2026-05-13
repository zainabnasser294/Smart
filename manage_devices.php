<?php 
session_start();
include 'config/db.php'; 

// التحقق من صلاحيات الأدمن
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') { 
    header("Location: login.php"); 
    exit; 
}

include 'includes/header.php'; 

// جلب قائمة المستخدمين لعرضهم في القائمة المنسدلة
$users_stmt = $pdo->query("SELECT id, username FROM users WHERE role != 'admin'");
$all_users = $users_stmt->fetchAll();
?>

<div class="container mt-5" style="max-width: 700px;">
    <div class="card shadow border-0 rounded-4">
        <div class="card-header bg-success text-white p-4 text-center">
            <h3 class="fw-bold mb-0"><i class="fas fa-plus-circle me-2"></i> Register New Device</h3>
            <p class="small mb-0">Assign a machine to a specific user for monitoring.</p>
        </div>
        
        <div class="card-body p-4">
            <form action="" method="POST">
                <div class="mb-3">
                    <label class="form-label fw-bold">Select Device Owner (User):</label>
                    <select name="user_id" class="form-select" required>
                        <option value="">-- Choose User --</option>
                        <?php foreach ($all_users as $u): ?>
                            <option value="<?php echo $u['id']; ?>"><?php echo htmlspecialchars($u['username']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Device Name:</label>
                    <input type="text" name="device_name" class="form-control" placeholder="e.g. Alyamama_PC" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Device IP:</label>
                    <input type="text" name="device_ip" class="form-control" placeholder="e.g. 192.168.68.64" required>
                </div>
                
                <button type="submit" name="add_device" class="btn btn-success w-100 fw-bold p-3">
                    <i class="fas fa-save me-2"></i> Register Device to User
                </button>
            </form>

            <?php
            if (isset($_POST['add_device'])) {
                $user_id = $_POST['user_id'];
                $name = $_POST['device_name'];
                $ip = $_POST['device_ip'];
                $key = bin2hex(random_bytes(16)); 

                try {
                    // الآن نقوم بإدخال الـ user_id مع بيانات الجهاز
                    $sql = "INSERT INTO devices (user_id, device_name, device_ip, api_key, status) VALUES (?, ?, ?, ?, 'online')";
                    $stmt = $pdo->prepare($sql);
                    if ($stmt->execute([$user_id, $name, $ip, $key])) {
                        echo "<div class='alert alert-success mt-4 shadow-sm border-start border-4 border-success'>";
                        echo "<strong><i class='fas fa-check-circle'></i> Success!</strong> Device successfully linked.<br>";
                        echo "<small>API Key for Python Script: </small><code class='bg-light p-1'>$key</code>";
                        echo "</div>";
                    }
                } catch (PDOException $e) {
                    echo "<div class='alert alert-danger mt-4'>Error: " . $e->getMessage() . "</div>";
                }
            }
            ?>
        </div>
        <div class="card-footer text-center bg-light">
            <a href="manage_devices.php" class="text-decoration-none text-muted small"><i class="fas fa-arrow-left"></i> Back to Device Fleet</a>
        </div>
    </div>
</div>

<?php 
include 'includes/footer.php'; 
?>