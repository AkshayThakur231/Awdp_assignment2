<?php require 'config.php'; $errors = []; $v = ['name' => '', 'email' => '', 'mobile' => '', 'address' => ''];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($v as $k => $_) $v[$k] = trim($_POST[$k] ?? '');
    $pw = $_POST['password'] ?? ''; $cp = $_POST['confirm'] ?? '';
    if (strlen($v['name']) < 2) $errors[] = 'Enter your full name.';
    if (!filter_var($v['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Enter a valid email address.';
    if (!preg_match('/^[6-9]\d{9}$/', $v['mobile'])) $errors[] = 'Mobile number must be 10 digits and start with 6–9.';
    if (strlen($pw) < 6) $errors[] = 'Password must be at least 6 characters.';
    if ($pw !== $cp) $errors[] = 'Passwords do not match.';
    if ($v['address'] === '') $errors[] = 'Enter your address.';
    if (!$errors) {
        $s = $pdo->prepare('SELECT id FROM users WHERE email=?'); $s->execute([$v['email']]);
        if ($s->fetch()) $errors[] = 'This email is already registered. Try logging in.';
        else {
            $pdo->prepare('INSERT INTO users (name,email,mobile,password,address) VALUES (?,?,?,?,?)')
                ->execute([$v['name'], $v['email'], $v['mobile'], password_hash($pw, PASSWORD_DEFAULT), $v['address']]);
            flash('Account created. Please log in.'); redirect('login.php');
        }
    }
}
$title = 'Register'; require 'includes/header.php'; ?>
<div class="panel narrow"><h1>Create an account</h1>
<?php foreach ($errors as $er) echo '<div class="flash err">' . e($er) . '</div>'; ?>
<form method="post"><?= csrf() ?>
  <label>Name</label><input name="name" value="<?= e($v['name']) ?>" required>
  <label>Email</label><input type="email" name="email" value="<?= e($v['email']) ?>" required>
  <label>Mobile</label><input name="mobile" value="<?= e($v['mobile']) ?>" maxlength="10" required>
  <label>Password</label><input type="password" name="password" required>
  <label>Confirm password</label><input type="password" name="confirm" required>
  <label>Address</label><textarea name="address" required><?= e($v['address']) ?></textarea>
  <p><button class="btn full">Register</button></p>
</form><p>Already registered? <a href="login.php"><u>Log in</u></a></p></div>
<?php require 'includes/footer.php'; ?>
