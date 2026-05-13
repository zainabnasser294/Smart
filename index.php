<?php 
/* SmartMonitor AI - Smart Cyber Landing Page
   تم الدمج: ستايل عصري + منطق الـ Session والروابط الخاص بك
*/
include 'includes/header.php'; 
?>

<div class="smart-main-wrapper">
    <div class="container text-center content-area">
        
        <div class="mb-4">
            <span class="system-status">
                <i class="fas fa-circle-check me-2"></i> SYSTEM STATUS: ONLINE
            </span>
        </div>

        <h1 class="smart-title">
            SmartMonitor <span class="blue-glow">AI</span>
        </h1>

        <p class="smart-description">
            Real-time monitoring powered by <strong>Network Behavior Intelligence (NBI)</strong>. 
            Secure your infrastructure with automated AI insights and professional performance metrics.
        </p>

        <div class="mt-5">
            <?php 
                // تحديد الوجهة بناءً على الجلسة الحالية
                $redirect_url = 'login.php'; 
                if(isset($_SESSION['user_id'])) {
                    $redirect_url = 'dashboard.php'; // افتراضي للعملاء
                    if ($_SESSION['role'] === 'admin') {
                        $redirect_url = 'admin_dashboard.php';
                    } elseif ($_SESSION['role'] === 'analyst') {
                        $redirect_url = 'view_metrics.php';
                    }
                }
            ?>
            <a href="<?php echo $redirect_url; ?>" class="smart-action-btn">
                <i class="fas fa-chart-line me-2"></i> 
                <?php echo isset($_SESSION['user_id']) ? 'Go to Dashboard' : 'Get Started Now'; ?>
            </a>
        </div>

        <div class="row g-4 mt-5">
            <div class="col-md-4">
                <div class="smart-feature-box">
                    <div class="feature-icon-wrap icon-blue">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <h5>NBI Protection</h5>
                </div>
            </div>
            <div class="col-md-4">
                <div class="smart-feature-box">
                    <div class="feature-icon-wrap icon-green">
                        <i class="fas fa-bolt-lightning"></i>
                    </div>
                    <h5>Live Metrics</h5>
                </div>
            </div>
            <div class="col-md-4">
                <div class="smart-feature-box">
                    <div class="feature-icon-wrap icon-yellow">
                        <i class="fas fa-robot"></i>
                    </div>
                    <h5>AI Assistant</h5>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* تصميم Smart Cyber الحديث */
    body {
        background-color: #0b1120; /* خلفية داكنة جداً */
        color: #f8fafc;
        font-family: 'Inter', sans-serif;
        margin: 0;
    }

    .smart-main-wrapper {
        min-height: 90vh;
        display: flex;
        align-items: center;
        position: relative;
        overflow: hidden;
        background: radial-gradient(circle at center, #111d35 0%, #0b1120 100%);
    }

    /* تأثير توهج الحالة */
    .system-status {
        background: rgba(56, 189, 248, 0.05);
        border: 1px solid rgba(56, 189, 248, 0.2);
        color: #38bdf8;
        padding: 8px 20px;
        border-radius: 50px;
        font-size: 0.8rem;
        font-weight: 700;
        letter-spacing: 1px;
        text-transform: uppercase;
    }

    /* العنوان */
    .smart-title {
        font-size: 4.5rem;
        font-weight: 800;
        margin-bottom: 20px;
        letter-spacing: -2px;
    }

    .blue-glow {
        color: #38bdf8;
        text-shadow: 0 0 30px rgba(56, 189, 248, 0.5);
    }

    .smart-description {
        color: #94a3b8;
        font-size: 1.2rem;
        max-width: 650px;
        margin: 0 auto;
        line-height: 1.6;
    }

    /* الزر الذكي الموحد */
    .smart-action-btn {
        display: inline-flex;
        align-items: center;
        padding: 15px 45px;
        background-color: #38bdf8;
        color: #000 !important;
        text-decoration: none !important;
        border-radius: 12px;
        font-weight: 700;
        font-size: 1.1rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 0 25px rgba(56, 189, 248, 0.4);
    }

    .smart-action-btn:hover {
        transform: translateY(-3px) scale(1.02);
        box-shadow: 0 0 40px rgba(56, 189, 248, 0.7);
        background-color: #7dd3fc;
    }

    /* بطاقات الأيقونات */
    .smart-feature-box {
        background: rgba(30, 41, 59, 0.4);
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 24px;
        padding: 40px 20px;
        transition: 0.3s;
        backdrop-filter: blur(10px);
    }

    .smart-feature-box:hover {
        border-color: #38bdf8;
        background: rgba(30, 41, 59, 0.6);
        transform: translateY(-5px);
    }

    .feature-icon-wrap {
        font-size: 2.5rem;
        width: 80px;
        height: 80px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        border-radius: 18px;
    }

    .icon-blue { background: rgba(56, 189, 248, 0.1); color: #38bdf8; }
    .icon-green { background: rgba(34, 197, 94, 0.1); color: #22c55e; }
    .icon-yellow { background: rgba(234, 179, 8, 0.1); color: #eab308; }

    .smart-feature-box h5 {
        font-weight: 600;
        font-size: 1.1rem;
        margin: 0;
        color: #f1f5f9;
    }

    /* لإخفاء أي صور متبقية قد تأتي من ملفات أخرى */
    img { max-width: 100%; height: auto; }
</style>

<?php 
// استدعاء الفوتر
include 'includes/footer.php'; 
?>