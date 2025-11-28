<?php
include 'koneksi.php';
session_start();

if (isset($_POST['login'])) {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    $stmt = mysqli_prepare($koneksi, "SELECT * FROM user WHERE username=?");
    mysqli_stmt_bind_param($stmt, "s", $user);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_assoc($result);

    if ($data) {
        if (password_verify($pass, $data['password'])) {
            $_SESSION['username'] = $data['username']; 
            $_SESSION['level'] = $data['level'];       
            header('location:dashboard.php');
            exit; 
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Username tidak ditemukan!";
    }
    mysqli_stmt_close($stmt);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Sistem Informasi Akademik</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <style>
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        
        .login-card {
            max-width: 450px;
            width: 100%;
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .login-header h2 {
            color: white;
            font-weight: 700;
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        
        .login-header p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 1rem;
        }
        
        .input-group-modern {
            position: relative;
            margin-bottom: 1.5rem;
        }
        
        .input-group-modern i {
            position: absolute;
            left: 1.25rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
            z-index: 10;
        }
        
        .input-group-modern input {
            padding-left: 3.25rem;
        }
        
        .alert-modern {
            padding: 1rem 1.25rem;
            border-radius: var(--radius-md);
            margin-bottom: 1.5rem;
            border: none;
            animation: slideIn 0.3s ease-out;
        }
        
        .alert-danger-modern {
            background: linear-gradient(135deg, #fee2e2, #fecaca);
            color: #991b1b;
        }
    </style>
</head>
<body>
    <div class="gradient-bg">
        <div class="login-container">
            <div class="login-card fade-in">
                <div class="login-header">
                    <h2><i class="fas fa-graduation-cap"></i> SIA</h2>
                    <p>Sistem Informasi Akademik</p>
                </div>
                
                <div class="glass-card">
                    <?php if (isset($error)): ?>
                        <div class="alert-modern alert-danger-modern">
                            <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" id="loginForm">
                        <div class="input-group-modern">
                            <i class="fas fa-user"></i>
                            <input type="text" name="username" class="modern-input" placeholder="Username" required autocomplete="username">
                        </div>
                        
                        <div class="input-group-modern">
                            <i class="fas fa-lock"></i>
                            <input type="password" name="password" class="modern-input" placeholder="Password" required autocomplete="current-password">
                        </div>
                        
                        <button type="submit" name="login" class="btn-modern btn-primary-modern w-100">
                            <span class="btn-text">Login</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>