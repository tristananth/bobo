<?php
require 'config.php'; auth();
$id = (int)($_GET['id'] ?? 0);
if ($id) {
  $n = q("SELECT n.*,p.nama pemilik,p.alamat,h.nama hewan,h.jenis_hewan,h.ras,d.nama dokter FROM nota n JOIN pemilik p ON p.id=n.pemilik_id JOIN hewan h ON h.id=n.hewan_id JOIN dokter d ON d.id=n.dokter_id WHERE n.id=?", [$id])->fetch();
  if (!$n) die('Nota tidak ditemukan');
  $det = q("SELECT l.nama,d.keterangan,d.harga FROM nota_detail d JOIN layanan l ON l.id=d.layanan_id WHERE d.nota_id=? ORDER BY d.id", [$id])->fetchAll();
  head('Cetak Nota', 'cetak'); ?>
<div class="np"><button onclick="window.print()">🖨 Cetak</button> <a class="btn y" href="nota.php">Kembali ke Nota</a></div>
<div class="n">
<h1>BOBO KLINIK &amp; PET SHOP</h1>
<div class="s">VAKSINASI, OPERASI, GROOMING, CHECK UP · TERIMA PANGGILAN<br>RUKO TRAFALGAR SRC/38 (DERETAN LOWSON) KOTA WISATA - CIBUBUR · TEL 021-22962747</div>
<hr>
<p><b>NOTA ADMINISTRASI</b> &nbsp; <b>NO.: <?= e($n['no_nota']) ?></b></p>
<table style="border:0"><?php
$h = ['Anjing', 'Kucing', 'Kera', 'Unggas']; $cb = '';
foreach ($h as $j) $cb .= ($n['jenis_hewan'] === $j ? '☑' : '☐') . ' ' . $j . ' &nbsp; ';
$cb .= ($n['jenis_hewan'] === 'Lainnya' ? '☑' : '☐') . ' Dll.';
foreach (['Tanggal' => tgl($n['tanggal']), 'Pemilik' => $n['pemilik'], 'Hewan' => null, 'Nama' => $n['hewan'], 'Jenis' => $n['ras'], 'Alamat' => $n['alamat']] as $k => $v)
  echo '<tr><td style="border:0;width:90px">' . $k . '</td><td style="border:0">: ' . ($v === null ? $cb : e($v)) . '</td></tr>'; ?></table>
<h3 style="text-align:center">PERINCIAN / KET.</h3>
<table><tr><th>Layanan</th><th>Keterangan</th><th>Rp</th></tr>
<?php foreach ($det as $r): ?><tr><td><?= e($r['nama']) ?></td><td><?= e($r['keterangan']) ?></td><td style="text-align:right"><?= number_format($r['harga'], 0, ',', '.') ?></td></tr><?php endforeach ?>
<tr><th colspan="2">Total</th><th style="text-align:right">Rp <?= number_format($n['total'], 0, ',', '.') ?></th></tr></table>
<p>Terbilang: <i><?= e(rupiah_kata($n['total'])) ?></i></p>
<p style="text-align:right">Cibubur, <?= tgl($n['tanggal']) ?><br><b>Dokter Hewan,</b><br><br><br><?= e($n['dokter']) ?></p>
</div>
<?php foot(); exit; }
$d1 = $_GET['dari'] ?? date('Y-m-01'); $d2 = $_GET['sampai'] ?? date('Y-m-d');
$R = q("SELECT n.no_nota,n.tanggal,n.total,p.nama pemilik,h.nama hewan FROM nota n JOIN pemilik p ON p.id=n.pemilik_id JOIN hewan h ON h.id=n.hewan_id WHERE n.tanggal BETWEEN ? AND ? ORDER BY n.tanggal,n.id", [$d1, $d2])->fetchAll();
head('Laporan', 'cetak'); ?>
<h2>Laporan Nota</h2>
<form class="np" method="get"><div class="f"><div><label>Dari</label><input type="date" name="dari" value="<?= e($d1) ?>"></div><div><label>Sampai</label><input type="date" name="sampai" value="<?= e($d2) ?>"></div></div>
<button>Tampilkan</button> <button type="button" onclick="window.print()">🖨 Cetak Laporan</button></form>
<p>Periode: <?= tgl($d1) ?> – <?= tgl($d2) ?></p>
<div class="x"><table><tr><th>No</th><th>Tanggal</th><th>Pemilik</th><th>Hewan</th><th>Total</th></tr>
<?php $s = 0; foreach ($R as $r): $s += $r['total']; ?><tr><td><?= e($r['no_nota']) ?></td><td><?= tgl($r['tanggal']) ?></td><td><?= e($r['pemilik']) ?></td><td><?= e($r['hewan']) ?></td><td style="text-align:right"><?= rp($r['total']) ?></td></tr><?php endforeach ?>
<tr><th colspan="4">Total (<?= count($R) ?> nota)</th><th style="text-align:right"><?= rp($s) ?></th></tr></table></div>
<?php foot();
