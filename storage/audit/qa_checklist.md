## QA Multi-Organization

### Skenario A — UNICODE Only (baseline)
- [ ] Login Owner → dashboard OK
- [ ] Login Ketua UNICODE → semua menu: dashboard, anggota, presensi, keuangan, LMS, game
- [ ] Login Pengurus → semua menu pengurus
- [ ] Login Anggota → semua menu anggota
- [ ] Login Anggota Umum → dashboard yang benar
- [ ] QR scan user anggota → valid
- [ ] NFC login → valid
- [ ] Booking ruangan → valid

### Skenario B — Multi Org
- [ ] User A sebagai Ketua UNICODE + Anggota BEM
- [ ] User A buka pemilih organisasi → 2 kartu muncul
- [ ] User A pilih BEM → hanya lihat data BEM
- [ ] User A pilih UNICODE → hanya lihat data UNICODE
- [ ] Ketua BEM buka keuangan BEM → OK
- [ ] Ketua BEM buka keuangan UNICODE → FORBIDDEN (403)
- [ ] Data absensi BEM tidak muncul di UNICODE
- [ ] Data keuangan UNICODE tidak muncul di BEM