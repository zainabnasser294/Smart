<?php 
session_start(); 
include 'config/db.php'; 

$error = "";

if (isset($_POST['login'])) {
    $user = trim($_POST['username']);
    $pass = $_POST['password'];

   
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$user]);
    $userData = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($userData) {
      
        if (password_verify($pass, $userData['password']) || $pass === $userData['password']) {
            
            $_SESSION['user_id'] = $userData['id'];
            $_SESSION['username'] = $userData['username'];
            $_SESSION['role'] = $userData['role']; 

            if ($userData['role'] === 'admin') {
                header("Location: admin_dashboard.php");
            } elseif ($userData['role'] === 'analyst') {
                header("Location: view_metrics.php"); 
            } else {
                header("Location: dashboard.php");
            }
            exit();
        } else {
            $error = "Invalid Password.";
        }
    } else {
        $error = "User not found.";
    }
}

include 'includes/header.php'; 
?>

<div style="display: flex; justify-content: center; align-items: center; min-height: 85vh; background: #0b111e; font-family: 'Segoe UI', sans-serif; padding: 20px;">
    
    <div style="width: 100%; max-width: 400px; text-align: center; background: transparent;">
        
        <div style="background: rgba(52, 152, 219, 0.1); width: 75px; height: 75px; border-radius: 50%; display: flex; justify-content: center; align-items: center; margin: 0 auto 20px; border: 1px solid rgba(52, 152, 219, 0.2);">
            <i class="fas fa-user-shield" style="font-size: 30px; color: #3498db;"></i>
        </div>

        <div style="margin-bottom: 35px;">
            <h2 style="color: #ffffff; font-size: 28px; font-weight: 700; margin: 0;">Welcome Back</h2>
            <p style="color: #64748b; font-size: 14px; margin-top: 8px;">Access your network monitoring tools</p>
        </div>

        <form action="login.php" method="POST" autocomplete="off" style="background: transparent; border: none; padding: 0;">
            
            <div style="margin-bottom: 20px; text-align: left;">
                <label style="color: #94a3b8; font-size: 13px; margin-left: 5px; display: block; margin-bottom: 8px;">Username</label>
                <input type="text" name="username" placeholder="Enter your username" style="width:100%; padding: 14px; background: #1a2234; border: 1px solid #2d3748; border-radius: 10px; color: white; outline: none; box-sizing: border-box; transition: 0.3s;" onfocus="this.style.borderColor='#3498db';" required>
            </div>

            <div style="margin-bottom: 30px; text-align: left;">
                <label style="color: #94a3b8; font-size: 13px; margin-left: 5px; display: block; margin-bottom: 8px;">Password</label>
                <input type="password" name="password" placeholder="••••••••" style="width:100%; padding: 14px; background: #1a2234; border: 1px solid #2d3748; border-radius: 10px; color: white; outline: none; box-sizing: border-box; transition: 0.3s;" onfocus="this.style.borderColor='#3498db';" required>
            </div>

            <button type="submit" name="login" style="width:100%; padding: 15px; background: #3498db; color: white; border: none; border-radius: 10px; font-size: 16px; font-weight: 700; cursor: pointer; transition: 0.3s; box-shadow: 0 8px 15px rgba(52, 152, 219, 0.2);">
                Sign In to Dashboard
            </button>
        </form>

        <?php if ($error): ?>
            <div style="color: #ff7675; margin-top: 20px; font-size: 14px; font-weight: 600; background: rgba(255, 118, 117, 0.1); padding: 10px; border-radius: 8px; border: 1px solid rgba(255, 118, 117, 0.2);">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <div style="margin-top: 35px; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 20px;">
            <p style="color: #64748b; font-size: 14px;">New to the system? <a href="register.php" style="color: #3498db; text-decoration: none; font-weight: 600;">Create Account</a></p>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>