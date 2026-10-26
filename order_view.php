<?php require 'config.php'; require_login();
$s = $pdo->prepare('SELECT * FROM orders WHERE id=? AND user_id=?'); $s->execute([(int)($_GET['id'] ?? 0), $_SESSION['uid']]); $o = $s->fetch();
if (!$o) redirect('orders.php');
$s = $pdo->prepare('SELECT * FROM order_items WHERE order_id=?'); $s->execute([$o['id']]); $items = $s->fetchAll();
$title = 'Order #' . $o['id']; require 'includes/header.php'; ?>
<h1>Order #<?= $o['id'] ?></h1>
<?php require 'includes/order_detail.php'; ?>
<a class="btn alt" href="orders.php">Back to orders</a>
<?php require 'includes/footer.php'; ?>
