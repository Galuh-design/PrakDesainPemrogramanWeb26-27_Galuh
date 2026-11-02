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
    $buku_id = (int) ($_POST['buku_id'] ?? 0);
    $anggota_id = (int) ($_POST['anggota_id'] ?? 0);
    $user_id = (int) $_SESSION['user_id'];
    $tanggal_kembali = trim($_POST['tanggal_kembali'] ?? '');

    if ($buku_id <= 0 || $anggota_id <= 0 || empty($tanggal_kembali)) {
        $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Semua kolom wajib diisi dengan benar!'];
        header('Location: tambah.php');
        exit;
    }

    try {
        // Mulai transaksi database untuk menjaga konsistensi stok dan data peminjaman
        $pdo->beginTransaction();

        // 1. Cek stok buku saat ini
        $stmtCek = $pdo->prepare("SELECT stok FROM buku WHERE id = :id FOR UPDATE");
        $stmtCek->execute(['id' => $buku_id]);
        $buku = $stmtCek->fetch(PDO::FETCH_ASSOC);

        if (!$buku || $buku['stok'] <= 0) {
            throw new Exception('Buku tidak ditemukan atau stok sudah habis.');
        }

        // 2. Masukkan data ke tabel peminjaman
        $sqlInsert = "INSERT INTO peminjaman (buku_id, anggota_id, user_id, tanggal_pinjam, tanggal_kembali, status) 
                      VALUES (:buku_id, :anggota_id, :user_id, CURRENT_DATE, :tanggal_kembali, 'dipinjam')";
        $stmtInsert = $pdo->prepare($sqlInsert);
        $stmtInsert->execute([
            'buku_id' => $buku_id,
            'anggota_id' => $anggota_id,
            'user_id' => $user_id,
            'tanggal_kembali' => $tanggal_kembali
        ]);

        // 3. Kurangi stok buku sebanyak 1
        $sqlUpdateStok = "UPDATE buku SET stok = stok - 1 WHERE id = :id";
        $stmtUpdate = $pdo->prepare($sqlUpdateStok);
        $stmtUpdate->execute(['id' => $buku_id]);

        $pdo->commit();

        $_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Transaksi peminjaman berhasil ditambahkan dan stok buku diperbarui.'];
        header('Location: list.php');
        exit;

    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Gagal memproses peminjaman: ' . $e->getMessage()];
        header('Location: tambah.php');
        exit;
    }
} else {
    header('Location: tambah.php');
    exit;
}