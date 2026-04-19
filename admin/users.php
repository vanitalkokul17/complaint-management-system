<?php
require_once '../includes/config.php';
if(!isAdmin()) redirect(APP_URL.'/index.php');
$pageTitle = 'Manage Users';

if(isset($_POST['toggle'])){
    $uid = (int)$_POST['user_id'];
    $pdo->prepare("UPDATE users SET status=IF(status='active','inactive','active') WHERE id=? AND role='user'")->execute([$uid]);
}

$users = $pdo->query("SELECT u.*, (SELECT COUNT(*) FROM complaints WHERE user_id=u.id) as total_complaints
  FROM users u WHERE u.role='user' ORDER BY u.created_at DESC")->fetchAll();

include '../includes/header.php';
?>
<div class="topbar"><div><h2>Users</h2><p><?= count($users) ?> registered users</p></div></div>
<div class="card">
  <div class="table-wrap">
  <table>
    <thead><tr><th>#</th><th>Name</th><th>Email</th><th>Phone</th><th>Complaints</th><th>Status</th><th>Joined</th><th>Action</th></tr></thead>
    <tbody>
    <?php foreach($users as $i=>$u): ?>
    <tr>
      <td><?= $i+1 ?></td>
      <td><strong><?= sanitize($u['name']) ?></strong></td>
      <td><?= sanitize($u['email']) ?></td>
      <td><?= sanitize($u['phone'] ?? '—') ?></td>
      <td><span class="badge badge-medium"><?= $u['total_complaints'] ?></span></td>
      <td><span class="badge <?= $u['status']==='active'?'badge-resolved':'badge-rejected' ?>"><?= $u['status'] ?></span></td>
      <td><?= date('M d, Y', strtotime($u['created_at'])) ?></td>
      <td>
        <form method="POST" style="display:inline">
          <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
          <button type="submit" name="toggle" class="btn btn-sm <?= $u['status']==='active'?'btn-danger':'btn-success' ?>">
            <?= $u['status']==='active'?'Deactivate':'Activate' ?>
          </button>
        </form>
      </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  </div>
</div>
<?php include '../includes/footer.php'; ?>
