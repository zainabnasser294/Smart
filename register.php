<?php 
include 'config/db.php'; 
$message = "";

if (isset($_POST['register'])) {
    $user = $_POST['username'];
    $email = $_POST['email'];
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT); 

    try {
        $sql = "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'user')";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute([$user, $email, $pass])) {
            header("Location: login.php");
            exit();
        }
    } catch (PDOException $e) {
        $message = "<div style='color: #ff7675; margin-top: 15px; text-align: center;'>Error: User already exists.</div>";
    }
}
include 'includes/header.php'; 
?>

<div style="display: flex; justify-content: center; align-items: center; min-height: 85vh; background: #0b111e; font-family: 'Segoe UI', sans-serif;">
    
    <div style="width: 100%; max-width: 400px; text-align: center; background: transparent;">
        
        <div style="margin-bottom: 30px;">
            <h2 style="color: #ffffff; font-size: 28px; font-weight: 700; margin: 0;">Account Setup</h2>
            <p style="color: #64748b; font-size: 14px; margin-top: 8px;">Please enter your administrative credentials</p>
        </div>

        <form action="register.php" method="POST" style="background: transparent; border: none; padding: 0;">
            
            <div style="margin-bottom: 20px; text-align: left;">
                <label style="color: #94a3b8; font-size: 13px; margin-left: 5px; display: block; margin-bottom: 8px;">Username</label>
                <input type="text" name="username" placeholder="Enter username" style="width:100%; padding: 14px; background: #1a2234; border: 1px solid #2d3748; border-radius: 10px; color: white; outline: none; box-sizing: border-box;" onfocus="this.style.borderColor='#3498db';" required>
            </div>

            <div style="margin-bottom: 20px; text-align: left;">
                <label style="color: #94a3b8; font-size: 13px; margin-left: 5px; display: block; margin-bottom: 8px;">Email Address</label>
                <input type="email" name="email" placeholder="admin@network.ai" style="width:100%; padding: 14px; background: #1a2234; border: 1px solid #2d3748; border-radius: 10px; color: white; outline: none; box-sizing: border-box;" onfocus="this.style.borderColor='#3498db';" required>
            </div>

            <div style="margin-bottom: 30px; text-align: left;">
                <label style="color: #94a3b8; font-size: 13px; margin-left: 5px; display: block; margin-bottom: 8px;">Password</label>
                <input type="password" name="password" placeholder="••••••••" style="width:100%; padding: 14px; background: #1a2234; border: 1px solid #2d3748; border-radius: 10px; color: white; outline: none; box-sizing: border-box;" onfocus="this.style.borderColor='#3498db';" required>
            </div>

            <button type="submit" name="register" style="width:100%; padding: 15px; background: #3498db; color: white; border: none; border-radius: 10px; font-size: 16px; font-weight: 700; cursor: pointer; transition: 0.3s;">
                Complete Registration
            </button>
        </form>

        <?php echo $message; ?>

        <div style="margin-top: 25px;">
            <p style="color: #64748b; font-size: 14px;">Already have an account? <a href="login.php" style="color: #3498db; text-decoration: none; font-weight: 600;">Sign In</a></p>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>