<?php
include 'config/db.php';
header('Content-Type: application/json; charset=utf-8');

$uni     = $_GET['uni'] ?? 'all';
$country = $_GET['country'] ?? 'all';
$q       = trim($_GET['q'] ?? '');

$where = [];
if ($uni !== 'all')     $where[] = "university='".$conn->real_escape_string($uni)."'";
if ($country !== 'all') $where[] = "preferable_country='".$conn->real_escape_string($country)."'";
if ($q !== '')          $where[] = "(name LIKE '%".$conn->real_escape_string($q)."%' 
                                     OR email LIKE '%".$conn->real_escape_string($q)."%')";

$whereSQL = $where ? 'WHERE '.implode(' AND ', $where) : '';

$res = $conn->query("SELECT id, name, email, university, preferable_country 
                     FROM students $whereSQL 
                     ORDER BY university, name 
                     LIMIT 500");

$data = [];
if ($res) {
    while ($r = $res->fetch_assoc()) $data[] = $r;
}
echo json_encode($data);
?>