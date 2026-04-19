<?php
require_once '../includes/config.php';
if(!isLoggedIn()||isAdmin()) redirect(APP_URL.'/index.php');
$pageTitle = 'New Complaint';

$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
$success = $error = '';

if($_SERVER['REQUEST_METHOD']==='POST'){
    $subject     = sanitize($_POST['subject'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $category_id = (int)($_POST['category_id'] ?? 0);
    $priority    = $_POST['priority'] ?? 'medium';
    $attachment  = null;

    if(!$subject || !$description){ $error = 'Subject and description are required.'; }
    else {
        if(!empty($_FILES['attachment']['name'])){
            $ext = strtolower(pathinfo($_FILES['attachment']['name'], PATHINFO_EXTENSION));
            if(in_array($ext,['jpg','jpeg','png','pdf','doc','docx'])){
                $filename = uniqid().'.'.$ext;
                move_uploaded_file($_FILES['attachment']['tmp_name'], UPLOAD_DIR.$filename);
                $attachment = $filename;
            } else { $error = 'Invalid file type. Allowed: jpg,png,pdf,doc,docx'; }
        }
        if(!$error){
            $ticket = generateTicket();
            $pdo->prepare("INSERT INTO complaints (ticket_no,user_id,category_id,subject,description,priority,attachment) VALUES (?,?,?,?,?,?,?)")
                ->execute([$ticket,$_SESSION['user_id'],$category_id?$category_id:null,$subject,$description,$priority,$attachment]);
            $cid = $pdo->lastInsertId();
            addNotification($pdo,$_SESSION['user_id'],$cid,"Your complaint has been submitted. Ticket: $ticket");

            // Notify all admins
            $admins = $pdo->query("SELECT id FROM users WHERE role='admin'")->fetchAll();
            foreach($admins as $a) addNotification($pdo,$a['id'],$cid,"New complaint received: $ticket — $subject");

            redirect(APP_URL."/user/view_complaint.php?id=$cid&new=1");
        }
    }
}

include '../includes/header.php';
?>
<div class="topbar"><div><h2>File a Complaint</h2><p>Tell us about your issue and we'll look into it.</p></div></div>

<?php if($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>

<div class="card" style="max-width:720px">
  <form method="POST" enctype="multipart/form-data">
    <div class="form-group">
      <label class="form-label">Subject <span style="color:var(--danger)">*</span></label>
      <input type="text" name="subject" class="form-control" placeholder="Brief title of your complaint" required>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
      <div class="form-group">
        <label class="form-label">Category</label>
        <select name="category_id" class="form-control">
          <option value="">-- Select Category --</option>
          <?php foreach($categories as $cat): ?>
          <option value="<?= $cat['id'] ?>"><?= sanitize($cat['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Priority</label>
        <select name="priority" class="form-control">
          <option value="low">Low</option>
          <option value="medium" selected>Medium</option>
          <option value="high">High</option>
          <option value="critical">Critical</option>
        </select>
      </div>
    </div>
    <div class="form-group">
      <label class="form-label">Description <span style="color:var(--danger)">*</span></label>
      <textarea name="description" class="form-control" rows="6" placeholder="Describe your complaint in detail…" required></textarea>
    </div>
    <div class="form-group">
      <label class="form-label">Attachment <span style="color:var(--muted);font-weight:400">(optional – jpg, png, pdf, doc)</span></label>
      <input type="file" name="attachment" class="form-control" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
    </div>
    <div style="display:flex;gap:12px;margin-top:8px">
      <button type="submit" class="btn btn-primary">Submit Complaint</button>
      <a href="<?= APP_URL ?>/user/dashboard.php" class="btn btn-ghost">Cancel</a>
    </div>
  </form>
</div>
<?php include '../includes/footer.php'; ?>
