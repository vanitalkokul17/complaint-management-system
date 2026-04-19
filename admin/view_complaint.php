<?php
require_once '../includes/config.php';
if(!isAdmin()) redirect(APP_URL.'/index.php');

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT c.*,u.name as uname,u.email as uemail,u.phone as uphne,cat.name as catname
  FROM complaints c LEFT JOIN users u ON c.user_id=u.id LEFT JOIN categories cat ON c.category_id=cat.id
  WHERE c.id=?");
$stmt->execute([$id]);
$c = $stmt->fetch();
if(!$c) redirect(APP_URL.'/admin/complaints.php');

$success = $error = '';

if($_SERVER['REQUEST_METHOD']==='POST'){
    $newStatus  = $_POST['status']       ?? $c['status'];
    $remarks    = sanitize($_POST['admin_remarks'] ?? '');
    $priority   = $_POST['priority']     ?? $c['priority'];

    $oldStatus = $c['status'];
    $pdo->prepare("UPDATE complaints SET status=?,admin_remarks=?,priority=?,updated_at=NOW() WHERE id=?")
        ->execute([$newStatus,$remarks,$priority,$id]);

    $pdo->prepare("INSERT INTO complaint_updates (complaint_id,updated_by,old_status,new_status,message) VALUES (?,?,?,?,?)")
        ->execute([$id,$_SESSION['user_id'],$oldStatus,$newStatus,'Status updated by admin: '.$remarks]);

    addNotification($pdo,$c['user_id'],$id,"Your complaint #{$c['ticket_no']} status changed to ".str_replace('_',' ',$newStatus).".");
    $success='Complaint updated successfully.';

    $stmt->execute([$id]);
    $c = $stmt->fetch();
}

$updates = $pdo->prepare("SELECT cu.*,u.name as uname FROM complaint_updates cu
  LEFT JOIN users u ON cu.updated_by=u.id WHERE cu.complaint_id=? ORDER BY cu.created_at ASC");
$updates->execute([$id]);
$timeline = $updates->fetchAll();

$pageTitle = 'View Complaint';
include '../includes/header.php';
?>
<div class="topbar">
  <div><h2><?= sanitize($c['subject']) ?></h2>
    <p><code style="color:var(--accent)"><?= $c['ticket_no'] ?></code> &nbsp;•&nbsp; Submitted by <?= sanitize($c['uname']) ?></p>
  </div>
  <a href="<?= APP_URL ?>/admin/complaints.php" class="btn btn-ghost">← Back</a>
</div>

<?php if($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>

<div style="display:grid;grid-template-columns:1fr 360px;gap:20px">
  <div>
    <div class="card" style="margin-bottom:20px">
      <div class="card-header"><span class="card-title">Complaint Details</span>
        <span class="badge badge-<?= $c['status'] ?>"><?= str_replace('_',' ',$c['status']) ?></span></div>
      <div style="margin-bottom:14px">
        <span class="form-label">Description</span>
        <p style="color:var(--text);line-height:1.7"><?= nl2br(sanitize($c['description'])) ?></p>
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
        <div><span class="form-label">Category</span><p><?= sanitize($c['catname'] ?? 'Uncategorized') ?></p></div>
        <div><span class="form-label">Priority</span><span class="badge badge-<?= $c['priority'] ?>"><?= $c['priority'] ?></span></div>
        <div><span class="form-label">Submitted</span><p><?= date('M d, Y H:i', strtotime($c['created_at'])) ?></p></div>
        <div><span class="form-label">Last Update</span><p><?= date('M d, Y H:i', strtotime($c['updated_at'])) ?></p></div>
      </div>
      <?php if($c['attachment']): ?>
      <div style="margin-top:14px">
        <span class="form-label">Attachment</span><br>
        <a href="<?= UPLOAD_URL.sanitize($c['attachment']) ?>" target="_blank" class="btn btn-ghost btn-sm" style="margin-top:6px">📎 View Attachment</a>
      </div>
      <?php endif; ?>
    </div>

    <div class="card">
      <div class="card-header"><span class="card-title">Activity Timeline</span></div>
      <ul class="timeline">
        <li class="timeline-item">
          <div class="timeline-dot" style="background:var(--success)"></div>
          <div class="timeline-time"><?= date('M d, Y H:i', strtotime($c['created_at'])) ?></div>
          <div class="timeline-content">Complaint submitted by <?= sanitize($c['uname']) ?></div>
        </li>
        <?php foreach($timeline as $t): ?>
        <li class="timeline-item">
          <div class="timeline-dot"></div>
          <div class="timeline-time"><?= date('M d, Y H:i', strtotime($t['created_at'])) ?> &nbsp;•&nbsp; <?= sanitize($t['uname']) ?></div>
          <div class="timeline-content">
            <strong><?= sanitize($t['old_status']) ?> → <?= sanitize($t['new_status']) ?></strong><br>
            <?= sanitize($t['message']) ?>
          </div>
        </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>

  <div>
    <div class="card" style="margin-bottom:20px">
      <div class="card-header"><span class="card-title">Complainant</span></div>
      <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px">
        <div class="avatar" style="width:46px;height:46px;border-radius:50%;background:linear-gradient(135deg,var(--accent),var(--accent2));display:flex;align-items:center;justify-content:center;font-weight:700;font-size:18px">
          <?= strtoupper(substr($c['uname'],0,1)) ?></div>
        <div><div style="font-weight:600"><?= sanitize($c['uname']) ?></div>
          <div style="font-size:12px;color:var(--muted)"><?= sanitize($c['uemail']) ?></div>
          <?php if($c['uphne']): ?><div style="font-size:12px;color:var(--muted)"><?= sanitize($c['uphne']) ?></div><?php endif; ?>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-header"><span class="card-title">Update Status</span></div>
      <form method="POST">
        <div class="form-group">
          <label class="form-label">Status</label>
          <select name="status" class="form-control">
            <?php foreach(['open','in_progress','resolved','closed','rejected'] as $s): ?>
            <option value="<?=$s?>" <?=$c['status']===$s?'selected':''?>><?= ucfirst(str_replace('_',' ',$s)) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Priority</label>
          <select name="priority" class="form-control">
            <?php foreach(['low','medium','high','critical'] as $p): ?>
            <option value="<?=$p?>" <?=$c['priority']===$p?'selected':''?>><?= ucfirst($p) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Admin Remarks</label>
          <textarea name="admin_remarks" class="form-control"><?= sanitize($c['admin_remarks'] ?? '') ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center">Update Complaint</button>
      </form>
    </div>
  </div>
</div>
<?php include '../includes/footer.php'; ?>
