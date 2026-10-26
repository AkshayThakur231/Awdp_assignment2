<?php require 'config.php'; require_login();
[$items, $total] = cart_items($pdo); $f = $_SESSION['checkout'] ?? null;
if (!$items || !$f) redirect('checkout.php');
$methods = ['Cash on Delivery', 'UPI', 'Credit/Debit Card']; $errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $m = $_POST['method'] ?? '';
    if (!in_array($m, $methods)) $errors[] = 'Choose a payment method.';
    if ($m === 'UPI' && !preg_match('/^[\w.\-]{2,}@[a-z]{2,}$/i', trim($_POST['upi'] ?? ''))) $errors[] = 'Enter a valid UPI ID, like name@bank.';
    if ($m === 'Credit/Debit Card') {   // Demo only: card details are validated, never stored.
        if (!preg_match('/^\d{16}$/', str_replace(' ', '', $_POST['card'] ?? ''))) $errors[] = 'Card number must be 16 digits.';
        if (!preg_match('/^(0[1-9]|1[0-2])\/\d{2}$/', $_POST['exp'] ?? '')) $errors[] = 'Expiry must be MM/YY.';
        if (!preg_match('/^\d{3}$/', $_POST['cvv'] ?? '')) $errors[] = 'CVV must be 3 digits.';
    }
    if (!$errors) {
        try {
            $pdo->beginTransaction();
            foreach ($items as $it) {
                $u = $pdo->prepare('UPDATE products SET stock=stock-? WHERE id=? AND stock>=?'); $u->execute([$it['qty'], $it['id'], $it['qty']]);
                if (!$u->rowCount()) throw new Exception('Not enough stock for ' . $it['name'] . '.');
            }
            $pdo->prepare('INSERT INTO orders (user_id,name,email,mobile,billing_address,shipping_address,city,state,pincode,total,payment_method,payment_status) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)')
                ->execute([$_SESSION['uid'], $f['name'], $f['email'], $f['mobile'], $f['billing_address'], $f['shipping_address'], $f['city'], $f['state'], $f['pincode'], $total, $m, $m === 'Cash on Delivery' ? 'Pending' : 'Paid']);
            $oid = $pdo->lastInsertId();
            $ins = $pdo->prepare('INSERT INTO order_items (order_id,product_id,name,price,qty) VALUES (?,?,?,?,?)');
            foreach ($items as $it) $ins->execute([$oid, $it['id'], $it['name'], $it['unit'], $it['qty']]);
            $pdo->commit();
            unset($_SESSION['cart'], $_SESSION['checkout']);
            redirect("order_success.php?id=$oid");
        } catch (Exception $ex) { $pdo->rollBack(); flash($ex->getMessage(), 'err'); redirect('cart.php'); }
    }
}
$title = 'Payment'; require 'includes/header.php'; ?>
<h1>Payment</h1>
<?php foreach ($errors as $er) echo '<div class="flash err">' . e($er) . '</div>'; ?>
<div class="layout">
<form method="post" class="panel"><?= csrf() ?>
  <?php foreach ($methods as $m): ?><label><input type="radio" name="method" value="<?= $m ?>" style="width:auto" <?= ($_POST['method'] ?? 'Cash on Delivery') === $m ? 'checked' : '' ?>> <?= $m ?></label><?php endforeach; ?>
  <label>UPI ID (for UPI)</label><input name="upi" placeholder="name@bank">
  <label>Card number (for card)</label><input name="card" placeholder="1234 5678 9012 3456" maxlength="19">
  <div class="two"><div><label>Expiry</label><input name="exp" placeholder="MM/YY" maxlength="5"></div><div><label>CVV</label><input name="cvv" maxlength="3" type="password"></div></div>
  <p class="muted">Demo payment: no real money is charged and card details are not saved.</p>
  <p><button class="btn">Place order – <?= price($total) ?></button></p>
</form>
<div class="panel"><h3>Ship to</h3><p><?= e($f['name']) ?><br><?= e($f['shipping_address']) ?><br><?= e($f['city']) ?>, <?= e($f['state']) ?> - <?= e($f['pincode']) ?></p>
  <p><b>Total: <?= price($total) ?></b></p><a href="checkout.php"><u>Edit details</u></a></div>
</div>
<?php require 'includes/footer.php'; ?>
