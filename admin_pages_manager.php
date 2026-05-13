<?php
include 'config/db.php';
session_start();

// التحقق من صلاحيات الأدمن
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// تحديد مسار مجلد المشروع ديناميكياً
$project_dir = __DIR__ . "/";
$files = array_diff(scandir($project_dir), array('.', '..', 'config', 'includes', '.git', 'vendor', 'composer.json', 'composer.lock', 'composer.phar')); 

// منطق الحذف
if (isset($_GET['delete_file'])) {
    $file_to_delete = $project_dir . $_GET['delete_file'];
    // التأكد من أن الملف ليس حرجاً (مثل config.php أو login.php) قبل الحذف
    $protected_files = ['config.php', 'login.php', 'admin_pages_manager.php', 'db.php'];
    if (!in_array($_GET['delete_file'], $protected_files) && file_exists($file_to_delete) && is_file($file_to_delete)) {
        unlink($file_to_delete);
        header("Location: admin_pages_manager.php?status=deleted");
        exit();
    }
}

include 'includes/header.php';
?>

<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <h2 class="fw-bold text-white"><i class="fas fa-file-code text-primary me-2"></i> System Page Manager</h2>
        <p class="text-muted">Manage all system files and pages within the workspace.</p>
    </div>
    <div class="col-md-6 text-md-end">
        <button class="btn btn-success shadow-sm" data-bs-toggle="modal" data-bs-target="#addPageModal">
            <i class="fas fa-plus me-2"></i> Create New Page
        </button>
    </div>
</div>

<div class="card bg-dark border-secondary shadow-sm overflow-hidden">
    <div class="table-responsive">
        <table class="table table-dark table-hover align-middle mb-0">
            <thead class="bg-black bg-opacity-50">
                <tr class="small text-muted text-uppercase">
                    <th class="ps-4">FILE NAME</th>
                    <th>TYPE</th>
                    <th>LAST MODIFIED</th>
                    <th class="text-end pe-4">ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($files as $file): ?>
                <?php if(is_file($project_dir . $file)): ?>
                <tr>
                    <td class="ps-4">
                        <div class="d-flex align-items-center">
                            <i class="far fa-file-alt text-info me-3 fa-lg"></i>
                            <span class="fw-bold text-white"><?php echo $file; ?></span>
                        </div>
                    </td>
                    <td>
                        <span class="badge bg-secondary"><?php echo pathinfo($file, PATHINFO_EXTENSION); ?></span>
                    </td>
                    <td class="small text-muted">
                        <?php echo date("Y-m-d H:i", filemtime($project_dir . $file)); ?>
                    </td>
                    <td class="text-end pe-4">
                        <div class="btn-group">
                            <a href="<?php echo $file; ?>" target="_blank" class="btn btn-sm btn-outline-light" title="View Page">
                                <i class="fas fa-external-link-alt"></i>
                            </a>
                            <a href="edit_page_content.php?file=<?php echo $file; ?>" class="btn btn-sm btn-outline-primary" title="Edit Source">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="admin_pages_manager.php?delete_file=<?php echo $file; ?>" 
                               class="btn btn-sm btn-outline-danger" 
                               onclick="return confirm('Are you sure you want to delete this file?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal لإضافة صفحة جديدة -->
<div class="modal fade" id="addPageModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark text-white border-secondary">
            <form action="create_page.php" method="POST">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title">New System Page</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">File Name</label>
                        <input type="text" name="page_name" class="form-control bg-dark text-white border-secondary" placeholder="e.g., custom_page.php" required>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="submit" class="btn btn-primary">Create File</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>