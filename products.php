<?php require 'config.php';
$cat = (int)($_GET['cat'] ?? 0); $q = trim($_GET['q'] ?? '');
$sql = "SELECT * FROM products WHERE status=1"; $args = [];
if ($cat) { $sql .= " AND category_id=?"; $args[] = $cat; }
if ($q !== '') { $sql .= " AND name LIKE ?"; $args[] = "%$q%"; }
$st = $pdo->prepare($sql . " ORDER BY id DESC"); $st->execute($args); $products = $st->fetchAll();
$cname = '';
if ($cat) { $s = $pdo->prepare("SELECT name FROM categories WHERE id=?"); $s->execute([$cat]); $cname = $s->fetchColumn(); }
$title = $cname ?: 'All products'; require 'includes/header.php'; ?>
<h1><?= e($title) ?></h1>
<form method="get" class="row" style="margin-bottom:18px">
  <?php if ($cat): ?><input type="hidden" name="cat" value="<?= $cat ?>"><?php endif; ?>
  <input style="max-width:320px" name="q" placeholder="Search products" value="<?= e($q) ?>"><button class="btn">Search</button>
</form>
<div class="grid"><?php foreach ($products as $p) echo product_card($p); ?></div>
<?php if (!$products) echo '<p>No products found. Try another search or <a href="categories.php"><u>browse categories</u></a>.</p>';
require 'includes/footer.php'; ?>
