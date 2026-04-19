<?php require_once 'includes/config.php';
if (isLoggedIn()) redirect(isAdmin() ? APP_URL.'/admin/dashboard.php' : APP_URL.'/user/dashboard.php');

$error = $success = '';
$mode = $_GET['mode'] ?? 'login';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($mode === 'login') {
        $email    = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email=? AND status='active'");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name']    = $user['name'];
            $_SESSION['role']    = $user['role'];
            redirect($user['role']==='admin' ? APP_URL.'/admin/dashboard.php' : APP_URL.'/user/dashboard.php');
        } else {
            $error = 'Invalid email or password.';
        }
    } elseif ($mode === 'register') {
        $name    = sanitize($_POST['name'] ?? '');
        $email   = sanitize($_POST['email'] ?? '');
        $phone   = sanitize($_POST['phone'] ?? '');
        $pass    = $_POST['password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';
        if ($pass !== $confirm) {
            $error = 'Passwords do not match.';
        } else {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email=?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $error = 'Email already registered.';
            } else {
                $hashed = password_hash($pass, PASSWORD_BCRYPT);
                $pdo->prepare("INSERT INTO users (name,email,password,phone) VALUES (?,?,?,?)")
                    ->execute([$name,$email,$hashed,$phone]);
                $success = 'Registration successful! Please log in.';
                $mode = 'login';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>ComplaintDesk – <?= $mode==='login'?'Sign In':'Register' ?></title>
<link rel="stylesheet" href="<?= APP_URL ?>/assets/css/style.css">
</head>
<body>
<div class="auth-wrapper">
  <div class="auth-card">
    <div class="auth-logo">
      <h1>ComplaintDesk</h1>
      <p>Complaint Management System</p>
    </div>

    <?php if($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
    <?php if($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>

    <?php if($mode==='login'): ?>
    <form method="POST">
      <div class="form-group">
        <label class="form-label">Email Address</label>
        <input type="email" name="email" class="form-control" placeholder="you@example.com" required>
      </div>
      <div class="form-group">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" placeholder="••••••••" required>
      </div>
      <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;margin-top:8px;">Sign In</button>
    </form>
    <p style="text-align:center;margin-top:18px;font-size:13px;color:var(--muted)">
      No account? <a href="?mode=register" style="color:var(--accent)">Register here</a>
    </p>
    <?php else: ?>
    <form method="POST">
      <div class="form-group">
        <label class="form-label">Full Name</label>
        <input type="text" name="name" class="form-control" placeholder="John Doe" required>
      </div>
      <div class="form-group">
        <label class="form-label">Email Address</label>
        <input type="email" name="email" class="form-control" placeholder="you@example.com" required>
      </div>
      <div class="form-group">
        <label class="form-label">Phone (optional)</label>
        <input type="text" name="phone" class="form-control" placeholder="+1 234 567 8900">
      </div>
      <div class="form-group">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" placeholder="Min 6 characters" required>
      </div>
      <div class="form-group">
        <label class="form-label">Confirm Password</label>
        <input type="password" name="confirm_password" class="form-control" placeholder="Repeat password" required>
      </div>
      <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;margin-top:8px;">Create Account</button>
    </form>
    <p style="text-align:center;margin-top:18px;font-size:13px;color:var(--muted)">
      Already have an account? <a href="?mode=login" style="color:var(--accent)">Sign in</a>
    </p>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
