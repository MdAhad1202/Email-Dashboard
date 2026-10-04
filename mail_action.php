<?php
include 'config/db.php';
include 'includes/mailer.php';

$ids     = $_POST['student_ids'] ?? [];
$subject = trim($_POST['subject'] ?? '');
$message = $_POST['message'] ?? '';

if (empty($ids)) {
    die("<div style='padding:30px;font-family:sans-serif'>
         ⚠️ No recipients selected. <a href='send_mail.php'>Go back</a></div>");
}
if ($subject === '' || $message === '') {
    die("<div style='padding:30px;font-family:sans-serif'>
         ⚠️ Subject and message required. <a href='send_mail.php'>Go back</a></div>");
}

$idList = implode(',', array_map('intval', $ids));
$res = $conn->query("SELECT id, name, email, university FROM students WHERE id IN ($idList)");

if (!$res || !$res->num_rows) {
    die("<div style='padding:30px'>No valid recipients found.</div>");
}

$mail = getMailer();
$sent = 0; $failed = 0; $errors = [];

while ($s = $res->fetch_assoc()) {
    try {
        $mail->clearAddresses();
        $mail->addAddress($s['email'], $s['name']);
        
        // Replace placeholders
        $personalSubject = str_replace(
            ['{name}', '{university}'],
            [$s['name'], $s['university']],
            $subject
        );
        $personalBody = str_replace(
            ['{name}', '{university}'],
            [$s['name'], $s['university']],
            $message
        );
        
        $mail->Subject = $personalSubject;
        $mail->Body    = nl2br($personalBody);
        $mail->AltBody = strip_tags($personalBody);
        
        if ($mail->send()) {
            $sent++;
            $status = 'sent';
        } else {
            $failed++;
            $status = 'failed';
            $errors[] = $s['email'] . ': ' . $mail->ErrorInfo;
        }
    } catch (Exception $e) {
        $failed++;
        $status = 'failed';
        $errors[] = $s['email'] . ': ' . $e->getMessage();
    }
    
    // Log to DB
    $msg = $conn->real_escape_string($message);
    $sub = $conn->real_escape_string($subject);
    $em  = $conn->real_escape_string($s['email']);
    $conn->query("INSERT INTO mail_log (student_id, email, subject, message, status) 
                  VALUES ({$s['id']}, '$em', '$sub', '$msg', '$status')");
    
    // Small delay for rate limits
    usleep(200000); // 0.2 seconds
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Mail Result</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<?php include 'includes/header.php'; ?>
<div class="container mt-5">
    <div class="card p-4">
        <h3>📧 Mail Result</h3>
        
        <div class="alert alert-success mt-3">
            ✅ <b>Sent:</b> <?= $sent ?>
        </div>
        <?php if ($failed): ?>
        <div class="alert alert-danger">
            ❌ <b>Failed:</b> <?= $failed ?>
        </div>
        <?php endif; ?>
        
        <div class="mt-3">
            <a href="send_mail.php" class="btn btn-primary">Send More</a>
            <a href="mail_logs.php" class="btn btn-outline-dark">View Logs</a>
            <a href="index.php" class="btn btn-outline-secondary">Dashboard</a>
        </div>
        
        <?php if ($errors): ?>
        <details class="mt-3">
            <summary>Error details</summary>
            <ul class="mt-2">
                <?php foreach ($errors as $e): ?>
                <li><code><?= htmlspecialchars($e) ?></code></li>
                <?php endforeach; ?>
            </ul>
        </details>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
