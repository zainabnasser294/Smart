<?php
include 'config/db.php';
session_start();


if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}


if (!isset($_GET['id'])) {
    header("Location: admin_dashboard.php");
    exit();
}

$user_id = $_GET['id'];


$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$customer = $stmt->fetch();


$stmt = $pdo->prepare("SELECT * FROM devices WHERE user_id = ?");
$stmt->execute([$user_id]);
$user_devices = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="container mt-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="admin_dashboard.php">Admin Panel</a></li>
            <li class="breadcrumb-item active">Customer Details: <?php echo $customer['username']; ?></li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body text-center p-4">
                    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="fas fa-user fa-3x text-primary"></i>
                    </div>
                    <h4 class="fw-bold"><?php echo $customer['username']; ?></h4>
                    <p class="text-muted small"><?php echo $customer['email']; ?></p>
                    <hr>
                    <div class="d-grid gap-2">
                        <button class="btn btn-warning btn-sm"><i class="fas fa-key"></i> Reset Password</button>
                        <a href="admin_actions.php?action=delete&id=<?php echo $customer['id']; ?>" 
                           class="btn btn-danger btn-sm" 
                           onclick="return confirm('حذف نهائي؟');">
                           <i class="fas fa-user-times"></i> Terminate Account
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4"><i class="fas fa-laptop-code text-info"></i> Registered Devices</h5>
                    
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Device Name</th>
                                    <th>Status</th>
                                    <th>Last Seen</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($user_devices)): ?>
                                    <tr><td colspan="4" class="text-center py-4 text-muted">This customer has no devices yet.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($user_devices as $device): ?>
                                    <tr>
                                        <td><strong><?php echo $device['device_name']; ?></strong></td>
                                        <td><span class="badge bg-success">Online</span></td>
                                        <td><?php echo $device['created_at']; ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-dark"><i class="fas fa-eye"></i> View Logs</button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>