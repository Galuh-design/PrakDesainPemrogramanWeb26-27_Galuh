-- psql -U postgres -d simpus_mini -f sql/tugas2_jatuh_tempo.sql

ALTER TABLE peminjaman
    ADD COLUMN IF NOT EXISTS tanggal_jatuh_tempo DATE
    GENERATED ALWAYS AS (tanggal_pinjam + 14) STORED;