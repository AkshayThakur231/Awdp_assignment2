<?php require 'config.php'; require_login();
[$items, $total] = cart_items($pdo);
if (!$items) { flash('Your cart is empty.', 'err'); redirect('cart.php'); }
$s = $pdo->prepare('SELECT * FROM users WHERE id=?'); $s->execute([$_SESSION['uid']]); $u = $s->fetch();
$f = $_SESSION['checkout'] ?? ['name' => $u['name'], 'email' => $u['email'], 'mobile' => $u['mobile'], 'billing_address' => $u['address'],
    'shipping_address' => $u['address'], 'city' => $u['city'], 'state' => $u['state'], 'pincode' => $u['pincode']];
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($f as $k => $_) $f[$k] = trim($_POST[$k] ?? '');
    if (strlen($f['name']) < 2) $errors[] = 'Enter your name.';
    if (!filter_var($f['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Enter a valid email.';
    if (!preg_match('/^[6-9]\d{9}$/', $f['mobile'])) $errors[] = 'Mobile number must be 10 digits.';
    if ($f['billing_address'] === '' || $f['shipping_address'] === '') $errors[] = 'Enter billing and shipping addresses.';
    if ($f['city'] === '' || $f['state'] === '') $errors[] = 'Enter city and state.';
    if (!preg_match('/^\d{6}$/', $f['pincode'])) $errors[] = 'Pincode must be 6 digits.';
    if (!$errors) { $_SESSION['checkout'] = $f; redirect('payment.php'); }
}
$title = 'Checkout'; require 'includes/header.php'; ?>
<h1>Checkout</h1>
<?php foreach ($errors as $er) echo '<div class="flash err">' . e($er) . '</div>'; ?>
<div class="layout">
<form method="post" class="panel"><?= csrf() ?>
  <div class="two"><div><label>Name</label><input name="name" value="<?= e($f['name']) ?>" required></div>
  <div><label>Email</label><input type="email" name="email" value="<?= e($f['email']) ?>" required></div></div>
  <label>Mobile number</label><input name="mobile" maxlength="10" value="<?= e($f['mobile']) ?>" required>
  <label>Billing address</label><textarea name="billing_address" required><?= e($f['billing_address']) ?></textarea>
  <label>Shipping address</label><textarea name="shipping_address" required><?= e($f['shipping_address']) ?></textarea>
  <div class="two"><div><label>City</label><input name="city" value="<?= e($f['city']) ?>" required></div>
  <div><label>State</label><input name="state" value="<?= e($f['state']) ?>" required></div></div>
  <label>Pincode</label><input name="pincode" maxlength="6" value="<?= e($f['pincode']) ?>" required>
  <p><button class="btn">Continue to payment</button></p>
</form>
<div class="panel"><h3>Order summary</h3>
  <?php foreach ($items as $i): ?><p><?= e($i['name']) ?> × <?= $i['qty'] ?><br><b><?= price($i['line']) ?></b></p><?php endforeach; ?>
  <hr><p><b>Total amount: <?= price($total) ?></b></p></div>
</div>
<?php require 'includes/footer.php'; ?>
