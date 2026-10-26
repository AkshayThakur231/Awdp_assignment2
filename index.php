<?php require 'config.php'; $title = 'Home'; require 'includes/header.php';
$cats = $pdo->query("SELECT * FROM categories WHERE status=1 LIMIT 6")->fetchAll();
$featured = $pdo->query("SELECT * FROM products WHERE status=1 AND featured=1 ORDER BY id DESC LIMIT 8")->fetchAll();
$latest = $pdo->query("SELECT * FROM products WHERE status=1 ORDER BY created_at DESC, id DESC LIMIT 8")->fetchAll();
$offers = $pdo->query("SELECT * FROM products WHERE status=1 AND discount>0 ORDER BY discount DESC LIMIT 4")->fetchAll();
?>
<section class="hero">
  <h1>Tech that keeps up with your day</h1>
  <p>Earbuds, chargers, power banks and watches – with offers up to <?= (int)($offers[0]['discount'] ?? 30) ?>% off.</p>
  <div><a class="btn" href="products.php">Shop all products</a></div>
</section>

<h2>Shop by category</h2>
<div class="grid">
<?php foreach ($cats as $c): ?>
  <a class="card" href="products.php?cat=<?= $c['id'] ?>"><img src="<?= img($c['image']) ?>" alt=""><div class="cb"><h3><?= e($c['name']) ?></h3><p class="muted"><?= e($c['description']) ?></p></div></a>
<?php endforeach; ?>
</div>

<h2>Featured products</h2>
<div class="grid"><?php foreach ($featured as $p) echo product_card($p); ?></div>

<div class="promo">
  <div><h2>This week's offers</h2><p class="muted">Biggest discounts across the store. While stock lasts.</p></div>
  <a class="btn" href="products.php">See all deals</a>
</div>
<div class="grid" style="margin-top:18px"><?php foreach ($offers as $p) echo product_card($p); ?></div>

<h2>New arrivals</h2>
<div class="grid"><?php foreach ($latest as $p) echo product_card($p); ?></div>
<?php require 'includes/footer.php'; ?>
