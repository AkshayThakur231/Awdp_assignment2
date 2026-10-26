<?php require 'config.php'; $errors = []; $v = ['name' => '', 'email' => '', 'message' => ''];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($v as $k => $_) $v[$k] = trim($_POST[$k] ?? '');
    if (strlen($v['name']) < 2) $errors[] = 'Enter your name.';
    if (!filter_var($v['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Enter a valid email.';
    if (strlen($v['message']) < 10) $errors[] = 'Message must be at least 10 characters.';
    if (!$errors) {
        $pdo->prepare('INSERT INTO contacts (name,email,message) VALUES (?,?,?)')->execute([$v['name'], $v['email'], $v['message']]);
        flash('Thanks! We have received your message and will reply by email.'); redirect('contact.php');
    }
}
$title = 'Contact us'; require 'includes/header.php'; ?>
<h1>Contact us</h1>
<div class="layout">
<form method="post" class="panel"><?= csrf() ?>
  <?php foreach ($errors as $er) echo '<div class="flash err">' . e($er) . '</div>'; ?>
  <label>Name</label><input name="name" value="<?= e($v['name']) ?>" required>
  <label>Email</label><input type="email" name="email" value="<?= e($v['email']) ?>" required>
  <label>Message</label><textarea name="message" required><?= e($v['message']) ?></textarea>
  <p><button class="btn">Send message</button></p>
</form>
<div class="panel"><h3>Get in touch</h3>
  <p><b>Address</b><br>College of Technology, Silver Oak University,<br>Ahmedabad, Gujarat 382481</p>
  <p><b>Phone</b><br>+91 98765 43210</p><p><b>Email</b><br>support@gadgethub.example</p></div>
</div>
<?php require 'includes/footer.php'; ?>
