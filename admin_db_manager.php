<?php
include 'config/db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}


$tables = $pdo->query("SHOW TABLE STATUS")->fetchAll();
?>

<?php include 'includes/header.php'; ?>

<div class="container mt-5">
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-dark text-white p-4">
            <h4 class="fw-bold mb-0"><i class="fas fa-database me-2"></i> Database Engine Manager</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Table Name</th>
                            <th>Records (Rows)</th>
                            <th>Data Size</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tables as $table): ?>
                        <tr>
                            <td class="fw-bold text-primary"><?php echo $table['Name']; ?></td>
                            <td><?php echo $table['Rows']; ?>register</td>
                            <td><?php echo round($table['Data_length'] / 1024, 2); ?> KB</td>
                            <td><span class="badge bg-success">Healthy</span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>