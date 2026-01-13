<?php include "app/views/layout/header.php"; ?>

<h4>Data Produk</h4>

<form method="GET">
<input type="text" name="search" class="form-control w-25 mb-2" placeholder="Cari kopi...">
</form>

<a href="product/create" class="btn btn-success mb-2">+ Tambah Produk</a>

<table class="table table-striped">
<tr>
<th>Nama</th><th>Harga</th><th>Kategori</th><th>Aksi</th>
</tr>

<?php while($p = $data->fetch_assoc()): ?>
<tr>
<td><?= $p['name'] ?></td>
<td>Rp <?= number_format($p['price']) ?></td>
<td><?= $p['category'] ?></td>
<td>
<a href="product/delete?id=<?= $p['id'] ?>" class="btn btn-sm btn-danger">Hapus</a>
</td>
</tr>
<?php endwhile; ?>
</table>

<?php include "app/views/layout/footer.php"; ?>
