<?php
include 'config/db.php';
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 25;
$offset = ($page-1)*$perPage;

$total = $conn->query("SELECT COUNT(*) c FROM mail_log")->fetch_assoc()['c'] ?? 0;
$totalPages = max(1, ceil($total/$perPage));
$rows = $conn->query("SELECT * FROM mail_log ORDER BY id DESC LIMIT $perPage OFFSET $offset");
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>Mail Logs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<?php include 'includes/header.php'; ?>

<div class="container mt-4">
    <h3 class="mb-3">📜 Mail Logs <span class="badge bg-secondary"><?= number_format($total) ?></span></h3>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Email</th>
                        <th>Subject</th>
                        <th>Status</th>
                        <th>Sent At</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($rows && $rows->num_rows): while($l = $rows->fetch_assoc()): ?>
                    <tr>
                        <td><?= $l['id'] ?></td>
                        <td><?= htmlspecialchars($l['email']) ?></td>
                        <td><?= htmlspecialchars($l['subject']) ?></td>
                        <td>
                            <?php if ($l['status']==='sent'): ?>
                                <span class="badge bg-success">Sent</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Failed</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($l['sent_at']) ?></td>
                    </tr>
                <?php endwhile; else: ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            No mail logs yet
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if ($totalPages > 1): ?>
    <nav class="mt-3">
        <ul class="pagination justify-content-center">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="page-item <?= $i==$page?'active':'' ?>">
                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
            </li>
            <?php endfor; ?>
        </ul>
    </nav>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>