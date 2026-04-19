<?php
require_once '../includes/config.php';
if(!isAdmin()) redirect(APP_URL.'/index.php');
$pageTitle = 'Admin Dashboard';

$total    = $pdo->query("SELECT COUNT(*) FROM complaints")->fetchColumn();
$open     = $pdo->query("SELECT COUNT(*) FROM complaints WHERE status='open'")->fetchColumn();
$progress = $pdo->query("SELECT COUNT(*) FROM complaints WHERE status='in_progress'")->fetchColumn();
$resolved = $pdo->query("SELECT COUNT(*) FROM complaints WHERE status='resolved'")->fetchColumn();
$users    = $pdo->query("SELECT COUNT(*) FROM users WHERE role='user'")->fetchColumn();
$critical = $pdo->query("SELECT COUNT(*) FROM complaints WHERE priority='critical' AND status NOT IN ('resolved','closed')")->fetchColumn();

$recent = $pdo->query("SELECT c.*,u.name as uname, cat.name as catname
  FROM complaints c
  LEFT JOIN users u ON c.user_id=u.id
  LEFT JOIN categories cat ON c.category_id=cat.id
  ORDER BY c.created_at DESC LIMIT 8")->fetchAll();

include '../includes/header.php';
?>
<div class="topbar">
  <div><h2>Dashboard</h2><p>Welcome back, <?= sanitize($_SESSION['name']) ?></p></div>
  <a href="<?= APP_URL ?>/admin/complaints.php" class="btn btn-primary">
    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="2"/></svg>
    Manage Complaints
  </a>
</div>

<div class="stats-grid">
  <div class="stat-card"><div class="stat-value"><?= $total ?></div><div class="stat-label">Total Complaints</div>
    <div class="stat-icon"><svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/></svg></div></div>
  <div class="stat-card warning"><div class="stat-value"><?= $open ?></div><div class="stat-label">Open</div></div>
  <div class="stat-card purple"><div class="stat-value"><?= $progress ?></div><div class="stat-label">In Progress</div></div>
  <div class="stat-card success"><div class="stat-value"><?= $resolved ?></div><div class="stat-label">Resolved</div></div>
  <div class="stat-card danger"><div class="stat-value"><?= $critical ?></div><div class="stat-label">Critical</div></div>
  <div class="stat-card"><div class="stat-value"><?= $users ?></div><div class="stat-label">Registered Users</div></div>
</div>

<div class="card">
  <div class="card-header">
    <span class="card-title">Recent Complaints</span>
    <a href="<?= APP_URL ?>/admin/complaints.php" class="btn btn-ghost btn-sm">View All</a>
  </div>
  <div class="table-wrap">
  <table>
    <thead><tr><th>Ticket</th><th>Subject</th><th>User</th><th>Category</th><th>Priority</th><th>Status</th><th>Date</th><th>Action</th></tr></thead>
    <tbody>
    <?php foreach($recent as $c): ?>
    <tr>
      <td><code style="font-size:11px;color:var(--accent)"><?= $c['ticket_no'] ?></code></td>
      <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?= sanitize($c['subject']) ?></td>
      <td><?= sanitize($c['uname']) ?></td>
      <td><?= sanitize($c['catname'] ?? '—') ?></td>
      <td><span class="badge badge-<?= $c['priority'] ?>"><?= $c['priority'] ?></span></td>
      <td><span class="badge badge-<?= $c['status'] ?>"><?= str_replace('_',' ',$c['status']) ?></span></td>
      <td><?= date('M d, Y', strtotime($c['created_at'])) ?></td>
      <td><a href="<?= APP_URL ?>/admin/view_complaint.php?id=<?= $c['id'] ?>" class="btn btn-ghost btn-sm">View</a></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  </div>
</div>
<?php include '../includes/footer.php'; ?>
