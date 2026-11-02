document.addEventListener("DOMContentLoaded", function() {
    const modal = document.getElementById('customDeleteModal');
    const messageEl = document.getElementById('customDeleteMessage');
    const btnYa = document.getElementById('btnModalYa');
    const btnBatal = document.getElementById('btnModalBatal');
    
    let targetForm = null;

    // Menangkap event submit dari form ber-class 'form-hapus'
    document.addEventListener("submit", function(e) {
        if (!e.target.classList.contains("form-hapus")) return;
        
        e.preventDefault();
        targetForm = e.target;

        // Mengambil teks dari kolom pertama (td) pada baris tabel terkait
        const row = targetForm.closest("tr");
        const nama = row ? row.querySelector("td").textContent : "data ini";

        // Menampilkan nama item secara dinamis ke dalam teks modal
        messageEl.textContent = `Yakin ingin menghapus "${nama}"?`;
        modal.style.display = 'flex';
    });

    // Jika pengguna menekan tombol "Ya, Hapus"
    if (btnYa) {
        btnYa.onclick = function() {
            if (targetForm) {
                HTMLFormElement.prototype.submit.call(targetForm);
            }
        };
    }

    // Jika pengguna menekan tombol "Batal"
    if (btnBatal) {
        btnBatal.onclick = function() {
            modal.style.display = 'none';
            targetForm = null;
        };
    }

    // Tutup modal jika area luar kotak (overlay) diklik
    if (modal) {
        modal.onclick = function(e) {
            if (e.target === modal) {
                modal.style.display = 'none';
                targetForm = null;
            }
        };
    }
});