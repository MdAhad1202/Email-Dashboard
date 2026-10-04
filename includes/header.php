<?php
if (!isset($_SESSION['admin_id'])) return;
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4 shadow-sm">
    <a class="navbar-brand fw-bold" href="<?= $basePath ?? '' ?>index.php">
        📊 Student Dashboard
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navMenu">
        <div class="ms-auto d-flex gap-2 flex-wrap">
            <a href="<?= $basePath ?? '' ?>index.php" class="btn btn-sm btn-outline-light">🏠 Home</a>
            <a href="<?= $basePath ?? '' ?>students.php" class="btn btn-sm btn-outline-light">👥 Students</a>
            <a href="<?= $basePath ?? '' ?>send_mail.php" class="btn btn-sm btn-outline-light">✉️ Send Mail</a>
            <a href="<?= $basePath ?? '' ?>mail_logs.php" class="btn btn-sm btn-outline-light">📜 Mail Logs</a>
            <a href="<?= $basePath ?? '' ?>auth/logout.php" class="btn btn-sm btn-danger">
                Logout (<?= htmlspecialchars($_SESSION['admin_name']) ?>)
            </a>
        </div>
    </div>
</nav>