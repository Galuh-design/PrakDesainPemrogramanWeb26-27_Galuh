// fungsijenerik.js
async function muatDaftar(url, type) {
    const tbody = document.querySelector(".table-responsive table tbody");
    const loading = document.getElementById("loading-indicator");
    if (!tbody) return;

    loading.style.display = "block";
    tbody.innerHTML = "";

    try {
        await new Promise((resolve) => setTimeout(resolve, 3000));

        const res = await fetch(url);
        if (!res.ok) {
            throw new Error("Gagal mengambil data (status " + res.status + ")");
        }
        const data = await res.json();

        data.forEach(function (item) {
            const tr = document.createElement("tr");

            if (type === "buku") {
                tr.innerHTML =
                    "<td>" + item.judul + "</td>" +
                    "<td>" + item.pengarang + "</td>" +
                    "<td>" + item.tahun + "</td>" +
                    "<td>" + item.stok + "</td>" +
                    "<td>" + item.kategori + "</td>";
            } else if (type === "anggota") {
                tr.innerHTML =
                    "<td>" + item.no_anggota + "</td>" +
                    "<td>" + item.nama + "</td>" +
                    "<td>" + item.alamat + "</td>" +
                    "<td>" + item.no_hp + "</td>";
            }

            tr.innerHTML +=
                "<td>" +
                '<button type="button">Edit</button> ' +
                '<button type="button" class="btn-hapus">Hapus</button>' +
                "</td>";

            tbody.appendChild(tr);
        });
    } catch (err) {
        tbody.innerHTML =
            '<tr><td colspan="5">Gagal memuat data: ' + err.message + "</td></tr>";
    } finally {
        loading.style.display = "none";
    }
}

document.addEventListener("DOMContentLoaded", function () {
    const path = window.location.pathname;

    if (path.includes("anggota")) {
        muatDaftar("../data/anggota.json", "anggota");
    } 
        
    if (path.includes("buku")) {
        muatDaftar("../data/buku.json", "buku");
    }
});