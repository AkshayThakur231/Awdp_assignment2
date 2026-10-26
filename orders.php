<?php require 'config.php'; require_login();
$s = $pdo->prepare('SELECT o.*, (SELECT COALESCE(SUM(qty),0) FROM order_items WHERE order_id=o.id) AS n FROM orders o WHERE user_id=? ORDER BY o.id DESC');
$s->execute([$_SESSION['uid']]); $orders = $s->fetchAll();
$title = 'Order history'; require 'includes/header.php'; ?>
<h1>Order history</h1>
<?php if (!$orders): ?><div class="panel"><p>You haven't placed any orders yet.</p><a class="btn" href="products.php">Browse products</a></div><?php else: ?>
<div class="tw"><table><tr><th>Order ID</th><th>Date</th><th>Products</th><th>Total</th><th>Payment</th><th>Status</th><th></th></tr>
<?php foreach ($orders as $o): ?>
  <tr><td>#<?= $o['id'] ?></td><td><?= date('d M Y', strtotime($o['created_at'])) ?></td><td><?= $o['n'] ?></td><td><?= price($o['total']) ?></td>
  <td><?= e($o['payment_method']) ?></td><td><span class="tag <?= $o['status'] ?>"><?= $o['status'] ?></span></td>
  <td><a class="btn alt sm" href="order_view.php?id=<?= $o['id'] ?>">View details</a></td></tr>
<?php endforeach; ?></table></div><?php endif; require 'includes/footer.php'; ?>
