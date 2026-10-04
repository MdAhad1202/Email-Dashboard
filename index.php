<?php
include 'config/db.php';

// Stats
$totalStudents = $conn->query("SELECT COUNT(*) c FROM students")->fetch_assoc()['c'] ?? 0;
$totalUni      = $conn->query("SELECT COUNT(DISTINCT university) c FROM students")->fetch_assoc()['c'] ?? 0;
$totalMails    = $conn->query("SELECT COUNT(*) c FROM mail_log WHERE status='sent'")->fetch_assoc()['c'] ?? 0;
$higherStudy   = $conn->query("SELECT COUNT(*) c FROM students WHERE interested_higherstudy IN ('Yes','yes','1','Interested')")->fetch_assoc()['c'] ?? 0;

// Top universities
$unis = $conn->query("SELECT university, COUNT(*) c FROM students 
                      WHERE university IS NOT NULL AND university != '' 
                      GROUP BY university ORDER BY c DESC LIMIT 8");

// Top countries
$countries = $conn->query("SELECT preferable_country, COUNT(*) c FROM students 
                           WHERE preferable_country IS NOT NULL AND preferable_country != '' 
                           GROUP BY preferable_country ORDER BY c DESC LIMIT 8");
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<?php include 'includes/header.php'; ?>

<div class="container mt-4">
    <h3 class="mb-4">📊 Overview</h3>
    
    <div class="row g-3">
        <div class="col-md-3 col-sm-6">
            <div class="card text-white bg-primary p-3 h-100">
                <small class="opacity-75">Total Students</small>
                <h2 class="mb-0"><?= number_format($totalStudents) ?></h2>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card text-white bg-success p-3 h-100">
                <small class="opacity-75">Universities</small>
                <h2 class="mb-0"><?= number_format($totalUni) ?></h2>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card text-white bg-info p-3 h-100">
                <small class="opacity-75">Higher Study Interested</small>
                <h2 class="mb-0"><?= number_format($higherStudy) ?></h2>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card text-white bg-warning p-3 h-100">
                <small class="opacity-75">Mails Sent</small>
                <h2 class="mb-0"><?= number_format($totalMails) ?></h2>
            </div>
        </div>
    </div>

    <div class="row mt-4 g-3">
        <div class="col-md-6">
            <div class="card p-3 h-100">
                <h5>🏛️ Top Universities</h5>
                <ul class="list-group list-group-flush">
                    <?php if ($unis && $unis->num_rows): while($u = $unis->fetch_assoc()): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <?= htmlspecialchars($u['university']) ?>
                        <span class="badge bg-primary rounded-pill"><?= $u['c'] ?></span>
                    </li>
                    <?php endwhile; else: ?>
                    <li class="list-group-item text-muted">কোনো data নেই</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card p-3 h-100">
                <h5>🌍 Top Preferred Countries</h5>
                <ul class="list-group list-group-flush">
                    <?php if ($countries && $countries->num_rows): while($c = $countries->fetch_assoc()): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <?= htmlspecialchars($c['preferable_country']) ?>
                        <span class="badge bg-success rounded-pill"><?= $c['c'] ?></span>
                    </li>
                    <?php endwhile; else: ?>
                    <li class="list-group-item text-muted">কোনো data নেই</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>