<?php
include 'config/db.php';
session_start();

// التحقق من صلاحية الأدمن
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$msg = "";

// --- 1. عملية الحذف ---
if (isset($_GET['delete_id'])) {
    $id_to_delete = $_GET['delete_id'];
    if ($id_to_delete != $_SESSION['user_id']) {
        $delete_stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $delete_stmt->execute([$id_to_delete]);
        header("Location: admin_manage_users.php?msg=deleted");
        exit();
    }
}

// --- 2. عملية الإضافة (Add User) ---
if (isset($_POST['add_user'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    try {
        $add_stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
        $add_stmt->execute([$username, $email, $password, $role]);
        header("Location: admin_manage_users.php?msg=added");
        exit();
    } catch (PDOException $e) {
        $msg = "Error: Email or Username already exists!";
    }
}

// جلب جميع المستخدمين
$users = $pdo->query("SELECT * FROM users WHERE id != {$_SESSION['user_id']} ORDER BY id DESC")->fetchAll();
?>

<?php include 'includes/header.php'; ?>

<style>
    body { background-color: #0b111e; color: #f8fafc; }
    
    /* ستايل الجدول الليلي */
    .custom-card {
        background: #111827;
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 15px;
        overflow: hidden;
    }

    .table { color: #f8fafc; margin-bottom: 0; }
    .table thead { background: rgba(255, 255, 255, 0.03); }
    .table thead th { border-bottom: 1px solid rgba(255, 255, 255, 0.08); color: #94a3b8; font-weight: 600; text-transform: uppercase; font-size: 0.8rem; padding: 15px; }
    .table tbody td { border-bottom: 1px solid rgba(255, 255, 255, 0.05); padding: 15px; vertical-align: middle; }
    
    .table-hover tbody tr:hover { background: rgba(56, 189, 248, 0.03); }

    /* شارات الأدوار (Badges) */
    .role-badge { padding: 5px 12px; border-radius: 8px; font-size: 0.75rem; font-weight: 600; }
    .role-admin { background: rgba(244, 63, 94, 0.1); color: #f43f5e; }
    .role-analyst { background: rgba(56, 189, 248, 0.1); color: #38bdf8; }
    .role-user { background: rgba(167, 139, 250, 0.1); color: #a78bfa; }

    /* ستايل المودال (Popup) */
    .modal-content { background: #111827; border: 1px solid rgba(56, 189, 248, 0.2); color: white; }
    .modal-header { border-bottom: 1px solid rgba(255, 255, 255, 0.05); }
    .modal-footer { border-top: 1px solid rgba(255, 255, 255, 0.05); }
    .form-control, .form-select { background: #1f2937; border: 1px solid #374151; color: white; }
    .form-control:focus { background: #1f2937; border-color: #38bdf8; color: white; box-shadow: none; }

    .btn-add { background: #38bdf8; color: #080b12; font-weight: 600; border-radius: 10px; border: none; transition: 0.3s; }
    .btn-add:hover { background: #0ea5e9; transform: translateY(-2px); }
</style>

<div class="container py-5">
    
    <?php if(isset($_GET['msg'])): ?>
        <div class="alert alert-success bg-success bg-opacity-10 border-success text-success alert-dismissible fade show rounded-4" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            <?php 
                if($_GET['msg'] == 'deleted') echo "User account has been permanently removed.";
                if($_GET['msg'] == 'added') echo "New user successfully integrated into the system.";
            ?>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1"><i class="fas fa-user-shield text-info me-2"></i>User Management</h2>
            <p class="text-muted small mb-0">Total system accounts: <?php echo count($users); ?></p>
        </div>
        <button class="btn btn-add px-4 py-2 shadow" data-bs-toggle="modal" data-bs-target="#addUserModal">
            <i class="fas fa-plus-circle me-2"></i>Add New User
        </button>
    </div>

    <div class="custom-card shadow-lg">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th class="ps-4">ID</th>
                        <th>User Profile</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th class="text-center">Operations</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td class="ps-4 text-muted"><?php echo $user['id']; ?></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="bg-secondary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                    <i class="fas fa-user text-info"></i>
                                </div>
                                <div>
                                    <div class="fw-bold"><?php echo $user['username']; ?></div>
                                    <div class="small text-muted"><?php echo $user['email']; ?></div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <?php 
                                $roleClass = 'role-user';
                                if($user['role'] === 'admin') $roleClass = 'role-admin';
                                if($user['role'] === 'analyst') $roleClass = 'role-analyst';
                            ?>
                            <span class="role-badge <?php echo $roleClass; ?>">
                                <?php echo strtoupper($user['role']); ?>
                            </span>
                        </td>
                        <td>
                            <span class="text-success small fw-bold">
                                <i class="fas fa-dot-circle me-1"></i> ACTIVE
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="btn-group shadow-sm">
                                <a href="view_metrics.php?user_id=<?php echo $user['id']; ?>" class="btn btn-sm btn-dark border-secondary text-warning" title="Monitor Traffic">
                                    <i class="fas fa-chart-line"></i>
                                </a>
                                <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-dark border-secondary text-info" title="Modify">
                                    <i class="fas fa-user-edit"></i>
                                </a>
                                <a href="admin_manage_users.php?delete_id=<?php echo $user['id']; ?>" 
                                   class="btn btn-sm btn-dark border-secondary text-danger" 
                                   onclick="return confirm('Security Warning: Are you sure you want to delete this user?')">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="admin_manage_users.php" method="POST">
            <div class="modal-content shadow-lg">
                <div class="modal-header border-0 bg-dark bg-opacity-25">
                    <h5 class="modal-title fw-bold text-info"><i class="fas fa-user-plus me-2"></i>Create New Account</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label text-muted small">System Username</label>
                            <input type="text" name="username" class="form-control rounded-3" placeholder="e.g. jdoe_nbi" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted small">Corporate Email</label>
                            <input type="email" name="email" class="form-control rounded-3" placeholder="name@domain.com" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted small">Secure Password</label>
                            <input type="password" name="password" class="form-control rounded-3" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted small">Assign Authority Role</label>
                            <select name="role" class="form-select rounded-3">
                                <option value="user">Customer (Standard User)</option>
                                <option value="analyst">Network Analyst</option>
                                <option value="admin">System Administrator</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-link text-muted text-decoration-none" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="add_user" class="btn btn-add px-4">Initialize User</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>