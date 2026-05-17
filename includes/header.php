<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Dashboard for Network Monitoring using Chatbot and Network Behavior Intelligence (NBI)</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">

    <?php
    $unread_count = 0;
    if (isset($_SESSION['user_id'])) {
        include_once 'config/db.php';
        $stmt = $pdo->query("SELECT COUNT(*) FROM nbi_alerts WHERE is_read = 0");
        $unread_count = $stmt->fetchColumn();
    }
    ?>

    <style>
        :root {
            --nav-bg: rgba(11, 17, 32, 0.95);
            --accent-blue: #38bdf8;
            --accent-green: #22c55e;
            --danger-red: #ef4444;
            --text-gray: #94a3b8;
        }

        body { 
            background-color: #0b1120; 
            margin: 0; 
            font-family: 'Inter', sans-serif; 
            color: #f8fafc;
        }

        /* Navbar Glassmorphism Style */
        .navbar { 
            background-color: var(--nav-bg) !important; 
            padding: 15px 0;
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: 0 4px 20px rgba(0,0,0,0.4);
        }

        .navbar-brand { 
            font-weight: 800; 
            color: #fff !important; 
            font-size: 1.1rem; 
            letter-spacing: -0.5px;
            white-space: normal;
            max-width: 300px;
        }

        .navbar-brand span { color: var(--accent-blue); }

        .nav-link { 
            color: var(--text-gray) !important; 
            font-size: 0.9rem; 
            font-weight: 500;
            transition: 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .nav-link:hover, .nav-link.active { 
            color: var(--accent-blue) !important; 
        }

        .notification-bell {
            position: relative;
            margin-right: 15px;
            font-size: 1.2rem;
            color: var(--text-gray);
            transition: 0.3s;
        }
        .notification-bell:hover { color: var(--accent-blue); }
        .badge-notify {
            position: absolute;
            top: -5px;
            right: -10px;
            background-color: var(--danger-red);
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 0.65rem;
            font-weight: 700;
            border: 2px solid var(--nav-bg);
        }

        /* Admin Badge - Neon Style */
        .admin-badge {
            background: rgba(34, 197, 94, 0.1) !important; 
            color: var(--accent-green) !important;
            padding: 8px 18px !important;
            border-radius: 12px;
            font-weight: 600;
            border: 1px solid rgba(34, 197, 94, 0.3) !important;
            box-shadow: 0 0 15px rgba(34, 197, 94, 0.1);
        }
        .admin-badge:hover { 
            background: rgba(34, 197, 94, 0.2) !important; 
            box-shadow: 0 0 20px rgba(34, 197, 94, 0.2);
        }

        /* User Pill */
        .user-pill {
            background: rgba(255, 255, 255, 0.03);
            color: #fff;
            padding: 6px 16px;
            border-radius: 50px;
            font-size: 0.85rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Logout Button */
        .logout-btn {
            color: var(--danger-red) !important;
            border: 1px solid rgba(239, 68, 68, 0.2) !important;
            border-radius: 10px;
            padding: 6px 15px !important;
            transition: 0.3s;
        }
        .logout-btn:hover { 
            background: rgba(239, 68, 68, 0.1) !important;
            border-color: var(--danger-red) !important;
        }

        .content-area { padding-top: 50px; min-height: 85vh; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark sticky-top">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <i class="fas fa-shield-halved text-blue-glow"></i> Smart Dashboard for Network Monitoring <span>(NBI)</span>
        </a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center gap-2">
                
                <li class="nav-item">
                    <a class="nav-link" href="index.php"><i class="fas fa-house-chimney"></i> Home</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="about.php"><i class="fas fa-circle-info"></i> About</a>
                </li>

                <?php if(isset($_SESSION['user_id'])): ?>
                    
                    <li class="nav-item">
                        <a class="nav-link" href="view_master.php">
                            <i class="fas fa-eye"></i> View Master
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link notification-bell" href="admin_alerts.php">
                            <i class="fas fa-bell"></i>
                            <?php if($unread_count > 0): ?>
                                <span class="badge-notify"><?php echo $unread_count; ?></span>
                            <?php endif; ?>
                        </a>
                    </li>

                    <?php if($_SESSION['role'] === 'admin'): ?>
                        <li class="nav-item ms-lg-2">
                            <a class="nav-link admin-badge" href="admin_dashboard.php">
                                <i class="fas fa-user-shield"></i> Admin
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if($_SESSION['role'] === 'analyst'): ?>
                        <li class="nav-item">
                            <a class="nav-link text-info active" href="analyst_dashboard.php">
                                <i class="fas fa-microscope"></i> Analyst Hub
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if($_SESSION['role'] === 'user'): ?>
                        <li class="nav-item">
                            <a class="nav-link text-blue-glow active" href="dashboard.php">
                                <i class="fas fa-chart-line"></i> Dashboard
                            </a>
                        </li>
                    <?php endif; ?>

                    <li class="nav-item ms-lg-3">
                        <span class="user-pill">
                            <i class="fas fa-circle-user text-primary"></i> 
                            <?php echo htmlspecialchars($_SESSION['username']); ?>
                        </span>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link logout-btn" href="logout.php">
                            <i class="fas fa-power-off"></i>
                        </a>
                    </li>

                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link admin-badge" href="login.php" style="border-color: var(--accent-blue) !important; color: var(--accent-blue) !important; background: transparent !important;">
                            <i class="fas fa-right-to-bracket"></i> Login
                        </a>
                    </li>
                <?php endif; ?>

            </ul>
        </div>
    </div>
</nav>

<div class="container content-area">