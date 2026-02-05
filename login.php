<?php
session_start();
include 'config.php';
$error = '';
$username = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $stored_password, $role);
    if ($stmt->num_rows > 0) {
        $stmt->fetch();
        if (password_verify($password, $stored_password)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $role;
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "Username not found.";
    }
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Coffee POS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body { min-height: 100vh; display: flex; justify-content: center; align-items: center; padding: 20px; position: relative; overflow: hidden; }
        video { position: fixed; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; z-index: -1; }
        .overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 0; }
        .container { display: flex; width: 100%; max-width: 1000px; background: rgba(255,255,255,0.95); border-radius: 20px; overflow: hidden; box-shadow: 0 15px 35px rgba(0,0,0,0.2); animation: fadeIn 0.8s; position: relative; z-index: 1; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .login-form { flex: 1; padding: 50px; display: flex; flex-direction: column; justify-content: center; }
        .welcome-side { flex: 1; background: #654321; color: #fff; padding: 50px; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; }
        .logo { font-size: 2.5rem; font-weight: 700; margin-bottom: 10px; color: #fff; }
        .logo i { margin-right: 10px; }
        .tagline { font-size: 1.1rem; opacity: 0.9; margin-bottom: 40px; }
        h2 { color: #654321; margin-bottom: 30px; font-size: 2rem; position: relative; padding-bottom: 10px; }
        h2:after { content: ''; position: absolute; bottom: 0; left: 0; width: 60px; height: 4px; background: #8B4513; border-radius: 2px; }
        .form-group { margin-bottom: 25px; }
        .form-group label { display: block; margin-bottom: 8px; color: #555; font-weight: 600; }
        .input-with-icon { position: relative; }
        .input-with-icon i { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #8B4513; font-size: 1.2rem; }
        .form-control { width: 100%; padding: 15px 15px 15px 45px; border: 2px solid #e1e1e1; border-radius: 10px; font-size: 1rem; transition: all 0.3s; background: #f9f9f9; }
        .form-control:focus { outline: none; border-color: #8B4513; background: #fff; box-shadow: 0 0 0 3px rgba(139,69,19,0.1); }
        .btn-login { background: #8B4513; color: #fff; border: none; padding: 16px; border-radius: 10px; font-size: 1.1rem; font-weight: 600; cursor: pointer; transition: all 0.3s; width: 100%; margin-top: 10px; }
        .btn-login:hover { background: #654321; transform: translateY(-3px); box-shadow: 0 7px 15px rgba(139,69,19,0.3); }
        .error-message { background: #ffebee; color: #c62828; padding: 12px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #c62828; display: <?php echo $error ? 'block' : 'none'; ?>; animation: shake 0.5s; }
        @keyframes shake { 0%, 100% { transform: translateX(0); } 10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); } 20%, 40%, 60%, 80% { transform: translateX(5px); } }
        .register-link { text-align: center; margin-top: 25px; color: #666; }
        .register-link a { color: #8B4513; text-decoration: none; font-weight: 600; transition: all 0.3s; position: relative; }
        .register-link a:hover { color: #654321; }
        .register-link a:after { content: ''; position: absolute; bottom: -2px; left: 0; width: 0; height: 2px; background: #8B4513; transition: width 0.3s; }
        .register-link a:hover:after { width: 100%; }
        .features { display: flex; flex-wrap: wrap; gap: 20px; margin-top: 40px; }
        .feature { display: flex; align-items: center; margin-bottom: 15px; }
        .feature i { background: rgba(255,255,255,0.2); width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 15px; font-size: 1.2rem; }
        @media (max-width: 768px) { .container { flex-direction: column; max-width: 500px; } .welcome-side { order: -1; padding: 30px; } .login-form { padding: 30px; } .features { display: none; } }
    </style>
</head>
<body>
    <video autoplay muted loop>
        <source src="coffee-bg.mp4" type="video/mp4">
        Your browser does not support the video tag.
    </video>
    <div class="overlay"></div>
    <div class="container">
        <div class="login-form">
            <h2>Sign In to Your Account</h2>
            <?php if ($error): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>
            <form method="POST" action="" id="loginForm">
                <div class="form-group">
                    <label for="username">Username</label>
                    <div class="input-with-icon">
                        <i class="fas fa-user"></i>
                        <input type="text" id="username" name="username" class="form-control" required value="<?php echo htmlspecialchars($username); ?>" placeholder="Enter your username">
                    </div>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-with-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" name="password" class="form-control" required placeholder="Enter your password">
                    </div>
                </div>
                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt"></i> Sign In
                </button>
            </form>
            <div class="register-link">
                Don't have an account? <a href="register.php">Register here</a>
            </div>
        </div>
        <div class="welcome-side">
            <div class="logo">
                <i class="fas fa-shield-alt"></i> SecureApp
            </div>
            <p class="tagline">Secure access to your dashboard</p>
            <div class="features">
                <div class="feature">
                    <i class="fas fa-check-circle"></i>
                    <div>
                        <h4>Secure Access</h4>
                        <p>Protected user authentication</p>
                    </div>
                </div>
                <div class="feature">
                    <i class="fas fa-bolt"></i>
                    <div>
                        <h4>Fast Dashboard</h4>
                        <p>Quick access to your data</p>
                    </div>
                </div>
                <div class="feature">
                    <i class="fas fa-mobile-alt"></i>
                    <div>
                        <h4>Mobile Friendly</h4>
                        <p>Access from any device</p>
                    </div>
                </div>
                <div class="feature">
                    <i class="fas fa-headset"></i>
                    <div>
                        <h4>24/7 Support</h4>
                        <p>Always here to help</p>
                    </div>
                </div>
            </div>
            <div style="margin-top: 30px; font-size: 0.9rem; opacity: 0.8;">
                <i class="fas fa-info-circle"></i> Use your registered credentials to login
            </div>
        </div>
    </div>
    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const submitBtn = document.querySelector('.btn-login');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Signing In...';
            submitBtn.disabled = true;
            setTimeout(() => {
                if (document.querySelector('.error-message')) {
                    submitBtn.innerHTML = '<i class="fas fa-sign-in-alt"></i> Sign In';
                    submitBtn.disabled = false;
                }
            }, 1500);
        });
    </script>
</body>
</html>