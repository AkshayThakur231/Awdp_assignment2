<?php require 'config.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0); $a = $_POST['action'] ?? ''; $qty = max(1, (int)($_POST['qty'] ?? 1));
    $st = $pdo->prepare('SELECT stock FROM products WHERE id=? AND status=1'); $st->execute([$id]); $p = $st->fetch();
    $cur = $_SESSION['cart'][$id] ?? 0;
    if ($a === 'remove') unset($_SESSION['cart'][$id]);
    elseif ($p) {
        if ($a === 'add' || $a === 'buy') {
            $n = min($cur + $qty, $p['stock']);
            if ($n > 0) { $_SESSION['cart'][$id] = $n; flash('Added to your cart.'); } else flash('Sorry, this item is out of stock.', 'err');
        } elseif ($a === 'inc') $_SESSION['cart'][$id] = min($cur + 1, $p['stock']);
        elseif ($a === 'dec') { if ($cur > 1) $_SESSION['cart'][$id] = $cur - 1; else unset($_SESSION['cart'][$id]); }
    }
    redirect($a === 'buy' ? 'checkout.php' : 'cart.php');
}
$title = 'Your cart'; require 'includes/header.php';
[$items, $total] = cart_items($pdo); ?>
<h1>Your cart</h1>
<?php if (!$items): ?>
  <div class="panel"><p>Your cart is empty.</p><a class="btn" href="products.php">Start shopping</a></div>
<?php else: ?>
<div class="layout">
  <div class="tw"><table><tr><th>Product</th><th>Price</th><th>Quantity</th><th>Subtotal</th><th></th></tr>
  <?php foreach ($items as $i): ?>
    <tr><td><b><?= e($i['name']) ?></b></td><td><?= price($i['unit']) ?></td>
      <td><div class="qty">
        <?php foreach (['dec' => '−', 'inc' => '+'] as $act => $sym): if ($act === 'inc') echo "<b>{$i['qty']}</b>"; ?>
          <form method="post"><?= csrf() ?><input type="hidden" name="id" value="<?= $i['id'] ?>"><button class="btn alt" name="action" value="<?= $act ?>"><?= $sym ?></button></form>
        <?php endforeach; ?></div></td>
      <td><?= price($i['line']) ?></td>
      <td><form method="post"><?= csrf() ?><input type="hidden" name="id" value="<?= $i['id'] ?>"><button class="btn danger sm" name="action" value="remove">Remove</button></form></td></tr>
  <?php endforeach; ?></table></div>
  <div class="panel"><h3>Order total</h3>
    <p>Subtotal: <?= price($total) ?></p><p>Shipping: Free</p><p><b>Total: <?= price($total) ?></b></p>
    <a class="btn full" href="checkout.php" style="text-align:center">Proceed to checkout</a></div>
</div>
<?php endif; require 'includes/footer.php'; ?>
