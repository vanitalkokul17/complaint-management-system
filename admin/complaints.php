<?php
require_once '../includes/config.php';
if(!isAdmin()) redirect(APP_URL.'/index.php');
$pageTitle = 'All Complaints';

$status   = $_GET['status']   ?? '';
$priority = $_GET['priority'] ?? '';
$search   = $_GET['search']   ?? '';

$where = '1=1';
$params = [];
if($status)   { $where .= ' AND c.status=?';   $params[]=$status; }
if($priority) { $where .= ' AND c.priority=?'; $params[]=$priority; }
if($search)   { $where .= ' AND (c.subject LIKE ? OR c.ticket_no LIKE ? OR u.name LIKE ?)';
                $params[] = "%$search%"; $params[] = "%$search%"; $params[] = "%$search%"; }

$stmt = $pdo->prepare("SELECT c.*,u.name as uname,cat.name as catname
  FROM complaints c
  LEFT JOIN users u ON c.user_id=u.id
  LEFT JOIN categories cat ON c.category_id=cat.id
  WHERE $where ORDER BY c.created_at DESC");
$stmt->execute($params);
$complaints = $stmt->fetchAll();

include '../includes/header.php';
?>
<div class="topbar"><div><h2>Complaints</h2><p><?= count($complaints) ?> records found</p></div></div>

<div class="card" style="margin-bottom:20px">
  <form method="GET" style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end">
    <div style="flex:1;min-width:180px"><label class="form-label">Search</label>
      <input type="text" name="search" class="form-control" value="<?= sanitize($search) ?>" placeholder="Ticket, subject, user…"></div>
    <div style="min-width:140px"><label class="form-label">Status</label>
      <select name="status" class="form-control">
        <option value="">All Statuses</option>
        <?php foreach(['open','in_progress','resolved','closed','rejected'] as $s): ?>
        <option value="<?=$s?>" <?=$status===$s?'selected':''?>><?= ucfirst(str_replace('_',' ',$s)) ?></option>
        <?php endforeach; ?>
      </select></div>
    <div style="min-width:140px"><label class="form-label">Priority</label>
      <select name="priority" class="form-control">
        <option value="">All Priorities</option>
        <?php foreach(['low','medium','high','critical'] as $p): ?>
        <option value="<?=$p?>" <?=$priority===$p?'selected':''?>><?= ucfirst($p) ?></option>
        <?php endforeach; ?>
      </select></div>
    <button type="submit" class="btn btn-primary">Filter</button>
    <a href="complaints.php" class="btn btn-ghost">Reset</a>
  </form>
</div>

<div class="card">
  <div class="table-wrap">
  <table>
    <thead><tr><th>Ticket</th><th>Subject</th><th>User</th><th>Category</th><th>Priority</th><th>Status</th><th>Date</th><th>Actions</th></tr></thead>
    <tbody>
    <?php foreach($complaints as $c): ?>
    <tr>
      <td><code style="font-size:11px;color:var(--accent)"><?= $c['ticket_no'] ?></code></td>
      <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?= sanitize($c['subject']) ?></td>
      <td><?= sanitize($c['uname']) ?></td>
      <td><?= sanitize($c['catname'] ?? '—') ?></td>
      <td><span class="badge badge-<?= $c['priority'] ?>"><?= $c['priority'] ?></span></td>
      <td><span class="badge badge-<?= $c['status'] ?>"><?= str_replace('_',' ',$c['status']) ?></span></td>
      <td><?= date('M d, Y', strtotime($c['created_at'])) ?></td>
      <td style="display:flex;gap:6px">
        <a href="<?= APP_URL ?>/admin/view_complaint.php?id=<?= $c['id'] ?>" class="btn btn-primary btn-sm">Manage</a>
      </td>
    </tr>
    <?php endforeach; ?>
    <?php if(!$complaints): ?>
    <tr><td colspan="8" style="text-align:center;padding:40px;color:var(--muted)">No complaints found.</td></tr>
    <?php endif; ?>
    </tbody>
  </table>
  </div>
</div>
<?php include '../includes/footer.php'; ?>
