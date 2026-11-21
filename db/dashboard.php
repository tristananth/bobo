<?php
require 'config.php'; auth();
$b = date('Y-m');
$n = q("SELECT COUNT(*) c, COALESCE(SUM(total),0) s FROM nota WHERE DATE_FORMAT(tanggal,'%Y-%m')=?", [$b])->fetch();
$po = q("SELECT COUNT(*) FROM pemilik")->fetchColumn();
$he = q("SELECT COUNT(*) FROM hewan")->fetchColumn();
$L = q("SELECT l.nama, COUNT(d.id) n, SUM(d.harga) s FROM nota_detail d JOIN nota t ON t.id=d.nota_id JOIN layanan l ON l.id=d.layanan_id WHERE DATE_FORMAT(t.tanggal,'%Y-%m')=? GROUP BY l.id ORDER BY s DESC", [$b])->fetchAll();
$max = max(1, ...array_map(fn($r) => (int)$r['s'], $L ?: [['s' => 1]]));
$R = q("SELECT n.id,n.no_nota,n.tanggal,n.total,p.nama pemilik,h.nama hewan FROM nota n JOIN pemilik p ON p.id=n.pemilik_id JOIN hewan h ON h.id=n.hewan_id ORDER BY n.tanggal DESC,n.id DESC LIMIT 5")->fetchAll();
head('Dashboard', 'd'); ?>
<h2>Halo, <?= e($_SESSION['u']['nama']) ?></h2>
<div class="cards">
<div class="c">Nota bulan ini<b><?= (int)$n['c'] ?></b></div>
<div class="c">Pendapatan bulan ini<b><?= rp($n['s']) ?></b></div>
<div class="c">Total pemilik<b><?= (int)$po ?></b></div>
<div class="c">Total hewan<b><?= (int)$he ?></b></div>
</div>
<h3>Pendapatan per layanan (bulan ini)</h3>
<div class="x"><table><tr><th>Layanan</th><th>Jumlah</th><th>Pendapatan</th><th style="width:30%"></th></tr>
<?php foreach ($L as $r): ?><tr><td><?= e($r['nama']) ?></td><td><?= (int)$r['n'] ?></td><td><?= rp($r['s']) ?></td><td><div class="bar" style="width:<?= round($r['s'] / $max * 100) ?>%"></div></td></tr>
<?php endforeach; if (!$L) echo '<tr><td colspan="4">Belum ada data</td></tr>'; ?></table></div>
<h3>Nota terakhir</h3>
<div class="x"><table><tr><th>No</th><th>Tanggal</th><th>Pemilik</th><th>Hewan</th><th>Total</th></tr>
<?php foreach ($R as $r): ?><tr><td><a href="cetak.php?id=<?= $r['id'] ?>"><?= e($r['no_nota']) ?></a></td><td><?= tgl($r['tanggal']) ?></td><td><?= e($r['pemilik']) ?></td><td><?= e($r['hewan']) ?></td><td><?= rp($r['total']) ?></td></tr>
<?php endforeach; if (!$R) echo '<tr><td colspan="5">Belum ada nota</td></tr>'; ?></table></div>
<?php foot();
