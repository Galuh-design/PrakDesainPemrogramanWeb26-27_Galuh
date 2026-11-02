<?php
$page_title = "Beranda";
include __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/koneksi.php';

$totalBuku = $pdo->query("SELECT COUNT(*) FROM buku")->fetchColumn();
$totalAnggota = $pdo->query("SELECT COUNT(*) FROM anggota")->fetchColumn();
$totalPinjam = $pdo->query("SELECT COUNT(*) FROM peminjaman WHERE status = 'dipinjam'")->fetchColumn();
?>
<section>
    <?php if ($sudahLogin): ?>
        <h2>Welcome! <span><?php echo $_SESSION['nama']; ?></span></h2>
    <?php else: ?>
        <h2>Welcome! Guest</h2>
    <?php endif; ?>
    <p>Aplikasi sederhana untuk mengelola data buku dan anggota perpustakaan.</p>
    <a href="/buku/list.php" class="a-index">Lihat Daftar Buku</a>
</section>

<section>
    <article>
        <h3>Total Buku</h3>
        <p><?php echo $totalBuku; ?></p>
    </article>
    <article>
        <h3>Total Anggota</h3>
        <p><?php echo $totalAnggota; ?></p>
    </article>
    <article>
        <h3>Sedang Dipinjam</h3>
        <p><?php echo $totalPinjam ?></p>
    </article>
</section>
<?php include __DIR__ . '/asset_freaky/maskot.php' ?>
<?php include __DIR__ . '/includes/footer.php'; ?>