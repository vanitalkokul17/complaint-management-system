<?php
require_once '../includes/config.php';
if(!isLoggedIn()||isAdmin()) redirect(APP_URL.'/index.php');
$pageTitle = 'My Dashboard';
$uid = $_SESSION['user_id'];

$total    = $pdo->prepare("SELECT COUNT(*) FROM complaints WHERE user_id=?"); $total->execute([$uid]);    $total=$total->fetchColumn();
$open     = $pdo->prepare("SELECT COUNT(*) FROM complaints WHERE user_id=? AND status='open'"); $open->execute([$uid]); $open=$open->fetchColumn();
$resolved = $pdo->prepare("SELECT COUNT(*) FROM complaints WHERE user_id=? AND status='resolved'"); $resolved->execute([$uid]); $resolved=$resolved->fetchColumn();
$pending  = $pdo->prepare("SELECT COUNT(*) FROM complaints WHERE user_id=? AND status='in_progress'"); $pending->execute([$uid]); $pending=$pending->fetchColumn();

$recent = $pdo->prepare("SELECT c.*,cat.name as catname FROM complaints c LEFT JOIN categories cat ON c.category_id=cat.id WHERE c.user_id=? ORDER BY c.created_at DESC LIMIT 5");
$recent->execute([$uid]); $recent=$recent->fetchAll();

$notifs = $pdo->prepare("SELECT * FROM notifications WHERE user_id=? AND is_read=0 ORDER BY created_at DESC LIMIT 5");
$notifs->execute([$uid]); $notifs=$notifs->fetchAll();
if($notifs) $pdo->prepare("UPDATE notifications SET is_read=1 WHERE user_id=?")->execute([$uid]);

include '../includes/header.php';
?>
<div class="topbar">
  <div><h2>My Dashboard</h2><p>Welcome, <?= sanitize($_SESSION['name']) ?></p></div>
  <a href="<?= APP_URL ?>/user/new_complaint.php" class="btn btn-primary">+ New Complaint</a>
</div>

<?php foreach($notifs as $n): ?>
<div class="alert alert-info">🔔 <?= sanitize($n['message']) ?></div>
<?php endforeach; ?>

<div class="stats-grid">
  <div class="stat-card"><div class="stat-value"><?= $total ?></div><div class="stat-label">Total Submitted</div></div>
  <div class="stat-card warning"><div class="stat-value"><?= $open ?></div><div class="stat-label">Open</div></div>
  <div class="stat-card purple"><div class="stat-value"><?= $pending ?></div><div class="stat-label">In Progress</div></div>
  <div class="stat-card success"><div class="stat-value"><?= $resolved ?></div><div class="stat-label">Resolved</div></div>
</div>

<div class="card">
  <div class="card-header"><span class="card-title">Recent Complaints</span>
    <a href="<?= APP_URL ?>/user/my_complaints.php" class="btn btn-ghost btn-sm">View All</a></div>
  <div class="table-wrap">
  <table>
    <thead><tr><th>Ticket</th><th>Subject</th><th>Category</th><th>Priority</th><th>Status</th><th>Date</th><th></th></tr></thead>
    <tbody>
    <?php foreach($recent as $c): ?>
    <tr>
      <td><code style="font-size:11px;color:var(--accent)"><?= $c['ticket_no'] ?></code></td>
      <td><?= sanitize($c['subject']) ?></td>
      <td><?= sanitize($c['catname'] ?? '—') ?></td>
      <td><span class="badge badge-<?= $c['priority'] ?>"><?= $c['priority'] ?></span></td>
      <td><span class="badge badge-<?= $c['status'] ?>"><?= str_replace('_',' ',$c['status']) ?></span></td>
      <td><?= date('M d, Y', strtotime($c['created_at'])) ?></td>
      <td><a href="<?= APP_URL ?>/user/view_complaint.php?id=<?= $c['id'] ?>" class="btn btn-ghost btn-sm">Track</a></td>
    </tr>
    <?php endforeach; ?>
    <?php if(!$recent): ?>
    <tr><td colspan="7" style="text-align:center;padding:40px;color:var(--muted)">No complaints yet. <a href="<?= APP_URL ?>/user/new_complaint.php" style="color:var(--accent)">File one now</a>.</td></tr>
    <?php endif; ?>
    </tbody>
  </table>
  </div>
</div>
<?php include '../includes/footer.php'; ?>
