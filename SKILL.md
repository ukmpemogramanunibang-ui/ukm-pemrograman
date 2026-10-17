name: AcademicPaperDrafter
description: Membuat draf makalah akademik lengkap, mulai dari pencarian jurnal hingga penyusunan struktur.
version: 2.0.0
author: Autopilot System
inputs:
  - topic: Topik utama makalah (contoh: "Dampak AI pada Pendidikan")
  - paper_count: Jumlah jurnal yang ingin dicari (default: 3)
allowedTools:
  - HTTP Request
  - Code
  - Google Sheets
  - Gmail


## Tujuan
Skill ini memandu agen untuk membuat kerangka dan draf awal sebuah makalah akademik berdasarkan topik yang diberikan pengguna, dengan standar penulisan ilmiah.

## Langkah-langkah
1.  Analisis Topik: Terima `topic` dari pengguna. Jika topik terlalu luas, minta klarifikasi untuk mempersempit cakupan.
2.  Pencarian Jurnal: Gunakan tool `HTTP Request` untuk mencari jurnal di API Crossref.
    *   URL: `https://api.crossref.org/works?query={topic}&rows={paper_count}&sort=relevance`
    *   Metode: GET
    *   Ekstraksi: Dari respons JSON, ambil `title`, `author`, `published-print.date-parts`, dan `URL`.
3.  Penyusunan Kerangka: Gunakan tool `Code` untuk membuat kerangka makalah.
    *   Input: Daftar judul dan penulis jurnal dari langkah 2.
    *   Logika: Susun kerangka dengan bagian: **Pendahuluan, Tinjauan Pustaka, Metodologi, Hasil dan Pembahasan, Kesimpulan**.
    *   Output: Kembalikan kerangka dalam format JSON.
4.  Pembuatan Draf: Gunakan tool `Code` untuk menulis draf awal. Setiap bagian kerangka diisi dengan 2-3 paragraf yang relevan berdasarkan judul jurnal.
5.  Penyimpanan & Notifikasi:
    *   Gunakan tool `Google Sheets` untuk menyimpan draf akhir (kolom: Topik, Kerangka, Draf, Tanggal).
    *   Gunakan tool `Gmail` untuk mengirim email berisi tautan ke Google Sheets dan ringkasan draf.
6.  Laporan Akhir: Tampilkan pesan konfirmasi ke pengguna bahwa draf telah disimpan dan email telah dikirim.

## Catatan
- Selalu gunakan bahasa akademik yang formal.
- Jika API Crossref gagal, coba lagi maksimal 2 kali.
- Jangan pernah mengirim email tanpa persetujuan pengguna.