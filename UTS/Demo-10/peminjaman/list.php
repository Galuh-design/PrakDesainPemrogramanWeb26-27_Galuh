<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

$page_title = "Daftar Peminjaman Buku";
include __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/koneksi.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$perPage = 5;
$page = max(1, (int) ($_GET['page'] ?? 1));
$offset = ($page - 1) * $perPage;
$keyword = trim($_GET['q'] ?? '');

// Query untuk pencarian berdasarkan nama anggota atau judul buku
if ($keyword !== '') {
    $sqlCount = "SELECT COUNT(*) FROM peminjaman p 
                 JOIN buku b ON p.buku_id = b.id 
                 JOIN anggota a ON p.anggota_id = a.id 
                 WHERE b.judul ILIKE :kw OR a.nama ILIKE :kw";
    $hitung = $pdo->prepare($sqlCount);
    $hitung->execute(['kw' => '%' . $keyword . '%']);
    $totalRows = $hitung->fetchColumn();

    $sqlData = "SELECT p.*, b.judul AS judul_buku, a.nama AS nama_anggota, u.nama AS nama_petugas 
                FROM peminjaman p 
                JOIN buku b ON p.buku_id = b.id 
                JOIN anggota a ON p.anggota_id = a.id 
                JOIN users u ON p.user_id = u.id 
                WHERE b.judul ILIKE :kw OR a.nama ILIKE :kw 
                ORDER BY p.id DESC LIMIT :limit OFFSET :offset";
    $stmt = $pdo->prepare($sqlData);
    $stmt->bindValue('kw', '%' . $keyword . '%');
} else {
    $totalRows = $pdo->query("SELECT COUNT(*) FROM peminjaman")->fetchColumn();
    
    $sqlData = "SELECT p.*, b.judul AS judul_buku, a.nama AS nama_anggota, u.nama AS nama_petugas 
                FROM peminjaman p 
                JOIN buku b ON p.buku_id = b.id 
                JOIN anggota a ON p.anggota_id = a.id 
                JOIN users u ON p.user_id = u.id 
                ORDER BY p.id DESC LIMIT :limit OFFSET :offset";
    $stmt = $pdo->prepare($sqlData);
}

$stmt->bindValue('limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue('offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$daftarPeminjaman = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalPages = max(1, (int) ceil($totalRows / $perPage));
?>
        <section>
            <h2 style="text-align: center;">Daftar Peminjaman Buku</h2>

            <?php if ($flash): ?>
                <p class="flash flash-<?php echo $flash['type']; ?>"><?php echo $flash['pesan']; ?></p>
            <?php endif; ?>

            <div class="search-box">
                <form method="get" action="list.php">
                    <span>
                        <label for="search-input">Cari Transaksi</label><br>
                        <input type="text" id="search-input" name="q" value="<?php echo htmlspecialchars($keyword); ?>" placeholder="Cari judul buku atau nama anggota...">
                    </span>
                    <button type="submit">Cari</button>
                </form>
            </div>

            <p style="margin-bottom: 1rem; font-weight: 500;">
                <a href="tambah.php" class="btn-tambah">+ Tambah Peminjaman Baru</a>
            </p>

            <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Judul Buku</th>
                        <th>Nama Anggota</th>
                        <th>Petugas</th>
                        <th>Tgl Pinjam</th>
                        <th>Tgl Harus Kembali</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($daftarPeminjaman)): ?>
                    <tr>
                        <td colspan="8">Tidak ada data peminjaman yang ditemukan.</td>
                    </tr>
                    <?php else: ?>
                        <?php $no = $offset + 1; foreach ($daftarPeminjaman as $row): ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo htmlspecialchars($row['judul_buku']); ?></td>
                            <td><?php echo htmlspecialchars($row['nama_anggota']); ?></td>
                            <td><?php echo htmlspecialchars($row['nama_petugas']); ?></td>
                            <td><?php echo htmlspecialchars($row['tanggal_pinjam']); ?></td>
                            <td><?php echo htmlspecialchars($row['tanggal_kembali']); ?></td>
                            <td>
                                <?php if ($row['status'] === 'dipinjam'): ?>
                                    <span class="badge badge-warning" style="color: #f29202ff; font-weight: bold;">Dipinjam</span>
                                <?php else: ?>
                                    <span class="badge badge-success" style="color: #00d315; font-weight: bold;">Dikembalikan</span>
                                    <br><small>(<?php echo $row['tanggal_dikembalikan']; ?>)</small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($row['status'] === 'dipinjam'): ?>
                                    <form method="post" action="proses_kembali.php" style="display:inline;">
                                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                        <button type="submit" class="btn-edit" onclick="return confirm('Proses pengembalian buku ini?');">Kembalikan</button>
                                    </form>
                                <?php else: ?>
                                    <span style="font-size: 0.9em;">Selesai</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            </div>

            <nav class="pagination">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="list.php?page=<?php echo $i; ?><?php echo $keyword !== '' ? '&q=' . urlencode($keyword) : ''; ?>"
                   class="<?php echo $i === $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
                <?php endfor; ?>
            </nav>
        </section>
<?php include __DIR__ . '/../asset_freaky/maskot.php' ?>
<?php include __DIR__ . '/../includes/footer.php'; ?>