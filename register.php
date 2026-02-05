<?php
session_start();
include 'config.php';

$error = '';
$success = '';
$username = '';
$email = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        $check = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $check->bind_param("ss", $username, $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $error = "Username or email already exists.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $hashed_password);
            if ($stmt->execute()) {
                $success = "Registration successful! You can now <a href='login.php' style='color: #fff; text-decoration: underline;'>login here</a>";
                $username = '';
                $email = '';
            } else {
                $error = "Error: " . $stmt->error;
            }
            $stmt->close();
        }
        $check->close();
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Coffee POS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body { min-height: 100vh; display: flex; justify-content: center; align-items: center; padding: 20px; position: relative; overflow: hidden; }
        video { position: fixed; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; z-index: -1; }
        .overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 0; }
        .container { display: flex; width: 100%; max-width: 1000px; background: rgba(255,255,255,0.95); border-radius: 20px; overflow: hidden; box-shadow: 0 15px 35px rgba(0,0,0,0.2); animation: fadeIn 0.8s; position: relative; z-index: 1; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .register-form { flex: 1; padding: 50px; display: flex; flex-direction: column; justify-content: center; }
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
        .password-strength { margin-top: 8px; height: 6px; background: #eee; border-radius: 3px; overflow: hidden; }
        .strength-bar { height: 100%; width: 0; background: #ff4757; border-radius: 3px; transition: all 0.3s; }
        .password-requirements { margin-top: 10px; font-size: 0.85rem; color: #666; }
        .requirement { margin-bottom: 5px; display: flex; align-items: center; }
        .requirement i { margin-right: 8px; font-size: 0.9rem; }
        .requirement.valid { color: #2ed573; }
        .btn-register { background: #8B4513; color: #fff; border: none; padding: 16px; border-radius: 10px; font-size: 1.1rem; font-weight: 600; cursor: pointer; transition: all 0.3s; width: 100%; margin-top: 10px; }
        .btn-register:hover { background: #654321; transform: translateY(-3px); box-shadow: 0 7px 15px rgba(139,69,19,0.3); }
        .error-message { background: #ffebee; color: #c62828; padding: 12px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #c62828; display: <?php echo $error ? 'flex' : 'none'; ?>; align-items: center; animation: shake 0.5s; }
        .success-message { background: #e8f5e9; color: #2e7d32; padding: 12px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #2e7d32; display: <?php echo $success ? 'flex' : 'none'; ?>; align-items: center; animation: slideIn 0.5s; }
        @keyframes shake { 0%, 100% { transform: translateX(0); } 10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); } 20%, 40%, 60%, 80% { transform: translateX(5px); } }
        @keyframes slideIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
        .error-message i, .success-message i { margin-right: 10px; font-size: 1.2rem; }
        .login-link { text-align: center; margin-top: 25px; color: #666; }
        .login-link a { color: #8B4513; text-decoration: none; font-weight: 600; transition: all 0.3s; position: relative; }
        .login-link a:hover { color: #654321; }
        .login-link a:after { content: ''; position: absolute; bottom: -2px; left: 0; width: 0; height: 2px; background: #8B4513; transition: width 0.3s; }
        .login-link a:hover:after { width: 100%; }
        .features { display: flex; flex-wrap: wrap; gap: 20px; margin-top: 40px; }
        .feature { display: flex; align-items: center; margin-bottom: 15px; }
        .feature i { background: rgba(255,255,255,0.2); width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 15px; font-size: 1.2rem; }
        @media (max-width: 768px) { .container { flex-direction: column; max-width: 500px; } .welcome-side { order: -1; padding: 30px; } .register-form { padding: 30px; } .features { display: none; } }
    </style>
</head>
<body>
    <video autoplay muted loop>
        <source src="coffee-bg.mp4.mp4" type="video/mp4">
        Your browser does not support the video tag.
    </video>
    <div class="overlay"></div>
    <div class="container">
        <div class="register-form">
            <h2>Create Your Account</h2>
            <?php if ($error): ?>
                <div class="error-message"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="success-message"><i class="fas fa-check-circle"></i> <?php echo $success; ?></div>
            <?php endif; ?>
            <form method="POST" action="" id="registerForm">
                <div class="form-group">
                    <label for="username">Username</label>
                    <div class="input-with-icon"><i class="fas fa-user"></i>
                        <input type="text" id="username" name="username" class="form-control" required value="<?php echo htmlspecialchars($username); ?>" placeholder="Choose a username" minlength="3" maxlength="20">
                    </div>
                </div>
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <div class="input-with-icon"><i class="fas fa-envelope"></i>
                        <input type="email" id="email" name="email" class="form-control" required value="<?php echo htmlspecialchars($email); ?>" placeholder="Enter your email">
                    </div>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-with-icon"><i class="fas fa-lock"></i>
                        <input type="password" id="password" name="password" class="form-control" required placeholder="Create a password" minlength="6">
                    </div>
                    <div class="password-strength"><div class="strength-bar" id="strengthBar"></div></div>
                    <div class="password-requirements">
                        <div class="requirement" id="reqLength"><i class="far fa-circle"></i> At least 6 characters</div>
                        <div class="requirement" id="reqUpper"><i class="far fa-circle"></i> Contains uppercase letter</div>
                        <div class="requirement" id="reqNumber"><i class="far fa-circle"></i> Contains number</div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <div class="input-with-icon"><i class="fas fa-lock"></i>
                        <input type="password" id="confirm_password" name="confirm_password" class="form-control" required placeholder="Confirm your password">
                    </div>
                    <div id="passwordMatch" style="margin-top: 5px; font-size: 0.9rem;"></div>
                </div>
                <button type="submit" class="btn-register"><i class="fas fa-user-plus"></i> Create Account</button>
            </form>
            <div class="login-link">Already have an account? <a href="login.php">Login here</a></div>
        </div>
        <div class="welcome-side">
            <div class="logo"><i class="fas fa-coffee"></i> Coffee POS</div>
            <p class="tagline">Join our coffee community</p>
            <div class="features">
                <div class="feature"><i class="fas fa-user-shield"></i><div><h4>Secure Account</h4><p>Your data is protected</p></div></div>
                <div class="feature"><i class="fas fa-rocket"></i><div><h4>Quick Setup</h4><p>Get started in minutes</p></div></div>
                <div class="feature"><i class="fas fa-chart-line"></i><div><h4>Track Progress</h4><p>Monitor your activity</p></div></div>
                <div class="feature"><i class="fas fa-users"></i><div><h4>Join Community</h4><p>Connect with others</p></div></div>
            </div>
            <div style="margin-top: 30px; font-size: 0.9rem; opacity: 0.8;"><i class="fas fa-info-circle"></i> Registration takes less than a minute</div>
        </div>
    </div>
    <script>
        const passwordInput = document.getElementById('password');
        const strengthBar = document.getElementById('strengthBar');
        const confirmPasswordInput = document.getElementById('confirm_password');
        const passwordMatch = document.getElementById('passwordMatch');
        const requirements = { length: document.getElementById('reqLength'), upper: document.getElementById('reqUpper'), number: document.getElementById('reqNumber') };
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;
            if (password.length >= 6) { strength += 33; requirements.length.classList.add('valid'); requirements.length.innerHTML = '<i class="fas fa-check-circle"></i> At least 6 characters'; } else { requirements.length.classList.remove('valid'); requirements.length.innerHTML = '<i class="far fa-circle"></i> At least 6 characters'; }
            if (/[A-Z]/.test(password)) { strength += 33; requirements.upper.classList.add('valid'); requirements.upper.innerHTML = '<i class="fas fa-check-circle"></i> Contains uppercase letter'; } else { requirements.upper.classList.remove('valid'); requirements.upper.innerHTML = '<i class="far fa-circle"></i> Contains uppercase letter'; }
            if (/[0-9]/.test(password)) { strength += 34; requirements.number.classList.add('valid'); requirements.number.innerHTML = '<i class="fas fa-check-circle"></i> Contains number'; } else { requirements.number.classList.remove('valid'); requirements.number.innerHTML = '<i class="far fa-circle"></i> Contains number'; }
            strengthBar.style.width = strength + '%';
            if (strength < 33) strengthBar.style.background = '#ff4757'; else if (strength < 66) strengthBar.style.background = '#ffa502'; else strengthBar.style.background = '#2ed573';
        });
        confirmPasswordInput.addEventListener('input', function() {
            const password = passwordInput.value;
            const confirmPassword = this.value;
            if (confirmPassword === '') passwordMatch.textContent = ''; else if (password === confirmPassword) { passwordMatch.innerHTML = '<i class="fas fa-check-circle"></i> Passwords match'; passwordMatch.style.color = '#2ed573'; } else { passwordMatch.innerHTML = '<i class="fas fa-times-circle"></i> Passwords do not match'; passwordMatch.style.color = '#ff4757'; }
        });
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const password = passwordInput.value;
            const confirmPassword = confirmPasswordInput.value;
            if (password !== confirmPassword) { e.preventDefault(); passwordMatch.innerHTML = '<i class="fas fa-exclamation-circle"></i> Please fix password mismatch before submitting'; passwordMatch.style.color = '#ff4757'; confirmPasswordInput.focus(); return false; }
            const submitBtn = document.querySelector('.btn-register'); submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating Account...'; submitBtn.disabled = true;
        });
    </script>
</body>
</html>