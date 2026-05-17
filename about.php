<?php
include 'config/db.php';
include 'includes/header.php';
?>

<style>
    
    body {
        background-color: #0f172a;
        color: #f8fafc;
    }

    /* الهيدر بتدرج لوني "حي" */
    .about-header { 
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); 
        color: white; 
        padding: 60px 20px; 
        border-radius: 30px; 
        margin-bottom: 50px; 
        border: 1px solid rgba(56, 189, 248, 0.2);
        box-shadow: 0 20px 40px rgba(0,0,0,0.3);
    }

    /* صناديق الخطوات بستايل زجاجي شفاف */
    .step-box { 
        background: rgba(30, 41, 59, 0.5); 
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-left: 5px solid #38bdf8; 
        padding: 30px; 
        margin-bottom: 30px; 
        border-radius: 20px; 
        transition: all 0.4s ease;
        position: relative;
        overflow: hidden;
    }

    /* تأثير عند المرور بالماوس */
    .step-box:hover { 
        transform: translateY(-5px) scale(1.02); 
        background: rgba(30, 41, 59, 0.8); 
        border-color: rgba(56, 189, 248, 0.5);
        box-shadow: 0 10px 30px rgba(56, 189, 248, 0.1);
    }

    /* أرقام الخطوات بوهج أزرق */
    .step-number { 
        background: #38bdf8; 
        color: #0f172a; 
        width: 40px; 
        height: 40px; 
        border-radius: 12px; 
        display: flex; 
        align-items: center; 
        justify-content: center; 
        font-weight: 800; 
        margin-bottom: 15px;
        box-shadow: 0 0 15px rgba(56, 189, 248, 0.4);
    }

    /* نصوص الخطوات */
    .step-box h5 { color: #f1f5f9; letter-spacing: 0.5px; }
    .step-box p { color: #94a3b8; line-height: 1.6; }

    /* الأزرار التفاعلية */
    .btn-guide {
        background: linear-gradient(90deg, #0284c7, #38bdf8);
        color: white;
        padding: 10px 25px;
        border-radius: 12px;
        text-decoration: none;
        font-weight: 600;
        display: inline-block;
        margin-top: 15px;
        transition: 0.3s;
        border: none;
    }
    .btn-guide:hover { 
        box-shadow: 0 0 20px rgba(56, 189, 248, 0.4); 
        transform: scale(1.05);
        color: white;
    }

    /* الخط الفاصل */
    .custom-hr {
        border: 0;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(56, 189, 248, 0.5), transparent);
        margin: 50px 0;
    }
</style>

<div class="container mt-5">
    <div class="about-header text-center">
        <h1 class="display-5 fw-bold mb-3">Mastering Your <span style="color: #38bdf8;">SmartMonitor</span></h1>
        <p class="lead opacity-75">Your roadmap to a secure and intelligent network environment.</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-9">
            <h3 class="mb-5 text-center fw-bold" style="color: #cbd5e1;">
                <i class="fas fa-rocket me-2 text-info"></i> Setup Journey
            </h3>
            
            <div class="step-box">
                <div class="step-number">01</div>
                <h5 class="fw-bold">Establish Your Identity</h5>
                <p>Start by creating your administrative account. This ensures all monitoring data is securely tied to your profile.</p>
                <a href="login.php" class="btn-guide"><i class="fas fa-sign-in-alt me-2"></i> Get Started</a>
            </div>

            <div class="step-box">
                <div class="step-number">02</div>
                <h5 class="fw-bold">Link Your Infrastructure</h5>
                <p>Enter your device's <strong>IP Address</strong> in the system. This allows our NBI engine to locate and communicate with your hardware.</p>
                <a href="manage_devices.php" class="btn-guide"><i class="fas fa-plus-circle me-2"></i> Register IP</a>
            </div>

            <div class="step-box" style="border-left-color: #fbbf24;">
                <div class="step-number" style="background: #fbbf24;">03</div>
                <h5 class="fw-bold">Security Handshake</h5>
                <p>The system will generate a unique <strong>Activation Code</strong>. Think of this as the "digital fingerprint" for your device.</p>
            </div>

            <div class="step-box" style="border-left-color: #a78bfa;">
                <div class="step-number" style="background: #a78bfa;">04</div>
                <h5 class="fw-bold">Final Activation</h5>
                <p>Submit your code through the <strong>Contact Portal</strong>. Our backend will finalize the secure tunnel between the collector and the dashboard.</p>
                <a href="contact.php" class="btn-guide"><i class="fas fa-paper-plane me-2"></i> Submit Code</a>
            </div>

            <div class="step-box" style="border-left-color: #2ecc71;">
                <div class="step-number" style="background: #2ecc71;">05</div>
                <h5 class="fw-bold">Explore the Intelligence</h5>
                <p>Everything is set! Access your real-time dashboard to witness AI-driven network behavior analysis in action.</p>
            </div>

        </div>
    </div>

    <div class="custom-hr"></div>

    <div class="text-center mb-5 pb-5">
        <h4 class="mb-4" style="color: #94a3b8;">Ready to see the magic?</h4>
        <a href="dashboard.php" class="btn btn-outline-info btn-lg px-5 rounded-pill fw-bold">
            <i class="fas fa-chart-line me-2"></i> Launch Dashboard
        </a>
    </div>
</div>

<?php include 'includes/footer.php'; ?>