<?php
// ---- Database & app configuration ----
ob_start();
if (session_status() === PHP_SESSION_NONE) session_start();

$DB = ['host' => 'localhost', 'name' => 'gadgethub', 'user' => 'root', 'pass' => ''];
try {
    $pdo = new PDO("mysql:host={$DB['host']};dbname={$DB['name']};charset=utf8mb4", $DB['user'], $DB['pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $ex) {
    die('Database connection failed. Import database.sql and check config.php.');
}

// ---- Helpers ----
function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function price($n) { return '₹' . number_format((float)$n, 2); }
function final_price($p) { return round($p['price'] * (100 - $p['discount']) / 100, 2); }
function redirect($u) { header("Location: $u"); exit; }
function flash($msg, $type = 'ok') { $_SESSION['flash'] = [$msg, $type]; }
function stars($r) { $r = (int)round($r); return str_repeat('★', $r) . str_repeat('☆', 5 - $r); }
function img($f, $root = '') {
    if ($f) return $root . 'uploads/' . e($f);
    return "data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='400' height='300'><rect width='100%25' height='100%25' fill='%23e8ecf1'/><text x='50%25' y='50%25' text-anchor='middle' fill='%238593a3' font-family='sans-serif' font-size='22'>No image</text></svg>";
}
function csrf() {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16));
    return '<input type="hidden" name="csrf" value="' . $_SESSION['csrf'] . '">';
}
// Every POST request must carry a valid CSRF token
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
    die('Invalid or expired form token. Go back, refresh the page and try again.');
}
function require_login() {
    if (empty($_SESSION['uid'])) {
        $_SESSION['redirect'] = basename($_SERVER['REQUEST_URI']);
        flash('Please log in to continue.', 'err');
        redirect('login.php');
    }
}
function upload($key, $old = '') {
    if (empty($_FILES[$key]['name']) || $_FILES[$key]['error'] !== 0) return $old;
    $ext = strtolower(pathinfo($_FILES[$key]['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif']) || $_FILES[$key]['size'] > 2097152 || !@getimagesize($_FILES[$key]['tmp_name'])) {
        flash('Image must be a jpg, png, webp or gif under 2 MB.', 'err');
        return $old;
    }
    $name = uniqid('img_') . '.' . $ext;
    move_uploaded_file($_FILES[$key]['tmp_name'], __DIR__ . '/uploads/' . $name);
    return $name;
}
// Cart is kept in $_SESSION['cart'][product_id] = qty. Returns [items, total].
function cart_items($pdo) {
    $cart = $_SESSION['cart'] ?? [];
    if (!$cart) return [[], 0];
    $ids = array_map('intval', array_keys($cart));
    $in = implode(',', array_fill(0, count($ids), '?'));
    $st = $pdo->prepare("SELECT * FROM products WHERE status=1 AND id IN ($in)");
    $st->execute($ids);
    $items = []; $total = 0;
    foreach ($st->fetchAll() as $p) {
        $q = min($cart[$p['id']], $p['stock']);
        if ($q < 1) continue;
        $p['qty'] = $q; $p['unit'] = final_price($p); $p['line'] = $p['unit'] * $q;
        $total += $p['line']; $items[] = $p;
    }
    return [$items, $total];
}
function product_card($p) {
    $fp = final_price($p); ob_start(); ?>
    <article class="card">
      <a href="product.php?id=<?= $p['id'] ?>"><img src="<?= img($p['image']) ?>" alt="<?= e($p['name']) ?>"></a>
      <div class="cb">
        <h3><?= e($p['name']) ?></h3>
        <div class="stars"><?= stars($p['rating']) ?> <small><?= e($p['rating']) ?></small></div>
        <p class="muted"><?= e(mb_strimwidth($p['description'], 0, 70, '…')) ?></p>
        <div class="price"><b><?= price($fp) ?></b>
          <?php if ($p['discount'] > 0): ?><s><?= price($p['price']) ?></s> <span class="badge"><?= $p['discount'] ?>% off</span><?php endif; ?></div>
        <div class="row">
          <?php if ($p['stock'] > 0): ?>
          <form method="post" action="cart.php"><?= csrf() ?><input type="hidden" name="action" value="add"><input type="hidden" name="id" value="<?= $p['id'] ?>"><button class="btn">Add to cart</button></form>
          <?php else: ?><span class="muted">Out of stock</span><?php endif; ?>
          <a class="btn alt" href="product.php?id=<?= $p['id'] ?>">View details</a>
        </div>
      </div>
    </article>
    <?php return ob_get_clean();
}
