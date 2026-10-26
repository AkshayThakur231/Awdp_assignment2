<?php require 'config.php'; $error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $s = $pdo->prepare('SELECT * FROM users WHERE email=?'); $s->execute([trim($_POST['email'] ?? '')]); $u = $s->fetch();
    if (!$u || !password_verify($_POST['password'] ?? '', $u['password'])) $error = 'Incorrect email or password.';
    elseif (!$u['status']) $error = 'Your account is deactivated. Contact support.';
    else {
        session_regenerate_id(true);
        $_SESSION['uid'] = $u['id']; $_SESSION['uname'] = $u['name'];
        $go = $_SESSION['redirect'] ?? ''; unset($_SESSION['redirect']);
        redirect(preg_match('/^[a-z_]+\.php(\?[\w=&]*)?$/', $go) ? $go : 'index.php');
    }
}
$title = 'Login'; require 'includes/header.php'; ?>
<div class="panel narrow"><h1>Log in</h1>
<?php if ($error) echo '<div class="flash err">' . e($error) . '</div>'; ?>
<form method="post"><?= csrf() ?>
  <label>Email</label><input type="email" name="email" required>
  <label>Password</label><input type="password" name="password" required>
  <p><button class="btn full">Log in</button></p>
</form><p>New here? <a href="register.php"><u>Create an account</u></a></p></div>
<?php require 'includes/footer.php'; ?>
