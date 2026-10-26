<?php require 'config.php';
$st = $pdo->prepare("SELECT p.*, c.name AS cname FROM products p JOIN categories c ON c.id=p.category_id WHERE p.id=? AND p.status=1");
$st->execute([(int)($_GET['id'] ?? 0)]); $p = $st->fetch();
if (!$p) { flash('Product not found.', 'err'); redirect('products.php'); }
$title = $p['name']; require 'includes/header.php'; $fp = final_price($p); ?>
<div class="detail">
  <img src="<?= img($p['image']) ?>" alt="<?= e($p['name']) ?>">
  <div>
    <p class="muted"><a href="products.php?cat=<?= $p['category_id'] ?>"><?= e($p['cname']) ?></a></p>
    <h1 style="margin:0"><?= e($p['name']) ?></h1>
    <div class="stars"><?= stars($p['rating']) ?> <small><?= e($p['rating']) ?></small></div>
    <p class="price"><b style="font-size:1.7rem"><?= price($fp) ?></b>
      <?php if ($p['discount'] > 0): ?><s><?= price($p['price']) ?></s> <span class="badge"><?= $p['discount'] ?>% off</span><?php endif; ?></p>
    <p><?= nl2br(e($p['description'])) ?></p>
    <p><b>Availability:</b> <?= $p['stock'] > 0 ? "<span class='tag Active'>{$p['stock']} in stock</span>" : "<span class='tag Cancelled'>Out of stock</span>" ?></p>
    <?php if ($p['specs']): ?><div class="tw"><table>
      <?php foreach (explode("\n", $p['specs']) as $line): if (!trim($line)) continue; [$k, $v] = array_pad(explode(':', $line, 2), 2, ''); ?>
        <tr><th style="width:40%"><?= e(trim($k)) ?></th><td><?= e(trim($v)) ?></td></tr>
      <?php endforeach; ?></table></div><?php endif; ?>
    <?php if ($p['stock'] > 0): ?>
    <form method="post" action="cart.php" style="margin-top:16px"><?= csrf() ?>
      <input type="hidden" name="id" value="<?= $p['id'] ?>">
      <label for="qty">Quantity</label><input id="qty" type="number" name="qty" value="1" min="1" max="<?= $p['stock'] ?>" style="max-width:100px">
      <div class="row"><button class="btn" name="action" value="add">Add to cart</button><button class="btn alt" name="action" value="buy">Buy now</button></div>
    </form><?php endif; ?>
  </div>
</div>
<?php require 'includes/footer.php'; ?>
