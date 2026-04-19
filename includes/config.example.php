<?php
// Copy this file to config.php and fill in your credentials.
define('DB_HOST', 'localhost');
define('DB_NAME', 'complaint_db');
define('DB_USER', 'complaint_user');
define('DB_PASS', 'CHANGE_ME');
define('APP_NAME', 'ComplaintDesk');
define('APP_URL',  '/complaint');
define('UPLOAD_DIR', __DIR__ . '/../assets/uploads/');
define('UPLOAD_URL', APP_URL . '/assets/uploads/');

try {
    $pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4',
        DB_USER, DB_PASS,
        [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
} catch(PDOException $e){ die('DB connection failed.'); }

session_start();
function isLoggedIn()  { return isset($_SESSION['user_id']); }
function isAdmin()     { return isset($_SESSION['role']) && $_SESSION['role']==='admin'; }
function redirect($u)  { header("Location: $u"); exit; }
function sanitize($v)  { return htmlspecialchars(trim((string)$v), ENT_QUOTES,'UTF-8'); }
function generateTicket() { return 'TKT-'.strtoupper(substr(md5(uniqid(rand(),true)),0,8)); }
function addNotification($pdo,$uid,$cid,$msg){
    $pdo->prepare("INSERT INTO notifications(user_id,complaint_id,message)VALUES(?,?,?)")->execute([$uid,$cid,$msg]);
}
