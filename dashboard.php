<?php
include 'config/db.php';
session_start();


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];


$devices_count = $pdo->prepare("SELECT COUNT(*) FROM devices WHERE user_id = ?");
$devices_count->execute([$user_id]);
$total_devices = $devices_count->fetchColumn();

$online_count = $pdo->prepare("SELECT COUNT(*) FROM devices WHERE user_id = ? AND status = 'online'");
$online_count->execute([$user_id]);
$total_online = $online_count->fetchColumn();

$alerts_count = $pdo->prepare("SELECT COUNT(*) FROM nbi_alerts a JOIN devices d ON a.device_id = d.id WHERE d.user_id = ?");
$alerts_count->execute([$user_id]);
$total_alerts = $alerts_count->fetchColumn();

include 'includes/header.php'; 
?>

<style>
    body, html { margin: 0; padding: 0; overflow-x: hidden; background: #f4f7f6; }
    .wrapper { display: flex; width: 100%; }
    
    .sidebar-custom {
        min-width: 260px;
        background: #1a202c;
        min-height: 100vh;
    }

    .main-content { width: 100%; }

    /* تنسيق بطاقات الاختصارات الجديدة */
    .action-card {
        border: none;
        border-radius: 15px;
        transition: 0.3s;
        text-decoration: none !important;
        background: white;
    }
    .action-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }

    .nav-link-custom {
        color: rgba(255,255,255,0.7);
        padding: 15px 25px;
        display: block;
        text-decoration: none;
    }
    .nav-link-custom.active {
        background: #2d3748;
        color: #fff;
        border-left: 4px solid #3182ce;
    }
</style>

<div class="wrapper">
    <!-- Sidebar -->
    <nav class="sidebar-custom shadow-lg">
        <div class="p-4">
            <h4 class="text-white fw-bold mb-4"><i class="fas fa-shield-alt text-info me-2"></i>NBI Control</h4>
            <div class="mt-4">
                <a href="dashboard.php" class="nav-link-custom active"><i class="fas fa-home me-2"></i> Dashboard</a>
                <a href="manage_devices.php" class="nav-link-custom"><i class="fas fa-laptop me-2"></i> My Devices</a>
                <a href="alerts.php" class="nav-link-custom"><i class="fas fa-bell me-2"></i> Alerts</a>
                <a href="view_metrics.php" class="nav-link-custom"><i class="fas fa-chart-line me-2"></i> Statistics</a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Bar -->
        <div class="bg-white p-4 shadow-sm d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">Welcome, <?php echo $username; ?>!</h4>
            <div class="d-flex align-items-center">
                <span class="badge bg-light text-dark me-3 p-2"><i class="far fa-calendar-alt me-1"></i> <?php echo date('d M Y'); ?></span>
                <a href="logout.php" class="btn btn-sm btn-outline-danger">Logout</a>
            </div>
        </div>

        <div class="container-fluid px-4">
            <!-- 1. Stats Row -->
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm p-3 rounded-4 bg-primary text-white">
                        <div class="d-flex align-items-center">
                            <div class="bg-white bg-opacity-25 p-3 rounded-3 me-3"><i class="fas fa-network-wired fa-lg"></i></div>
                            <div><p class="mb-0 small opacity-75">Total Devices</p><h3 class="fw-bold mb-0"><?php echo $total_devices; ?></h3></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm p-3 rounded-4 bg-success text-white">
                        <div class="d-flex align-items-center">
                            <div class="bg-white bg-opacity-25 p-3 rounded-3 me-3"><i class="fas fa-check-circle fa-lg"></i></div>
                            <div><p class="mb-0 small opacity-75">Online Now</p><h3 class="fw-bold mb-0"><?php echo $total_online; ?></h3></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm p-3 rounded-4 bg-warning text-dark">
                        <div class="d-flex align-items-center">
                            <div class="bg-dark bg-opacity-10 p-3 rounded-3 me-3"><i class="fas fa-exclamation-triangle fa-lg"></i></div>
                            <div><p class="mb-0 small opacity-75">Security Alerts</p><h3 class="fw-bold mb-0"><?php echo $total_alerts; ?></h3></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Action Hub (أزرار التحكم السريع للكستمر) -->
            <h5 class="fw-bold mb-3 mt-5">Quick Commands</h5>
            <div class="row g-4 mb-5">
                <div class="col-md-2">
                    <a href="manage_devices.php" class="card action-card shadow-sm p-4 text-center h-100">
                        <i class="fas fa-plus-circle text-primary fa-2x mb-3"></i>
                        <h6 class="fw-bold text-dark">Add Device</h6>
                    </a>
                </div>
                <div class="col-md-2">
                    <a href="view_metrics.php" class="card action-card shadow-sm p-4 text-center h-100">
                        <i class="fas fa-chart-pie text-info fa-2x mb-3"></i>
                        <h6 class="fw-bold text-dark">Analysis</h6>
                    </a>
                </div>
                <div class="col-md-2">
                    <a href="alerts.php" class="card action-card shadow-sm p-4 text-center h-100">
                        <i class="fas fa-shield-virus text-danger fa-2x mb-3"></i>
                        <h6 class="fw-bold text-dark">Threats</h6>
                    </a>
                </div>
                <div class="col-md-2">
                    <a href="reports.php" class="card action-card shadow-sm p-4 text-center h-100">
                        <i class="fas fa-file-pdf text-warning fa-2x mb-3"></i>
                        <h6 class="fw-bold text-dark">Reports</h6>
                    </a>
                </div>
                <div class="col-md-2">
                    <a href="chatbot.php" class="card action-card shadow-sm p-4 text-center h-100">
                        <i class="fas fa-robot text-purple fa-2x mb-3"></i>
                        <h6 class="fw-bold text-dark">AI Chat</h6>
                    </a>
                </div>
                <div class="col-md-2">
                    <a href="contact.php" class="card action-card shadow-sm p-4 text-center h-100">
                        <i class="fas fa-headset text-success fa-2x mb-3"></i>
                        <h6 class="fw-bold text-dark">Support</h6>
                    </a>
                </div>
            </div>

            <!-- 3. Table Row -->
            <div class="card border-0 shadow-sm rounded-4 mb-5">
                <div class="card-header bg-white border-0 p-4 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">My Connected Devices</h5>
                    <button class="btn btn-sm btn-light border">Refresh List</button>
                </div>
                <div class="table-responsive p-4">
                    <table class="table table-hover align-middle">
                        <thead class="bg-light">
                            <tr class="text-secondary small">
                                <th>DEVICE NAME</th>
                                <th>IP ADDRESS</th>
                                <th>STATUS</th>
                                <th class="text-center">ACTION</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmt = $pdo->prepare("SELECT * FROM devices WHERE user_id = ?");
                            $stmt->execute([$user_id]);
                            while ($device = $stmt->fetch()) {
                                $status_badge = ($device['status'] == 'online') ? 'bg-success' : 'bg-danger';
                                $ip = !empty($device['ip_address']) ? $device['ip_address'] : '127.0.0.1';
                                echo "<tr>
                                    <td><div class='d-flex align-items-center'><i class='fas fa-desktop text-muted me-3'></i><span class='fw-bold'>{$device['device_name']}</span></div></td>
                                    <td><code class='text-primary px-2 bg-light rounded'>$ip</code></td>
                                    <td><span class='badge rounded-pill $status_badge px-3'>{$device['status']}</span></td>
                                    <td class='text-center'>
                                        <a href='view_metrics.php?id={$device['id']}' class='btn btn-sm btn-primary rounded-pill px-3'>Monitor</a>
                                    </td>
                                </tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>