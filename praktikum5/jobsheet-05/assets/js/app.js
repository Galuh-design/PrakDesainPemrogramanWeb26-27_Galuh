
function initNavToggle() {
    const toggleBtn = document.getElementById("nav-toggle-btn");
    const nav = document.querySelector("header nav");
    if (!toggleBtn || !nav) return;

    nav.classList.add("transition");
    toggleBtn.addEventListener("click", function () {
        nav.classList.toggle("nav-open");
    });
}

function initHapusConfirm() {
    document.querySelectorAll(".btn-hapus").forEach(function (btn) {
        btn.addEventListener("click", function () {
            const row = btn.closest("tr");
            const nama = row ? row.querySelector("td")?.textContent : "data ini";
            const yakin = confirm("Yakin ingin menghapus \"" + nama + "\"?");
            if (yakin && row) {
                row.remove();
                updateCounter();
            }
        });
    });
}

function updateCounter() {
    const countElement = document.getElementById("jumlah-buku");
    const table = document.querySelector(".table-responsive table");
    if (!countElement || !table) return;

    const allRows = table.querySelectorAll("tbody tr");
    const totalBuku = allRows.length;


    let visibleCount = 0;
    allRows.forEach(function (row) {
        if (row.style.display !== "none") {
            visibleCount++;
        }
    });

    countElement.textContent = `Menampilkan ${visibleCount} dari ${totalBuku} buku`;
}

function initTableFilter() {
    const input = document.getElementById("search-input");
    const table = document.querySelector(".table-responsive table");
    if (!input || !table) return;

    input.addEventListener("keyup", function () {
        const keyword = input.value.toLowerCase();
        const rows = table.querySelectorAll("tbody tr");

        rows.forEach(function (row) {
            const teks = row.querySelector("td:nth-child(1)").textContent.toLowerCase();
            row.style.display = teks.includes(keyword) ? "" : "none";
        });
        updateCounter();
    });
}

function tampilkanError(input, pesan) {
    hapusError(input);
    const span = document.createElement("span");
    span.className = "error";
    span.textContent = pesan;
    input.insertAdjacentElement("afterend", span);
}

function hapusError(input) {
    const next = input.nextElementSibling;
    if (next && next.classList.contains("error")) {
        next.remove();
    }
}

function initValidasiForm() {
    const form = document.getElementById("form-tambah");
    if (!form) return;

    form.addEventListener("submit", function (e) {
        let valid = true;
        //array
        const aturanValidasi = [
            {
                //judul
                selector: "[name='judul'], [name='nama']",
                validate: (val) => val.trim() !== "",
                pesan: "Field ini wajib diisi."
            },
            { //pengarang
                selector: "[name='pengarang']",
                validate: (val) => val.trim() !== "",
                pesan: "Pengarang wajib diisi."
            },
            { //tahun
                selector: "[name='tahun']",
                validate: (val) => {
                    const nilai = parseInt(val, 10);
                    return !isNaN(nilai) && nilai >= 1900 && nilai <= 2026;
                },
                pesan: "Tahun harus di antara 1900-2026."
            },
            {//isbn
                selector: "[id='isbn']",
                validate: (val) => val.trim() !== "" && !isNaN(val.trim()),
                pesan: "ISBN hanya boleh angka dan tanda hubung."
            },
            {//stok
                selector: "[name='stok']",
                validate: (val) => {
                    const nilai = parseInt(val, 10);
                    return !isNaN(nilai) && nilai >= 0;
                },
                pesan: "Stok tidak boleh negatif."
            }
        ];


        aturanValidasi.forEach(function (item) {
            const cek = form.querySelector(item.selector);
            if (cek) {
                if (!item.validate(cek.value)) {
                    tampilkanError(cek, item.pesan);
                    valid = false;
                } else {
                    hapusError(cek);
                }
            }
        });

        if (!valid) {
            e.preventDefault();
        }
    });
}

document.addEventListener("DOMContentLoaded", function () {
    initNavToggle();
    initHapusConfirm();
    initTableFilter();
    initValidasiForm();
    updateCounter();
});