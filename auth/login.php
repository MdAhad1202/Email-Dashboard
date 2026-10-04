<?php
session_start();

// Already logged in? redirect
if (isset($_SESSION['admin_id'])) {
    header("Location: ../index.php");
    exit;
}

include '../config/db.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = $conn->real_escape_string($_POST['username'] ?? '');
    $p = $_POST['password'] ?? '';
    
    $res = $conn->query("SELECT * FROM admins WHERE username='$u'");
    if ($row = $res->fetch_assoc()) {
        if (password_verify($p, $row['password'])) {
            $_SESSION['admin_id'] = $row['id'];
            $_SESSION['admin_name'] = $row['username'];
            header("Location: ../index.php");
            exit;
        }
    }
    $error = '❌ ভুল username বা password';
}
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; }
        .login-card { box-shadow: 0 20px 60px rgba(0,0,0,0.3); border-radius: 15px; }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center">
<div class="container" style="max-width: 420px;">
    <div class="card login-card p-4">
        <div class="text-center mb-4">
            <h2>🔐</h2>
            <h4>Admin Login</h4>
            <p class="text-muted small">Student Dashboard</p>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input name="username" class="form-control form-control-lg" 
                       placeholder="admin" required autofocus>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input name="password" type="password" class="form-control form-control-lg" 
                       placeholder="••••••" required>
            </div>
            <button class="btn btn-primary btn-lg w-100">Login</button>
        </form>
    </div>
</div>
</body>
</html>