<?php
session_start();
try {
  $pdo = new PDO('mysql:host=localhost;dbname=bobo_klinik;charset=utf8mb4', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ]);
} catch (PDOException $x) {
  die('Koneksi gagal. Pastikan MySQL di XAMPP aktif dan database.sql sudah diimpor.');
}
function q($sql, $p = []) { global $pdo; $s = $pdo->prepare($sql); $s->execute($p); return $s; }
function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function rp($n) { return 'Rp ' . number_format((float)$n, 0, ',', '.'); }
function tgl($d) { return $d ? date('d/m/Y', strtotime($d)) : ''; }
function auth() { if (empty($_SESSION['u'])) { header('Location: login.php'); exit; } }
function tok() { if (empty($_SESSION['tk'])) $_SESSION['tk'] = bin2hex(random_bytes(16)); return $_SESSION['tk']; }
function chk() {
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && !hash_equals($_SESSION['tk'] ?? '', $_POST['tk'] ?? '')) { http_response_code(400); die('Token tidak valid'); }
}
function terbilang($x) {
  $x = abs((int)$x);
  $a = ['', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh', 'sebelas'];
  if ($x < 12) return $a[$x];
  if ($x < 20) return terbilang($x - 10) . ' belas';
  if ($x < 100) return terbilang(intdiv($x, 10)) . ' puluh ' . terbilang($x % 10);
  if ($x < 200) return 'seratus ' . terbilang($x - 100);
  if ($x < 1000) return terbilang(intdiv($x, 100)) . ' ratus ' . terbilang($x % 100);
  if ($x < 2000) return 'seribu ' . terbilang($x - 1000);
  if ($x < 1000000) return terbilang(intdiv($x, 1000)) . ' ribu ' . terbilang($x % 1000);
  if ($x < 1000000000) return terbilang(intdiv($x, 1000000)) . ' juta ' . terbilang($x % 1000000);
  return terbilang(intdiv($x, 1000000000)) . ' miliar ' . terbilang($x % 1000000000);
}
function rupiah_kata($n) {
  $t = trim(preg_replace('/\s+/', ' ', terbilang($n)));
  return ucfirst($t === '' ? 'nol' : $t) . ' rupiah';
}
function css() { return '<style>
:root{color-scheme:light}
*{box-sizing:border-box}
body{margin:0;font-family:system-ui,Arial,sans-serif;background:#fff;color:#111;line-height:1.5}
.w{max-width:1000px;margin:0 auto;padding:16px}
nav.tabs{display:flex;flex-wrap:wrap;gap:6px;border-bottom:2px solid #111;padding:10px 16px;align-items:center}
nav.tabs a.t{padding:8px 14px;border:2px solid #111;border-radius:8px;text-decoration:none;color:#111;font-weight:700;font-size:.9rem}
nav.tabs a.t.on{background:#1f1a7a;color:#fff}
.btn,button{background:#1f1a7a;color:#fff;border:2px solid #111;border-radius:8px;padding:8px 16px;font:inherit;font-weight:700;font-size:.9rem;cursor:pointer;text-decoration:none;display:inline-block}
.btn.y{background:#f4b942;color:#111}.btn.r{background:#fff;color:#b00020}
table{width:100%;border-collapse:collapse}th,td{border:1px solid #111;padding:6px 8px;text-align:left;font-size:.9rem}th{background:#f4f4f6}
input,select{padding:8px;border:2px solid #111;border-radius:8px;font:inherit;width:100%;background:#fff;color:#111}
label{font-weight:700;font-size:.85rem;display:block;margin-bottom:3px}
.f{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:10px;margin-bottom:10px}
.cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:12px;margin-bottom:20px}
.c{border:2px solid #111;border-radius:12px;padding:14px}.c b{display:block;font-size:1.5rem}
.msg{padding:10px;border:2px solid #111;border-radius:8px;margin:10px 0;background:#f4f4f6}
.x{overflow-x:auto;margin:12px 0}.bar{height:12px;background:#1f1a7a;border-radius:6px}
.n{border:2px solid #111;padding:20px;max-width:720px;margin:12px auto}.n h1{margin:0;text-align:center;color:#1f1a7a}.n .s{text-align:center;font-size:.85rem;font-weight:700}
@media print{.np{display:none!important}.w{max-width:none;padding:0}.n{border:0}}
</style>'; }
function head($t, $a = '') {
  $tabs = ['dashboard.php' => ['Dashboard', 'd'], 'nota.php' => ['Nota', 'n'], 'crud.php?t=pemilik' => ['Pemilik', 'pemilik'],
    'crud.php?t=hewan' => ['Hewan', 'hewan'], 'crud.php?t=layanan' => ['Layanan', 'layanan'], 'crud.php?t=dokter' => ['Dokter', 'dokter'], 'cetak.php' => ['Cetak', 'cetak']];
  echo '<!DOCTYPE html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>' . e($t) . ' – BOBO Klinik</title>' . css() . '</head><body><nav class="tabs np">';
  foreach ($tabs as $u => [$l, $k]) echo '<a class="t ' . ($a === $k ? 'on' : '') . '" href="' . $u . '">' . $l . '</a>';
  echo '<span style="flex:1"></span><a class="btn y" href="../index.html">← Kembali ke Homepage</a><a class="btn r" href="logout.php">Keluar</a></nav><main class="w">';
}
function foot() { echo '</main></body></html>'; }
