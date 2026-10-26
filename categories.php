<?php require 'config.php'; $title = 'Categories'; require 'includes/header.php';
$cats = $pdo->query("SELECT * FROM categories WHERE status=1")->fetchAll(); ?>
<h1>Categories</h1>
<div class="grid">
<?php foreach ($cats as $c): ?>
  <div class="card"><img src="<?= img($c['image']) ?>" alt=""><div class="cb">
    <h3><?= e($c['name']) ?></h3><p class="muted"><?= e($c['description']) ?></p>
    <div class="row"><a class="btn" href="products.php?cat=<?= $c['id'] ?>">View products</a></div></div></div>
<?php endforeach; if (!$cats) echo '<p>No categories yet.</p>'; ?>
</div>
<?php require 'includes/footer.php'; ?>
