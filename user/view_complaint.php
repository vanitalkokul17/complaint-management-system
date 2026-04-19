<?php
require_once '../includes/config.php';
if(!isLoggedIn()||isAdmin()) redirect(APP_URL.'/index.php');
$pageTitle = 'Track Complaint';

$id  = (int)($_GET['id'] ?? 0);
$uid = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT c.*,cat.name as catname FROM complaints c LEFT JOIN categories cat ON c.category_id=cat.id WHERE c.id=? AND c.user_id=?");
$stmt->execute([$id,$uid]);
$c = $stmt->fetch();
if(!$c) redirect(APP_URL.'/user/my_complaints.php');

$updates = $pdo->prepare("SELECT cu.*,u.name as uname FROM complaint_updates cu LEFT JOIN users u ON cu.updated_by=u.id WHERE cu.complaint_id=? ORDER BY cu.created_at ASC");
$updates->execute([$id]); $timeline=$updates->fetchAll();

include '../includes/header.php';
?>
<div class="topbar">
  <div><h2><?= sanitize($c['subject']) ?></h2>
    <p><code style="color:var(--accent)"><?= $c['ticket_no'] ?></code></p>
  </div>
  <a href="<?= APP_URL ?>/user/my_complaints.php" class="btn btn-ghost">← Back</a>
</div>

<?php if(isset($_GET['new'])): ?>
<div class="alert alert-success">✓ Your complaint has been submitted! Ticket No: <strong><?= $c['ticket_no'] ?></strong>. We'll review it shortly.</div>
<?php endif; ?>

<div style="display:grid;grid-template-columns:1fr 320px;gap:20px">
  <div>
    <div class="card" style="margin-bottom:20px">
      <div class="card-header"><span class="card-title">Complaint Details</span>
        <span class="badge badge-<?= $c['status'] ?>"><?= str_replace('_',' ',$c['status']) ?></span></div>
      <div style="margin-bottom:14px">
        <span class="form-label">Description</span>
        <p style="line-height:1.7"><?= nl2br(sanitize($c['description'])) ?></p>
      </div>
      <?php if($c['admin_remarks']): ?>
      <div style="background:rgba(79,124,255,.08);border-radius:8px;padding:14px;margin-top:12px;border-left:3px solid var(--accent)">
        <span class="form-label">Admin Response</span>
        <p style="margin-top:4px"><?= nl2br(sanitize($c['admin_remarks'])) ?></p>
      </div>
      <?php endif; ?>
    </div>

    <div class="card">
      <div class="card-header"><span class="card-title">Status Timeline</span></div>
      <ul class="timeline">
        <li class="timeline-item">
          <div class="timeline-dot" style="background:var(--accent)"></div>
          <div class="timeline-time"><?= date('M d, Y H:i', strtotime($c['created_at'])) ?></div>
          <div class="timeline-content">Complaint submitted — awaiting review</div>
        </li>
        <?php foreach($timeline as $t): ?>
        <li class="timeline-item">
          <div class="timeline-dot"></div>
          <div class="timeline-time"><?= date('M d, Y H:i', strtotime($t['created_at'])) ?></div>
          <div class="timeline-content">
            Status changed: <strong><?= $t['old_status'] ?> → <?= $t['new_status'] ?></strong>
            <?php if($t['message']): ?><br><span style="color:var(--muted);font-size:12px"><?= sanitize($t['message']) ?></span><?php endif; ?>
          </div>
        </li>
        <?php endforeach; ?>
        <?php if($c['status']==='resolved'||$c['status']==='closed'): ?>
        <li class="timeline-item">
          <div class="timeline-dot" style="background:var(--success)"></div>
          <div class="timeline-time">Completed</div>
          <div class="timeline-content" style="color:var(--success)">✓ Complaint <?= $c['status'] ?></div>
        </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>

  <div class="card" style="height:fit-content">
    <div class="card-header"><span class="card-title">Info</span></div>
    <div style="display:flex;flex-direction:column;gap:14px">
      <div><span class="form-label">Ticket No</span><p style="font-family:monospace;color:var(--accent)"><?= $c['ticket_no'] ?></p></div>
      <div><span class="form-label">Category</span><p><?= sanitize($c['catname'] ?? 'General') ?></p></div>
      <div><span class="form-label">Priority</span><span class="badge badge-<?= $c['priority'] ?>"><?= $c['priority'] ?></span></div>
      <div><span class="form-label">Status</span><span class="badge badge-<?= $c['status'] ?>"><?= str_replace('_',' ',$c['status']) ?></span></div>
      <div><span class="form-label">Submitted</span><p><?= date('M d, Y H:i', strtotime($c['created_at'])) ?></p></div>
      <div><span class="form-label">Last Update</span><p><?= date('M d, Y H:i', strtotime($c['updated_at'])) ?></p></div>
      <?php if($c['attachment']): ?>
      <div><span class="form-label">Attachment</span><br>
        <a href="<?= UPLOAD_URL.sanitize($c['attachment']) ?>" target="_blank" class="btn btn-ghost btn-sm" style="margin-top:6px">📎 View</a></div>
      <?php endif; ?>
    </div>
  </div>
</div>
<?php include '../includes/footer.php'; ?>
