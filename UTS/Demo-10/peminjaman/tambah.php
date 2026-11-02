<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

$page_title = "Tambah Peminjaman Buku";
include __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/koneksi.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

// Ambil daftar anggota yang aktif untuk pilihan select
$stmtAnggota = $pdo->query("SELECT id, nama, no_anggota FROM anggota ORDER BY nama ASC");
$daftarAnggota = $stmtAnggota->fetchAll(PDO::FETCH_ASSOC);

// Ambil daftar buku yang stoknya masih > 0
$stmtBuku = $pdo->query("SELECT id, judul, pengarang, stok FROM buku WHERE stok > 0 ORDER BY judul ASC");
$daftarBuku = $stmtBuku->fetchAll(PDO::FETCH_ASSOC);
?>
<section>
    <h2 style="text-align: center;">Tambah Transaksi Peminjaman</h2>

    <?php if ($flash): ?>
        <p class="flash flash-<?php echo $flash['type']; ?>"><?php echo $flash['pesan']; ?></p>
    <?php endif; ?>

    <form method="post" action="proses_tambah.php">
        <p>
            <label for="anggota_id">Pilih Anggota Peminjam</label><br>
            <select id="anggota_id" name="anggota_id" required style="width: 100%; padding: 8px; margin-top: 5px;">
                <option value="">-- Pilih Anggota --</option>
                <?php foreach ($daftarAnggota as $anggota): ?>
                    <option value="<?php echo $anggota['id']; ?>">
                        <?php echo htmlspecialchars($anggota['nama'] . ' (' . $anggota['no_anggota'] . ')'); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </p>

        <p>
            <label for="buku_id">Pilih Buku (Stok Tersedia)</label><br>
            <select id="buku_id" name="buku_id" required style="width: 100%; padding: 8px; margin-top: 5px;">
                <option value="">-- Pilih Buku --</option>
                <?php foreach ($daftarBuku as $buku): ?>
                    <option value="<?php echo $buku['id']; ?>">
                        <?php echo htmlspecialchars($buku['judul'] . ' - ' . $buku['pengarang'] . ' (Stok: ' . $buku['stok'] . ')'); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </p>

        <p>
            <label for="tanggal_kembali">Batas Tanggal Pengembalian</label><br>
            <input type="date" id="tanggal_kembali" name="tanggal_kembali" required
                style="width: 100%; padding: 8px; margin-top: 5px;"
                value="<?php echo date('Y-m-d', strtotime('+7 days')); ?>">
        </p>

        <p style="margin-top: 20px;">
            <button type="submit" class="btn-tambah">Simpan Peminjaman</button>
            <a href="list.php" style="margin-left: 10px; text-decoration: none; color: #555;">Batal / Kembali</a>
        </p>
    </form>
</section>
<?php include __DIR__ . '/../asset_freaky/maskot.php' ?>
<?php include __DIR__ . '/../includes/footer.php'; ?>