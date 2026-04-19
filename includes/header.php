<?php
if(!isLoggedIn()) redirect(APP_URL.'/index.php');
$unread = 0;
try {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id=? AND is_read=0");
    $stmt->execute([$_SESSION['user_id']]);
    $unread = $stmt->fetchColumn();
} catch(Exception $e){}

$isAdmin = isAdmin();
$base    = APP_URL;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= $pageTitle ?? 'ComplaintDesk' ?> – ComplaintDesk</title>
<link rel="stylesheet" href="<?= $base ?>/assets/css/style.css">
</head>
<body>
<div class="wrapper">
<nav class="sidebar">
  <div class="sidebar-logo">
    <h1>ComplaintDesk</h1>
    <p><?= $isAdmin ? 'Admin Panel' : 'User Portal' ?></p>
  </div>
  <div class="sidebar-nav">
    <?php if($isAdmin): ?>
    <div class="nav-section">Overview</div>
    <a class="nav-item <?= strpos($_SERVER['PHP_SELF'],'dashboard')!==false?'active':'' ?>" href="<?= $base ?>/admin/dashboard.php">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg> Dashboard
    </a>
    <div class="nav-section">Complaints</div>
    <a class="nav-item <?= strpos($_SERVER['PHP_SELF'],'complaints')!==false?'active':'' ?>" href="<?= $base ?>/admin/complaints.php">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="2"/></svg> All Complaints <?php if($unread>0):?><span class="nav-badge"><?=$unread?></span><?php endif?>
    </a>
    <div class="nav-section">Management</div>
    <a class="nav-item <?= strpos($_SERVER['PHP_SELF'],'users')!==false?'active':'' ?>" href="<?= $base ?>/admin/users.php">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg> Users
    </a>
    <?php else: ?>
    <div class="nav-section">Overview</div>
    <a class="nav-item <?= strpos($_SERVER['PHP_SELF'],'dashboard')!==false?'active':'' ?>" href="<?= $base ?>/user/dashboard.php">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg> Dashboard
    </a>
    <div class="nav-section">My Complaints</div>
    <a class="nav-item <?= strpos($_SERVER['PHP_SELF'],'my_complaints')!==false?'active':'' ?>" href="<?= $base ?>/user/my_complaints.php">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="2"/></svg> My Complaints
    </a>
    <a class="nav-item <?= strpos($_SERVER['PHP_SELF'],'new_complaint')!==false?'active':'' ?>" href="<?= $base ?>/user/new_complaint.php">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> New Complaint
    </a>
    <?php endif; ?>
    <div class="nav-section">Account</div>
    <a class="nav-item" href="<?= $base ?>/logout.php">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg> Sign Out
    </a>
  </div>
  <div class="sidebar-user">
    <div class="user-info">
      <div class="avatar"><?= strtoupper(substr($_SESSION['name'],0,1)) ?></div>
      <div><div class="user-name"><?= sanitize($_SESSION['name']) ?></div>
      <div class="user-role"><?= ucfirst($_SESSION['role']) ?></div></div>
    </div>
  </div>
</nav>
<main class="main-content">
