<?php
session_start();
require __DIR__ . '/../includes/koneksi.php';
$path_file_buku = __DIR__ . '/../data/buku.json';
$path_file_anggota = __DIR__ . '/../data/anggota.json';

if (file_exists($path_file_buku)) {
    $data_buku = json_decode(file_get_contents($path_file_buku), true);
    foreach ($data_buku as $buku) {
        $stmt = $pdo->prepare("INSERT INTO buku (judul, pengarang, tahun, stok , kategori) VALUES (:judul, :pengarang, :tahun, :stok , :kategori)");
        $stmt->execute([
            'judul' => $buku['judul'],
            'pengarang' => $buku['pengarang'],
            'tahun' => $buku['tahun'],
            'stok' => $buku['stok'],
            'kategori' => $buku['kategori'],
        ]);
    }
}


if (file_exists($path_file_anggota)) {
    $data_anggota = json_decode(file_get_contents($path_file_anggota), true);
    foreach ($data_anggota as $anggota) {
        $stmt = $pdo->prepare("INSERT INTO anggota (nama, no_anggota, alamat, no_hp) VALUES (:nama, :no_anggota, :alamat, :no_hp)");
        $stmt->execute([
            'nama' => $anggota['nama'],
            'no_anggota' => $anggota['no_anggota'],
            'alamat' => $anggota['alamat'],
            'no_hp' => $anggota['no_hp'],
        ]);
    }
}