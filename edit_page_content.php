<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$file = $_GET['file'] ?? '';
$project_dir = __DIR__ . "/";
$target_path = realpath($project_dir . $file);

// حماية: التأكد من أن الملف داخل مجلد المشروع وليس خارجه
if (!$target_path || strpos($target_path, realpath($project_dir)) !== 0 || !is_file($target_path)) {
    die("Invalid file access.");
}

// حفظ التعديلات
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['content'])) {
    file_put_contents($target_path, $_POST['content']);
    $status = "success";
}

$content = file_get_contents($target_path);
include 'includes/header.php';
?>

<div class="row mb-4">
    <div class="col-md-8">
        <h3 class="fw-bold text-white"><i class="fas fa-code me-2"></i> Editing: <?php echo htmlspecialchars($file); ?></h3>
    </div>
    <div class="col-md-4 text-end">
        <a href="admin_pages_manager.php" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-2"></i> Back to Manager
        </a>
    </div>
</div>

<?php if(isset($status)): ?>
    <div class="alert alert-success">File saved successfully!</div>
<?php endif; ?>

<div class="card bg-dark border-secondary">
    <div class="card-body p-0">
        <form method="POST">
            <textarea name="content" class="form-control bg-black text-info border-0 font-monospace" rows="25" style="resize: none; outline: none;"><?php echo htmlspecialchars($content); ?></textarea>
            <div class="card-footer bg-dark border-secondary text-end">
                <button type="submit" class="btn btn-primary px-5">
                    <i class="fas fa-save me-2"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    textarea:focus { box-shadow: none !important; }
</style>

<?php include 'includes/footer.php'; ?>
