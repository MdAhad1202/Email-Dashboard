<?php
include 'config/db.php';

$search  = trim($_GET['search'] ?? '');
$uni     = trim($_GET['university'] ?? '');
$country = trim($_GET['country'] ?? '');
$higher  = $_GET['higher'] ?? '';

$where = [];
if ($search !== '') $where[] = "(name LIKE '%".$conn->real_escape_string($search)."%' 
                                OR email LIKE '%".$conn->real_escape_string($search)."%' 
                                OR phone LIKE '%".$conn->real_escape_string($search)."%')";
if ($uni !== '')     $where[] = "university='".$conn->real_escape_string($uni)."'";
if ($country !== '') $where[] = "preferable_country='".$conn->real_escape_string($country)."'";
if ($higher !== '')  $where[] = "interested_higherstudy='".$conn->real_escape_string($higher)."'";

$whereSQL = $where ? 'WHERE '.implode(' AND ', $where) : '';

$res = $conn->query("SELECT * FROM students $whereSQL ORDER BY id DESC");

// CSV Headers
$filename = 'students_' . date('Ymd_His') . '.csv';
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');

$out = fopen('php://output', 'w');

// BOM for Excel (UTF-8 support for Bangla)
fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));

// Column headers
fputcsv($out, [
    'ID', 'Name', 'Email', 'Phone', 
    'University', 'Interested Higher Study', 'Preferred Country', 'Created At'
]);

// Rows
if ($res) {
    while ($r = $res->fetch_assoc()) {
        fputcsv($out, [
            $r['id'] ?? '',
            $r['name'] ?? '',
            $r['email'] ?? '',
            $r['phone'] ?? '',
            $r['university'] ?? '',
            $r['interested_higherstudy'] ?? '',
            $r['preferable_country'] ?? '',
            $r['created_at'] ?? ''
        ]);
    }
}

fclose($out);
exit;
?>