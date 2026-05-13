<?php
session_start();

// التحقق من صلاحية الأدمن
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include 'config/db.php';

// جلب الإحصائيات المباشرة
try {
    $total_users = $pdo->query("SELECT COUNT(*) FROM users WHERE role != 'admin'")->fetchColumn();
    $total_devices = $pdo->query("SELECT COUNT(*) FROM devices")->fetchColumn();
    $total_alerts = $pdo->query("SELECT COUNT(*) FROM nbi_alerts")->fetchColumn();
} catch (PDOException $e) {
    $error = "Database Error: " . $e->getMessage();
}

include 'includes/header.php'; 
?>

<style>
    /* التناسق مع الستايل الليلي */
    body { background-color: #0b111e; color: #f8fafc; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }

    /* الهيدر بتدرج لوني */
    .admin-header {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        padding: 40px;
        border-radius: 20px;
        margin-bottom: 40px;
        border: 1px solid rgba(56, 189, 248, 0.1);
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }

    /* 1. كروت الإحصائيات (المربعات) - رجعناها مربعات بستايل زجاجي */
    .stat-card-box {
        background: rgba(30, 41, 59, 0.5);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 20px;
        padding: 25px;
        text-align: center;
        height: 100%;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    .stat-card-box:hover {
        transform: translateY(-5px);
        background: rgba(30, 41, 59, 0.7);
        border-color: #38bdf8;
    }

    /* أشرطة جانبية صغيرة ملونة للكروت */
    .stat-card-box::after { content: ""; position: absolute; bottom: 0; left: 0; width: 100%; height: 3px; }
    .stat-blue::after { background: #38bdf8; }
    .stat-purple::after { background: #a78bfa; }
    .stat-red::after { background: #f43f5e; }

    .stat-icon { font-size: 30px; margin-bottom: 15px; }
    .stat-value { font-size: 2rem; font-weight: 800; color: #ffffff; margin-bottom: 5px; }
    .stat-label { font-size: 13px; text-transform: uppercase; color: #94a3b8; letter-spacing: 1px; }

    /* 2. الوحدات الإدارية العريضة (الصفوف التفاعلية) - حافظنا عليها */
    .hub-row-card {
        background: #1a2234;
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 15px;
        padding: 25px 30px;
        text-decoration: none !important;
        transition: all 0.4s ease;
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 15px;
    }
    .hub-row-card:hover {
        background: #1e293b;
        transform: translateX(10px) scale(1.01);
        border-color: rgba(56, 189, 248, 0.3);
        box-shadow: 0 10px 30px rgba(56, 189, 248, 0.05);
    }

    .module-left { display: flex; align-items: center; gap: 20px; flex: 1; }
    .icon-wrapper {
        width: 60px; height: 60px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 26px;
        flex-shrink: 0;
    }
    .hub-row-card:hover .icon-wrapper { transform: rotate(-5deg) scale(1.05); }

    /* ألوان الأيقونات الناعمة */
    .text-soft-blue { color: #38bdf8; }
    .text-soft-purple { color: #a78bfa; }
    .text-soft-red { color: #f43f5e; }
    .text-soft-green { color: #22c55e; }
    .text-soft-amber { color: #f59e0b; }

    .hub-title { color: #f1f5f9; font-weight: 700; font-size: 1.1rem; margin: 0 0 5px 0; }
    .hub-desc { color: #94a3b8; font-size: 0.95rem; line-height: 1.5; margin: 0; }
    .enter-link-right { color: #38bdf8; font-size: 14px; font-weight: 600; display: flex; align-items: center; gap: 8px; flex-shrink: 0; transition: 0.3s; }
    .hub-row-card:hover .enter-link-right { transform: translateX(5px); }

</style>

<div class="container py-5">
    <div class="admin-header">
        <div>
            <h1 class="fw-bold mb-1">Master Control Center</h1>
            <p class="mb-0 opacity-50">Administrator Command & Overview Platform.</p>
        </div>
        <div>
            <div class="badge rounded-pill bg-success bg-opacity-10 text-success border border-success border-opacity-20 px-3 py-2">
                <i class="fas fa-satellite-dish me-2"></i>Live Server Status
            </div>
        </div>
    </div>

    <h4 class="fw-bold mb-4" style="color: #cbd5e1;"><i class="fas fa-brain me-2"></i> System Intelligence</h4>
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <a href="admin_manage_users.php" class="text-decoration-none h-100 d-block">
                <div class="stat-card-box stat-blue">
                    <div class="stat-icon text-soft-blue"><i class="fas fa-users-cog"></i></div>
                    <div class="stat-value"><?php echo $total_users; ?></div>
                    <div class="stat-label">Verified Customers</div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="manage_devices.php" class="text-decoration-none h-100 d-block">
                <div class="stat-card-box stat-purple">
                    <div class="stat-icon text-soft-purple"><i class="fas fa-network-wired"></i></div>
                    <div class="stat-value"><?php echo $total_devices; ?></div>
                    <div class="stat-label">Connected Nodes</div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="admin_alerts.php" class="text-decoration-none h-100 d-block">
                <div class="stat-card-box stat-red">
                    <div class="stat-icon text-soft-red"><i class="fas fa-shield-virus"></i></div>
                    <div class="stat-value text-danger"><?php echo $total_alerts; ?></div>
                    <div class="stat-label text-danger fw-semibold">AI Threat Alerts</div>
                </div>
            </a>
        </div>
    </div>

    <h4 class="fw-bold mb-4" style="color: #cbd5e1;"><i class="fas fa-th-large me-2"></i> Management Modules</h4>
    <div class="row">
        <div class="col-12">
            
            <a href="admin_manage_users.php" class="hub-row-card shadow-sm">
                <div class="module-left">
                    <div class="icon-wrapper bg-secondary bg-opacity-10 text-soft-blue"><i class="fas fa-users-cog"></i></div>
                    <div>
                        <h5 class="hub-title">User Database</h5>
                        <p class="hub-desc">Manage customer credentials, account roles, and security permissions.</p>
                    </div>
                </div>
                <div class="enter-link-right">Open DB <i class="fas fa-chevron-right ms-1"></i></div>
            </a>

            <a href="view_metrics.php" class="hub-row-card shadow-sm">
                <div class="module-left">
                    <div class="icon-wrapper bg-secondary bg-opacity-10 text-soft-purple"><i class="fas fa-project-diagram"></i></div>
                    <div>
                        <h5 class="hub-title">Real-time Metrics</h5>
                        <p class="hub-desc">Open interactive charts showcasing global trends of CPU, RAM, and bandwidth.</p>
                    </div>
                </div>
                <div class="enter-link-right">Analytics <i class="fas fa-chevron-right ms-1"></i></div>
            </a>

            <a href="admin_alerts.php" class="hub-row-card shadow-sm">
                <div class="module-left">
                    <div class="icon-wrapper bg-secondary bg-opacity-10 text-soft-red"><i class="fas fa-shield-virus"></i></div>
                    <div>
                        <h5 class="hub-title">NBI Intelligence</h5>
                        <p class="hub-desc">Review critical security anomalies detected by the AI behavioral engine.</p>
                    </div>
                </div>
                <div class="enter-link-right">Review Threats <i class="fas fa-chevron-right ms-1"></i></div>
            </a>

            <a href="manage_devices.php" class="hub-row-card shadow-sm">
                <div class="module-left">
                    <div class="icon-wrapper bg-secondary bg-opacity-10 text-soft-green"><i class="fas fa-microchip"></i></div>
                    <div>
                        <h5 class="hub-title">Node Fleet Manager</h5>
                        <p class="hub-desc">Audit all registered hardware devices and monitor their connection status.</p>
                    </div>
                </div>
                <div class="enter-link-right">Manage Fleet <i class="fas fa-chevron-right ms-1"></i></div>
            </a>

            <a href="admin_pages_manager.php" class="hub-row-card shadow-sm">
                <div class="module-left">
                    <div class="icon-wrapper bg-secondary bg-opacity-10 text-primary"><i class="fas fa-file-code"></i></div>
                    <div>
                        <h5 class="hub-title">Source Page Editor</h5>
                        <p class="hub-desc">Direct access to modify and update frontend components and system pages.</p>
                    </div>
                </div>
                <div class="enter-link-right text-primary">Open Editor <i class="fas fa-edit ms-1"></i></div>
            </a>

            <a href="admin_messages.php" class="hub-row-card shadow-sm">
                <div class="module-left">
                    <div class="icon-wrapper bg-secondary bg-opacity-10 text-info"><i class="fas fa-envelope-open-text"></i></div>
                    <div>
                        <h5 class="hub-title">Communication Logs</h5>
                        <p class="hub-desc">Review support tickets and messages sent by customers through the portal.</p>
                    </div>
                </div>
                <div class="enter-link-right text-info">View Inbox <i class="fas fa-inbox ms-1"></i></div>
            </a>

            <a href="admin_analyst_reports.php" class="hub-row-card shadow-sm">
                <div class="module-left">
                    <div class="icon-wrapper bg-secondary bg-opacity-10 text-soft-purple"><i class="fas fa-microscope"></i></div>
                    <div>
                        <h5 class="hub-title">Analyst Intelligence Reports</h5>
                        <p class="hub-desc">Review detailed findings and security reports submitted by network analysts.</p>
                    </div>
                </div>
                <div class="enter-link-right text-soft-purple">Review Reports <i class="fas fa-chevron-right ms-1"></i></div>
            </a>

            <a href="admin_db_manager.php" class="hub-row-card shadow-sm">
                <div class="module-left">
                    <div class="icon-wrapper bg-secondary bg-opacity-10 text-soft-amber"><i class="fas fa-database"></i></div>
                    <div>
                        <h5 class="hub-title">Database Architect</h5>
                        <p class="hub-desc">Administrative tool for direct database table management and optimization.</p>
                    </div>
                </div>
                <div class="enter-link-right text-warning">Launch Tool <i class="fas fa-tools ms-1"></i></div>
            </a>

        </div>
    </div>

    <!-- Recent Device Readings Section -->
    <h4 class="fw-bold mt-5 mb-4" style="color: #cbd5e1;"><i class="fas fa-history me-2 text-info"></i> Recent System Readings</h4>
    <div class="card bg-dark border-secondary shadow-sm">
        <div class="table-responsive">
            <table class="table table-dark table-hover mb-0 align-middle">
                <thead>
                    <tr class="small text-muted uppercase">
                        <th class="ps-4">Device</th>
                        <th>Owner</th>
                        <th>CPU</th>
                        <th>RAM</th>
                        <th>Status</th>
                        <th>Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $readings = $pdo->query("SELECT m.*, d.device_name, u.username 
                                            FROM metrics m 
                                            JOIN devices d ON m.device_id = d.id 
                                            JOIN users u ON d.user_id = u.id 
                                            ORDER BY m.captured_at DESC LIMIT 10")->fetchAll();
                    
                    if(empty($readings)) {
                        echo '<tr><td colspan="6" class="text-center py-4 opacity-50">No recent data collected.</td></tr>';
                    } else {
                        foreach($readings as $r) {
                            $row_class = ($r['is_abnormal']) ? 'table-danger text-danger fw-bold' : '';
                            $status_text = ($r['is_abnormal']) ? 'ABNORMAL' : 'NORMAL';
                            echo "<tr class='{$row_class}'>
                                <td class='ps-4'>{$r['device_name']}</td>
                                <td>{$r['username']}</td>
                                <td>{$r['cpu_usage']}%</td>
                                <td>{$r['ram_usage']}%</td>
                                <td>{$status_text}</td>
                                <td class='small text-muted'>{$r['captured_at']}</td>
                            </tr>";
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>