<?php
include 'config/db.php';

$search  = trim($_GET['search'] ?? '');
$uni     = trim($_GET['university'] ?? '');
$country = trim($_GET['country'] ?? '');
$higher  = $_GET['higher'] ?? '';
$page    = max(1, intval($_GET['page'] ?? 1));
$perPage = 20;
$offset  = ($page - 1) * $perPage;

$where = [];
if ($search !== '') $where[] = "(name LIKE '%".$conn->real_escape_string($search)."%' 
                                OR email LIKE '%".$conn->real_escape_string($search)."%' 
                                OR phone LIKE '%".$conn->real_escape_string($search)."%')";
if ($uni !== '')     $where[] = "university='".$conn->real_escape_string($uni)."'";
if ($country !== '') $where[] = "preferable_country='".$conn->real_escape_string($country)."'";
if ($higher !== '')  $where[] = "interested_higherstudy='".$conn->real_escape_string($higher)."'";

$whereSQL = $where ? 'WHERE '.implode(' AND ', $where) : '';

$total = $conn->query("SELECT COUNT(*) c FROM students $whereSQL")->fetch_assoc()['c'];
$totalPages = max(1, ceil($total / $perPage));

$rows = $conn->query("SELECT * FROM students $whereSQL ORDER BY id DESC LIMIT $perPage OFFSET $offset");

$unis = $conn->query("SELECT DISTINCT university FROM students WHERE university IS NOT NULL AND university != '' ORDER BY university");
$countries = $conn->query("SELECT DISTINCT preferable_country FROM students WHERE preferable_country IS NOT NULL AND preferable_country != '' ORDER BY preferable_country");
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>All Students</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<?php include 'includes/header.php'; ?>

<div class="container-fluid mt-4 px-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>👥 All Students <span class="badge bg-secondary"><?= number_format($total) ?></span></h3>
        <a href="export_csv.php?<?= http_build_query($_GET) ?>" class="btn btn-success">
            ⬇️ Export CSV
        </a>
    </div>

    <form class="card p-3 mb-3">
        <div class="row g-2">
            <div class="col-md-4">
                <input name="search" class="form-control" 
                       placeholder="🔍 Search name / email / phone"
                       value="<?= htmlspecialchars($search) ?>">
            </div>
            <div class="col-md-2">
                <select name="university" class="form-select">
                    <option value="">All Universities</option>
                    <?php if ($unis): while($u = $unis->fetch_assoc()): ?>
                    <option value="<?= htmlspecialchars($u['university']) ?>" 
                        <?= $u['university']===$uni?'selected':'' ?>>
                        <?= htmlspecialchars($u['university']) ?>
                    </option>
                    <?php endwhile; endif; ?>
                </select>
            </div>
            <div class="col-md-2">
                <select name="country" class="form-select">
                    <option value="">All Countries</option>
                    <?php if ($countries): while($c = $countries->fetch_assoc()): ?>
                    <option value="<?= htmlspecialchars($c['preferable_country']) ?>"
                        <?= $c['preferable_country']===$country?'selected':'' ?>>
                        <?= htmlspecialchars($c['preferable_country']) ?>
                    </option>
                    <?php endwhile; endif; ?>
                </select>
            </div>
            <div class="col-md-2">
                <select name="higher" class="form-select">
                    <option value="">Higher Study?</option>
                    <option value="Yes" <?= $higher==='Yes'?'selected':'' ?>>Yes</option>
                    <option value="No"  <?= $higher==='No'?'selected':'' ?>>No</option>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary w-100">🔍 Filter</button>
            </div>
        </div>
    </form>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>#</th><th>Name</th><th>Email</th><th>Phone</th>
                        <th>University</th><th>Higher Study</th><th>Country</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($rows && $rows->num_rows): $i = $offset + 1; while($s = $rows->fetch_assoc()): ?>
                    <tr>
                        <td><?= $i++ ?></td>
                        <td><b><?= htmlspecialchars($s['name'] ?? '') ?></b></td>
                        <td><?= htmlspecialchars($s['email'] ?? '') ?></td>
                        <td><?= htmlspecialchars($s['phone'] ?? '') ?></td>
                        <td><?= htmlspecialchars($s['university'] ?? '') ?></td>
                        <td>
                            <?php if (strtolower($s['interested_higherstudy'] ?? '') === 'yes' || $s['interested_higherstudy'] == '1'): ?>
                                <span class="badge bg-success">Yes</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">No</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($s['preferable_country'] ?? '') ?></td>
                    </tr>
                <?php endwhile; else: ?>
                    <tr><td colspan="7" class="text-center py-4 text-muted">No students found</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if ($totalPages > 1): ?>
    <nav class="mt-3">
        <ul class="pagination justify-content-center">
            <?php
            $qs = $_GET;
            $start = max(1, $page - 3);
            $end = min($totalPages, $page + 3);
            
            if ($start > 1):
                $qs['page'] = 1;
            ?>
            <li class="page-item"><a class="page-link" href="?<?= http_build_query($qs) ?>">« First</a></li>
            <?php endif; ?>
            
            <?php for ($i = $start; $i <= $end; $i++): 
                $qs['page'] = $i;
            ?>
            <li class="page-item <?= $i==$page?'active':'' ?>">
                <a class="page-link" href="?<?= http_build_query($qs) ?>"><?= $i ?></a>
            </li>
            <?php endfor; ?>
            
            <?php if ($end < $totalPages):
                $qs['page'] = $totalPages;
            ?>
            <li class="page-item"><a class="page-link" href="?<?= http_build_query($qs) ?>">Last »</a></li>
            <?php endif; ?>
        </ul>
    </nav>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>