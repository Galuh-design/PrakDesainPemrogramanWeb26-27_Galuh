<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

require __DIR__ . '/../includes/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['id'] ?? 0);

    if ($id <= 0) {
        $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'ID Transaksi tidak valid!'];
        header('Location: list.php');
        exit;
    }

    try {
        // Mulai transaksi database agar proses konsisten
        $pdo->beginTransaction();

        // 1. Ambil data peminjaman dan pastikan statusnya masih 'dipinjam'
        $stmtCek = $pdo->prepare("SELECT * FROM peminjaman WHERE id = :id FOR UPDATE");
        $stmtCek->execute(['id' => $id]);
        $peminjaman = $stmtCek->fetch(PDO::FETCH_ASSOC);

        if (!$peminjaman) {
            throw new Exception('Data transaksi peminjaman tidak ditemukan.');
        }

        if ($peminjaman['status'] === 'dikembalikan') {
            throw new Exception('Buku ini sudah pernah dikembalikan sebelumnya.');
        }

        $buku_id = $peminjaman['buku_id'];

        // 2. Perbarui status peminjaman menjadi 'dikembalikan' dan isi tanggal_dikembalikan dengan tanggal hari ini
        $sqlUpdatePeminjaman = "UPDATE peminjaman 
                                SET status = 'dikembalikan', tanggal_dikembalikan = CURRENT_DATE 
                                WHERE id = :id";
        $stmtUpdatePeminjman = $pdo->prepare($sqlUpdatePeminjaman);
        $stmtUpdatePeminjman->execute(['id' => $id]);

        // 3. Kembalikan/tambah stok buku sebanyak 1
        $sqlUpdateStok = "UPDATE buku SET stok = stok + 1 WHERE id = :id";
        $stmtUpdateStok = $pdo->prepare($sqlUpdateStok);
        $stmtUpdateStok->execute(['id' => $buku_id]);

        $pdo->commit();

        $_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Buku berhasil dikembalikan dan stok telah diperbarui.'];
        header('Location: list.php');
        exit;

    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Gagal memproses pengembalian: ' . $e->getMessage()];
        header('Location: list.php');
        exit;
    }
} else {
    header('Location: list.php');
    exit;
}