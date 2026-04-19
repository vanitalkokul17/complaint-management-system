<?php
require_once '../includes/config.php';
if(!isLoggedIn()||isAdmin()) redirect(APP_URL.'/index.php');
$pageTitle = 'My Complaints';

$uid    = $_SESSION['user_id'];
$status = $_GET['status'] ?? '';
$where  = 'c.user_id=?';
$params = [$uid];
if($status){ $where .= ' AND c.status=?'; $params[]=$status; }

$stmt = $pdo->prepare("SELECT c.*,cat.name as catname FROM complaints c LEFT JOIN categories cat ON c.category_id=cat.id WHERE $where ORDER BY c.created_at DESC");
$stmt->execute($params);
$complaints = $stmt->fetchAll();

include '../includes/header.php';
?>
<div class="topbar">
  <div><h2>My Complaints</h2><p><?= count($complaints) ?> complaints</p></div>
  <a href="<?= APP_URL ?>/user/new_complaint.php" class="btn btn-primary">+ New</a>
</div>

<div class="card" style="margin-bottom:20px;padding:16px">
  <form method="GET" style="display:flex;gap:8px;flex-wrap:wrap">
    <?php foreach(['','open','in_progress','resolved','closed','rejected'] as $s): ?>
    <a href="?status=<?=$s?>" class="btn btn-sm <?= $status===$s?'btn-primary':'btn-ghost' ?>"><?= $s?ucfirst(str_replace('_',' ',$s)):'All' ?></a>
    <?php endforeach; ?>
  </form>
</div>

<div class="card">
  <div class="table-wrap">
  <table>
    <thead><tr><th>Ticket</th><th>Subject</th><th>Category</th><th>Priority</th><th>Status</th><th>Date</th><th></th></tr></thead>
    <tbody>
    <?php foreach($complaints as $c): ?>
    <tr>
      <td><code style="font-size:11px;color:var(--accent)"><?= $c['ticket_no'] ?></code></td>
      <td style="max-width:220px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?= sanitize($c['subject']) ?></td>
      <td><?= sanitize($c['catname'] ?? '—') ?></td>
      <td><span class="badge badge-<?= $c['priority'] ?>"><?= $c['priority'] ?></span></td>
      <td><span class="badge badge-<?= $c['status'] ?>"><?= str_replace('_',' ',$c['status']) ?></span></td>
      <td><?= date('M d, Y', strtotime($c['created_at'])) ?></td>
      <td><a href="<?= APP_URL ?>/user/view_complaint.php?id=<?= $c['id'] ?>" class="btn btn-ghost btn-sm">Track</a></td>
    </tr>
    <?php endforeach; ?>
    <?php if(!$complaints): ?><tr><td colspan="7" style="text-align:center;padding:40px;color:var(--muted)">No complaints found.</td></tr><?php endif; ?>
    </tbody>
  </table>
  </div>
</div>
<?php include '../includes/footer.php'; ?>
