<?php
require 'config.php'; auth(); chk();
$T = [
  'pemilik' => ['Pemilik', ['nama' => ['Nama', 'text'], 'alamat' => ['Alamat', 'text'], 'telepon' => ['Telepon', 'text']]],
  'hewan' => ['Hewan', ['pemilik_id' => ['Pemilik', 'fk:pemilik:nama'], 'nama' => ['Nama Hewan', 'text'],
    'jenis_hewan' => ['Jenis Hewan', 'enum:Anjing,Kucing,Kera,Unggas,Lainnya'], 'ras' => ['Jenis / Ras', 'text']]],
  'layanan' => ['Layanan', ['nama' => ['Nama Layanan', 'text']]],
  'dokter' => ['Dokter Hewan', ['nama' => ['Nama Dokter', 'text']]],
];
$t = $_GET['t'] ?? 'pemilik'; if (!isset($T[$t])) $t = 'pemilik';
[$jud, $F] = $T[$t];
$opt = [];
foreach ($F as $c => $d) if (str_starts_with($d[1], 'fk:')) { [, $tb, $lb] = explode(':', $d[1]); $opt[$c] = q("SELECT id,$lb FROM $tb ORDER BY $lb")->fetchAll(PDO::FETCH_KEY_PAIR); }
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  try {
    $id = (int)($_POST['id'] ?? 0);
    if (($_POST['act'] ?? '') === 'del') { q("DELETE FROM `$t` WHERE id=?", [$id]); $msg = 'Data dihapus'; }
    else {
      $v = [];
      foreach ($F as $c => $d) {
        $x = trim($_POST[$c] ?? '');
        if ($x === '' && !in_array($c, ['alamat', 'telepon', 'ras'])) throw new Exception($d[0] . ' wajib diisi');
        $v[] = $x === '' ? null : $x;
      }
      $cols = array_keys($F);
      if ($id) q("UPDATE `$t` SET " . implode(',', array_map(fn($c) => "`$c`=?", $cols)) . " WHERE id=?", [...$v, $id]);
      else q("INSERT INTO `$t`(" . implode(',', array_map(fn($c) => "`$c`", $cols)) . ") VALUES(" . implode(',', array_fill(0, count($cols), '?')) . ")", $v);
      $msg = 'Data disimpan';
    }
  } catch (Exception $x) {
    $msg = ($x instanceof PDOException && $x->getCode() == '23000') ? 'Gagal: data terkait dengan data lain atau sudah ada' : $x->getMessage();
  }
}
$edit = isset($_GET['edit']) ? (q("SELECT * FROM `$t` WHERE id=?", [(int)$_GET['edit']])->fetch() ?: []) : [];
$rows = q("SELECT * FROM `$t` ORDER BY id DESC")->fetchAll();
head($jud, $t); ?>
<h2><?= e($jud) ?></h2>
<?php if ($msg): ?><div class="msg"><?= e($msg) ?></div><?php endif ?>
<form method="post" action="crud.php?t=<?= $t ?>">
<input type="hidden" name="tk" value="<?= tok() ?>"><input type="hidden" name="id" value="<?= e($edit['id'] ?? 0) ?>">
<div class="f">
<?php foreach ($F as $c => $d): $val = $edit[$c] ?? ''; ?>
<div><label><?= e($d[0]) ?></label>
<?php if (isset($opt[$c])): ?><select name="<?= $c ?>"><option value="">Pilih</option>
<?php foreach ($opt[$c] as $k => $l): ?><option value="<?= $k ?>" <?= $val == $k ? 'selected' : '' ?>><?= e($l) ?></option><?php endforeach ?></select>
<?php elseif (str_starts_with($d[1], 'enum:')): ?><select name="<?= $c ?>">
<?php foreach (explode(',', substr($d[1], 5)) as $o): ?><option <?= $val === $o ? 'selected' : '' ?>><?= e($o) ?></option><?php endforeach ?></select>
<?php else: ?><input name="<?= $c ?>" value="<?= e($val) ?>"><?php endif ?></div>
<?php endforeach ?>
</div>
<button>Simpan</button> <?php if ($edit): ?><a class="btn y" href="crud.php?t=<?= $t ?>">Batal</a><?php endif ?>
</form>
<div class="x"><table><tr><?php foreach ($F as $d): ?><th><?= e($d[0]) ?></th><?php endforeach ?><th>Aksi</th></tr>
<?php foreach ($rows as $r): ?><tr>
<?php foreach ($F as $c => $d): ?><td><?= e(isset($opt[$c]) ? ($opt[$c][$r[$c]] ?? '') : $r[$c]) ?></td><?php endforeach ?>
<td><a class="btn y" href="crud.php?t=<?= $t ?>&edit=<?= $r['id'] ?>">Ubah</a>
<form method="post" action="crud.php?t=<?= $t ?>" style="display:inline" onsubmit="return confirm('Hapus data ini?')">
<input type="hidden" name="tk" value="<?= tok() ?>"><input type="hidden" name="act" value="del"><input type="hidden" name="id" value="<?= $r['id'] ?>"><button class="r" style="background:#fff;color:#b00020">Hapus</button></form></td></tr>
<?php endforeach; if (!$rows) echo '<tr><td colspan="' . (count($F) + 1) . '">Belum ada data</td></tr>'; ?></table></div>
<?php foot();
