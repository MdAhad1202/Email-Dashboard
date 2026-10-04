<?php include 'config/db.php'; ?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>Send Mail</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<?php include 'includes/header.php'; ?>

<div class="container mt-4">
    <h3 class="mb-3">✉️ Send Mail to Students</h3>

    <form action="mail_action.php" method="POST" id="mailForm">
        
        <!-- Filters -->
        <div class="card p-3 mb-3">
            <h6 class="text-muted mb-3">🔎 Filter Recipients</h6>
            <div class="row g-2">
                <div class="col-md-4">
                    <label class="form-label small">University</label>
                    <select id="uniSelect" class="form-select" onchange="loadStudents()">
                        <option value="all">🌐 All Universities</option>
                        <?php
                        $unis = $conn->query("SELECT DISTINCT university FROM students WHERE university IS NOT NULL AND university != '' ORDER BY university");
                        while($u = $unis->fetch_assoc()):
                        ?>
                        <option value="<?= htmlspecialchars($u['university']) ?>">
                            <?= htmlspecialchars($u['university']) ?>
                        </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small">Preferred Country</label>
                    <select id="countrySelect" class="form-select" onchange="loadStudents()">
                        <option value="all">🌍 All Countries</option>
                        <?php
                        $cs = $conn->query("SELECT DISTINCT preferable_country FROM students WHERE preferable_country IS NOT NULL AND preferable_country != '' ORDER BY preferable_country");
                        while($c = $cs->fetch_assoc()):
                        ?>
                        <option value="<?= htmlspecialchars($c['preferable_country']) ?>">
                            <?= htmlspecialchars($c['preferable_country']) ?>
                        </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small">Search</label>
                    <input id="searchBox" class="form-control" placeholder="Name / Email" onkeyup="loadStudents()">
                </div>
            </div>
        </div>

        <!-- Students list -->
        <div class="card p-3 mb-3">
            <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                <b>Recipients</b>
                <div>
                    <button type="button" class="btn btn-sm btn-success" onclick="selectAll(true)">✅ Select All</button>
                    <button type="button" class="btn btn-sm btn-secondary" onclick="selectAll(false)">❌ Deselect</button>
                    <span class="badge bg-info ms-2">Selected: <span id="selCount">0</span></span>
                </div>
            </div>
            <div id="studentList" style="max-height:400px; overflow-y:auto; border:1px solid #dee2e6; padding:12px; border-radius:6px;">
                <p class="text-muted mb-0">Loading...</p>
            </div>
        </div>

        <!-- Subject & Message -->
        <div class="card p-3 mb-3">
            <label class="form-label"><b>Subject</b></label>
            <input type="text" name="subject" class="form-control mb-3" placeholder="Mail subject" required>

            <label class="form-label"><b>Message</b></label>
            <textarea name="message" rows="8" class="form-control" 
                      placeholder="Type your message here..." required></textarea>
            <small class="text-muted mt-1">
                💡 Tip: HTML allowed. Use <code>{name}</code>, <code>{university}</code> for dynamic content.
            </small>
        </div>

        <button type="submit" class="btn btn-primary btn-lg mb-4">
            🚀 Send Mail
        </button>
    </form>
</div>

<script>
function loadStudents() {
    const uni = document.getElementById('uniSelect').value;
    const country = document.getElementById('countrySelect').value;
    const q = document.getElementById('searchBox').value;
    
    fetch(`get_students.php?uni=${encodeURIComponent(uni)}&country=${encodeURIComponent(country)}&q=${encodeURIComponent(q)}`)
        .then(r => r.json())
        .then(data => {
            const box = document.getElementById('studentList');
            if (!data.length) {
                box.innerHTML = '<p class="text-muted mb-0">No students found</p>';
                updateCount(); return;
            }
            box.innerHTML = data.map(s => `
                <div class="form-check py-1 border-bottom">
                    <input class="form-check-input student-cb" type="checkbox"
                           name="student_ids[]" value="${s.id}" id="s${s.id}" onchange="updateCount()">
                    <label class="form-check-label w-100" for="s${s.id}">
                        <b>${escapeHtml(s.name)}</b> — ${escapeHtml(s.email)}
                        <small class="text-muted">(${escapeHtml(s.university)} • ${escapeHtml(s.preferable_country)})</small>
                    </label>
                </div>
            `).join('');
            updateCount();
        });
}
function selectAll(state) {
    document.querySelectorAll('.student-cb').forEach(cb => cb.checked = state);
    updateCount();
}
function updateCount() {
    document.getElementById('selCount').innerText = 
        document.querySelectorAll('.student-cb:checked').length;
}
function escapeHtml(t) {
    if (!t) return '';
    return String(t).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
}
window.onload = loadStudents;
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>