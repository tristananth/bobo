<?php
require 'config.php'; auth(); chk();
$msg = $_GET['m'] ?? '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  try {
    if (($_POST['act'] ?? '') === 'del') { q("DELETE FROM nota WHERE id=?", [(int)$_POST['id']]); header('Location: nota.php?m=Nota+dihapus'); exit; }
    $id = (int)($_POST['id'] ?? 0);
    $tg = $_POST['tanggal'] ?? ''; $po = (int)($_POST['pemilik_id'] ?? 0); $he = (int)($_POST['hewan_id'] ?? 0); $dk = (int)($_POST['dokter_id'] ?? 0);
    $rows = []; $tot = 0;
    foreach (($_POST['layanan_id'] ?? []) as $i => $l) {
      if (!$l) continue;
      $h = (int)preg_replace('/\D/', '', $_POST['harga'][$i] ?? '0');
      $rows[] = [(int)$l, trim($_POST['ket'][$i] ?? ''), $h]; $tot += $h;
    }
    if (!$tg || !$po || !$he || !$dk || !$rows) throw new Exception('Lengkapi tanggal, pemilik, hewan, dokter, dan minimal satu rincian');
    if (!q("SELECT 1 FROM hewan WHERE id=? AND pemilik_id=?", [$he, $po])->fetch()) throw new Exception('Hewan tidak sesuai dengan pemilik');
    $pdo->beginTransaction();
    if ($id) { q("UPDATE nota SET tanggal=?,pemilik_id=?,hewan_id=?,dokter_id=?,total=? WHERE id=?", [$tg, $po, $he, $dk, $tot, $id]); q("DELETE FROM nota_detail WHERE nota_id=?", [$id]); }
    else {
      q("INSERT INTO nota(no_nota,tanggal,pemilik_id,hewan_id,dokter_id,total,user_id) VALUES(?,?,?,?,?,?,?)", [uniqid('T'), $tg, $po, $he, $dk, $tot, $_SESSION['u']['id']]);
      $id = $pdo->lastInsertId();
      q("UPDATE nota SET no_nota=? WHERE id=?", [sprintf('BK-%s-%05d', date('Y', strtotime($tg)), $id), $id]);
    }
    foreach ($rows as $r) q("INSERT INTO nota_detail(nota_id,layanan_id,keterangan,harga) VALUES(?,?,?,?)", [$id, $r[0], $r[1], $r[2]]);
    $pdo->commit(); header('Location: nota.php?m=Nota+disimpan'); exit;
  } catch (Exception $x) { if ($pdo->inTransaction()) $pdo->rollBack(); $msg = $x->getMessage(); }
}
$edit = null; $rows = [];
if (isset($_GET['edit'])) { $edit = q("SELECT * FROM nota WHERE id=?", [(int)$_GET['edit']])->fetch(); if ($edit) $rows = q("SELECT layanan_id,keterangan,harga FROM nota_detail WHERE nota_id=? ORDER BY id", [$edit['id']])->fetchAll(); }
$form = isset($_GET['new']) || $edit;
$P = q("SELECT id,nama FROM pemilik ORDER BY nama")->fetchAll(PDO::FETCH_KEY_PAIR);
$D = q("SELECT id,nama FROM dokter ORDER BY nama")->fetchAll(PDO::FETCH_KEY_PAIR);
$LAY = q("SELECT id,nama FROM layanan ORDER BY id")->fetchAll();
$HEW = q("SELECT id,pemilik_id,nama,jenis_hewan FROM hewan ORDER BY nama")->fetchAll();
$list = q("SELECT n.id,n.no_nota,n.tanggal,n.total,p.nama pemilik,h.nama hewan FROM nota n JOIN pemilik p ON p.id=n.pemilik_id JOIN hewan h ON h.id=n.hewan_id ORDER BY n.tanggal DESC,n.id DESC")->fetchAll();
$J = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT;
head('Nota', 'n'); ?>
<h2>Nota Administrasi</h2>
<?php if ($msg): ?><div class="msg"><?= e($msg) ?></div><?php endif ?>
<?php if (!$form): ?>
<a class="btn" href="nota.php?new=1">+ Nota Baru</a>
<div class="x"><table><tr><th>No</th><th>Tanggal</th><th>Pemilik</th><th>Hewan</th><th>Total</th><th>Aksi</th></tr>
<?php foreach ($list as $r): ?><tr><td><?= e($r['no_nota']) ?></td><td><?= tgl($r['tanggal']) ?></td><td><?= e($r['pemilik']) ?></td><td><?= e($r['hewan']) ?></td><td><?= rp($r['total']) ?></td>
<td><a class="btn y" href="nota.php?edit=<?= $r['id'] ?>">Ubah</a> <a class="btn" href="cetak.php?id=<?= $r['id'] ?>">Cetak</a>
<form method="post" style="display:inline" onsubmit="return confirm('Hapus nota ini?')"><input type="hidden" name="tk" value="<?= tok() ?>"><input type="hidden" name="act" value="del"><input type="hidden" name="id" value="<?= $r['id'] ?>"><button style="background:#fff;color:#b00020">Hapus</button></form></td></tr>
<?php endforeach; if (!$list) echo '<tr><td colspan="6">Belum ada nota</td></tr>'; ?></table></div>
<?php else: ?>
<form method="post">
<input type="hidden" name="tk" value="<?= tok() ?>"><input type="hidden" name="id" value="<?= (int)($edit['id'] ?? 0) ?>">
<div class="f">
<div><label>Tanggal</label><input type="date" name="tanggal" value="<?= e($edit['tanggal'] ?? date('Y-m-d')) ?>" required></div>
<div><label>Pemilik</label><select name="pemilik_id" id="po"><option value="">Pilih pemilik</option><?php foreach ($P as $k => $l): ?><option value="<?= $k ?>" <?= ($edit['pemilik_id'] ?? 0) == $k ? 'selected' : '' ?>><?= e($l) ?></option><?php endforeach ?></select></div>
<div><label>Hewan</label><select name="hewan_id" id="he"></select></div>
<div><label>Dokter Hewan</label><select name="dokter_id"><option value="">Pilih dokter</option><?php foreach ($D as $k => $l): ?><option value="<?= $k ?>" <?= ($edit['dokter_id'] ?? 0) == $k ? 'selected' : '' ?>><?= e($l) ?></option><?php endforeach ?></select></div>
</div>
<h3>Perincian</h3>
<div id="rows"></div>
<button type="button" class="btn y" onclick="add()">+ Tambah Rincian</button>
<p><b>Total: <span id="tot">Rp 0</span></b></p>
<button>Simpan Nota</button> <a class="btn y" href="nota.php">Batal</a>
</form>
<script>
const LAY=<?= json_encode($LAY, $J) ?>,HEW=<?= json_encode($HEW, $J) ?>,ROWS=<?= json_encode($rows, $J) ?>,CUR=<?= json_encode($edit['hewan_id'] ?? '', $J) ?>;
const $=s=>document.querySelector(s);
function hw(c){const p=$('#po').value,s=$('#he');s.innerHTML='';s.add(new Option('Pilih hewan',''));HEW.filter(h=>h.pemilik_id==p).forEach(h=>{const o=new Option(h.nama+' ('+h.jenis_hewan+')',h.id);if(h.id==c)o.selected=true;s.add(o)})}
function sum(){let t=0;document.querySelectorAll('[name="harga[]"]').forEach(i=>t+=parseInt(i.value.replace(/\D/g,''))||0);$('#tot').textContent='Rp '+t.toLocaleString('id-ID')}
function add(r){r=r||{};const d=document.createElement('div');d.className='f';
d.innerHTML='<select name="layanan_id[]"></select><input name="ket[]" placeholder="Keterangan"><input name="harga[]" inputmode="numeric" placeholder="Harga (Rp)"><button type="button" style="background:#fff;color:#b00020">Hapus</button>';
const s=d.querySelector('select');s.add(new Option('Pilih layanan',''));LAY.forEach(l=>{const o=new Option(l.nama,l.id);if(l.id==r.layanan_id)o.selected=true;s.add(o)});
d.querySelector('[name="ket[]"]').value=r.keterangan||'';const h=d.querySelector('[name="harga[]"]');h.value=r.harga||'';h.oninput=sum;
d.querySelector('button').onclick=()=>{d.remove();sum()};$('#rows').append(d);sum()}
$('#po').onchange=()=>hw('');hw(CUR);(ROWS.length?ROWS:[{}]).forEach(add);
</script>
<?php endif;
foot();
