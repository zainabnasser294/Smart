<?php
include 'config/db.php';
// ملاحظة: header.php غالباً يحتوي على session_start()، لذا تأكدي من عدم تكرارها إذا ظهر تنبيه
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'includes/header.php'; 

// التحقق من الجلسة
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$current_user_id = $_SESSION['user_id'];
$current_user_role = $_SESSION['role'];

// 1. استلام المعرفات من الرابط
$device_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$target_user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

// 2. التعديل الجوهري: منطق جلب الجهاز للأدمن وللمستخدم
if ($device_id == 0) {
    if ($target_user_id > 0) {
        // إذا تم تحديد مستخدم معين (مثل اليمامة)
        $find_device = $pdo->prepare("SELECT id FROM devices WHERE user_id = ? LIMIT 1");
        $find_device->execute([$target_user_id]);
    } elseif ($current_user_role === 'admin') {
        // إذا كان أدمن وضغط على المربع العام، نجلب أول جهاز متاح في النظام كله
        $find_device = $pdo->prepare("SELECT id FROM devices LIMIT 1");
        $find_device->execute();
    } else {
        // إذا كان مستخدم عادي، نجلب جهازه الشخصي فقط
        $find_device = $pdo->prepare("SELECT id FROM devices WHERE user_id = ? LIMIT 1");
        $find_device->execute([$current_user_id]);
    }
    
    $device_row = $find_device->fetch();
    if ($device_row) {
        $device_id = $device_row['id'];
    }
}

// 3. جلب بيانات الجهاز للتأكد من وجوده وعرض اسمه
$device_stmt = $pdo->prepare("SELECT device_name FROM devices WHERE id = ?");
$device_stmt->execute([$device_id]);
$device = $device_stmt->fetch();

if (!$device) {
    echo "<div class='container mt-5'><div class='alert alert-danger text-center shadow-sm'>
            <i class='fas fa-exclamation-circle me-2'></i> لم يتم العثور على أجهزة نشطة لعرض بياناتها حالياً.
          </div></div>";
    include 'includes/footer.php';
    exit;
}
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-4 p-4 mb-4 bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="fw-bold mb-0"><i class="fas fa-microchip text-primary me-2"></i> <?php echo htmlspecialchars($device['device_name']); ?></h2>
                        <small class="text-muted">Network Behavior Intelligence (NBI) Live Analysis</small>
                    </div>
                    <span class="badge bg-success p-2 pulse-animation"><i class="fas fa-sync fa-spin me-1"></i> Live Monitoring</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4 text-white">
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-primary text-center">
                <small class="opacity-75">Download (IN)</small>
                <h3 class="fw-bold mb-0" id="currentIn">0 MB</h3>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-info text-center">
                <small class="opacity-75">Upload (OUT)</small>
                <h3 class="fw-bold mb-0" id="currentOut">0 MB</h3>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-warning text-dark text-center">
                <small class="opacity-75">Ping (Latency)</small>
                <h3 class="fw-bold mb-0" id="currentPing">0 ms</h3>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-danger text-center">
                <small class="opacity-75">Active Connections</small>
                <h3 class="fw-bold mb-0" id="currentConn">0</h3>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <h5 class="fw-bold mb-4"><i class="fas fa-network-wired text-info me-2"></i> Network Traffic: Download vs Upload</h5>
                <canvas id="networkChart" style="max-height: 300px;"></canvas>
            </div>
        </div>
        
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <h5 class="fw-bold mb-3 text-secondary">CPU Usage %</h5>
                <canvas id="cpuChart"></canvas>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <h5 class="fw-bold mb-3 text-secondary">RAM Usage %</h5>
                <canvas id="ramChart"></canvas>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
function createChart(ctxId, label, color, fill = false) {
    const ctx = document.getElementById(ctxId).getContext('2d');
    return new Chart(ctx, {
        type: 'line',
        data: { labels: [], datasets: [{ label: label, data: [], borderColor: color, backgroundColor: color + '22', fill: fill, tension: 0.4, borderWidth: 3 }] },
        options: { responsive: true, animation: false, scales: { x: { display: false }, y: { beginAtZero: true } }, plugins: { legend: { display: true } } }
    });
}

const networkChart = createChart('networkChart', 'Download (MB)', '#0d6efd', true);
const cpuChart = createChart('cpuChart', 'CPU %', '#6f42c1');
const ramChart = createChart('ramChart', 'RAM %', '#198754');

networkChart.data.datasets.push({
    label: 'Upload (MB)',
    data: [],
    borderColor: '#dc3545',
    backgroundColor: '#dc354522',
    fill: true,
    tension: 0.4,
    borderWidth: 3
});

async function updateAllMetrics() {
    try {
        const response = await fetch(`get_live_data.php?id=<?php echo $device_id; ?>`);
        const data = await response.json();

        if (data && data.length > 0) {
            const last = data[data.length - 1];
            
            document.getElementById('currentIn').innerText = (last.network_in || 0) + ' MB';
            document.getElementById('currentOut').innerText = (last.network_out || 0) + ' MB';
            document.getElementById('currentPing').innerText = (last.latency || 0) + ' ms';
            document.getElementById('currentConn').innerText = (last.connections || 0);

            const labels = data.map(r => r.captured_at);
            
            networkChart.data.labels = labels;
            networkChart.data.datasets[0].data = data.map(r => r.network_in);
            networkChart.data.datasets[1].data = data.map(r => r.network_out);
            
            cpuChart.data.labels = labels;
            cpuChart.data.datasets[0].data = data.map(r => r.cpu_usage);
            
            ramChart.data.labels = labels;
            ramChart.data.datasets[0].data = data.map(r => r.ram_usage);

            networkChart.update('none');
            cpuChart.update('none');
            ramChart.update('none');
        }
    } catch (e) { console.error("Fetch Error:", e); }
}

setInterval(updateAllMetrics, 3000);
updateAllMetrics();
</script>

<style>
.pulse-animation { animation: pulse 2s infinite; }
@keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.5; } }
.card { transition: 0.3s; }
.card:hover { transform: translateY(-3px); }
</style>

<?php include 'includes/footer.php'; ?>