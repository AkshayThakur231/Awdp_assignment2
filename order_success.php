<?php require 'config.php'; require_login();
$s = $pdo->prepare('SELECT * FROM orders WHERE id=? AND user_id=?'); $s->execute([(int)($_GET['id'] ?? 0), $_SESSION['uid']]); $o = $s->fetch();
if (!$o) redirect('orders.php');
$s = $pdo->prepare('SELECT * FROM order_items WHERE order_id=?'); $s->execute([$o['id']]); $items = $s->fetchAll();
$title = 'Order placed'; require 'includes/header.php'; ?>
<div class="success"><h1>✔ Order placed successfully</h1><p>Thank you, <?= e($o['name']) ?>. A confirmation is on its way to <?= e($o['email']) ?>.</p></div>
<?php require 'includes/order_detail.php'; ?>
<div class="row"><a class="btn" href="index.php">Continue shopping</a><a class="btn alt" href="orders.php">View order history</a></div>
<?php require 'includes/footer.php'; ?>
