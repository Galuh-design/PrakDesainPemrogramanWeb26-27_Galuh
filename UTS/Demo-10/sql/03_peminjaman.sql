-- Jobsheet: Tabel peminjaman untuk transaksi peminjaman dan pengembalian buku
-- Jalankan setelah tabel buku, anggota, dan users dibuat:
--   psql -d simpus_mini -f sql/peminjaman.sql

CREATE TABLE IF NOT EXISTS peminjaman (
    id SERIAL PRIMARY KEY,
    buku_id INT NOT NULL,
    anggota_id INT NOT NULL,
    user_id INT NOT NULL,
    tanggal_pinjam DATE NOT NULL DEFAULT CURRENT_DATE,
    tanggal_kembali DATE NOT NULL,
    tanggal_dikembalikan DATE DEFAULT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'dipinjam', 
    CONSTRAINT fk_buku FOREIGN KEY (buku_id) REFERENCES buku(id) ON DELETE CASCADE,
    CONSTRAINT fk_anggota FOREIGN KEY (anggota_id) REFERENCES anggota(id) ON DELETE CASCADE,
    CONSTRAINT fk_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);