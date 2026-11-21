<?php
require 'config.php'; chk();
if (!q("SELECT COUNT(*) FROM users")->fetchColumn())
  q("INSERT INTO users(username,password,nama) VALUES('admin',?,'Administrator')", [password_hash('admin123', PASSWORD_DEFAULT)]);
if (!empty($_SESSION['u'])) { header('Location: dashboard.php'); exit; }
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $u = q("SELECT * FROM users WHERE username=?", [trim($_POST['username'] ?? '')])->fetch();
  if ($u && password_verify($_POST['password'] ?? '', $u['password'])) {
    session_regenerate_id(true);
    $_SESSION['u'] = ['id' => $u['id'], 'nama' => $u['nama']];
    header('Location: dashboard.php'); exit;
  }
  $err = 'Username atau password salah';
}
?><!DOCTYPE html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Login – BOBO Klinik</title><?= css() ?></head>
<body><main class="w" style="max-width:380px;margin-top:60px">
<h2 style="color:#1f1a7a">Login Basis Data</h2>
<?php if ($err): ?><div class="msg"><?= e($err) ?></div><?php endif ?>
<form method="post">
<input type="hidden" name="tk" value="<?= tok() ?>">
<div class="f" style="grid-template-columns:1fr"><div><label>Username</label><input name="username" required autofocus></div>
<div><label>Password</label><input type="password" name="password" required></div></div>
<button>Masuk</button> <a class="btn y" href="../index.html">← Kembali ke Homepage</a>
</form></main></body></html>
