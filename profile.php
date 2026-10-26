<?php require 'config.php'; require_login(); $errors = [];
$s = $pdo->prepare('SELECT * FROM users WHERE id=?'); $s->execute([$_SESSION['uid']]); $u = $s->fetch();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach (['name', 'email', 'mobile', 'address', 'city', 'state', 'pincode'] as $k) $u[$k] = trim($_POST[$k] ?? '');
    if (strlen($u['name']) < 2) $errors[] = 'Enter your name.';
    if (!filter_var($u['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Enter a valid email.';
    if (!preg_match('/^[6-9]\d{9}$/', $u['mobile'])) $errors[] = 'Mobile number must be 10 digits.';
    if ($u['pincode'] !== '' && !preg_match('/^\d{6}$/', $u['pincode'])) $errors[] = 'Pincode must be 6 digits.';
    $d = $pdo->prepare('SELECT id FROM users WHERE email=? AND id<>?'); $d->execute([$u['email'], $_SESSION['uid']]);
    if ($d->fetch()) $errors[] = 'That email belongs to another account.';
    if (!$errors) {
        $pdo->prepare('UPDATE users SET name=?,email=?,mobile=?,address=?,city=?,state=?,pincode=? WHERE id=?')
            ->execute([$u['name'], $u['email'], $u['mobile'], $u['address'], $u['city'], $u['state'], $u['pincode'], $_SESSION['uid']]);
        $_SESSION['uname'] = $u['name']; flash('Profile updated.'); redirect('profile.php');
    }
}
$title = 'My profile'; require 'includes/header.php'; ?>
<div class="panel narrow"><h1>My profile</h1>
<?php foreach ($errors as $er) echo '<div class="flash err">' . e($er) . '</div>'; ?>
<form method="post"><?= csrf() ?>
  <label>Name</label><input name="name" value="<?= e($u['name']) ?>" required>
  <label>Email</label><input type="email" name="email" value="<?= e($u['email']) ?>" required>
  <label>Mobile number</label><input name="mobile" maxlength="10" value="<?= e($u['mobile']) ?>" required>
  <label>Address</label><textarea name="address"><?= e($u['address']) ?></textarea>
  <div class="two"><div><label>City</label><input name="city" value="<?= e($u['city']) ?>"></div><div><label>State</label><input name="state" value="<?= e($u['state']) ?>"></div></div>
  <label>Pincode</label><input name="pincode" maxlength="6" value="<?= e($u['pincode']) ?>">
  <p><button class="btn">Save changes</button></p>
</form></div>
<?php require 'includes/footer.php'; ?>
