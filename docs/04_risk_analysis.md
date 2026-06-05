# Risk Analysis

| Threat | Vulnerability | Impact | Likelihood | Mitigation |
|---|---|---|---|---|
| SQL Injection | Raw SQL tanpa binding | Data bocor/rusak | Medium | Query builder dan parameter binding |
| XSS | Output HTML tidak di-escape | Session/user data dicuri | Medium | Blade escaped output dan validasi |
| CSRF | Form tanpa token | Transaksi palsu | Medium | CSRF middleware Laravel |
| Weak password | Password pendek | Akun diambil alih | Medium | Minimal 8 karakter dan hashing |
| Broken access control | Role tidak dicek | Data admin bocor | High | Middleware role dan test akses |
| File upload berbahaya | Ekstensi bebas | RCE/malware | Medium | Validasi image/mimes/max size dan Storage |
| Data loss | Migrasi/cutover tanpa backup | Operasi berhenti | Medium | Backup dan rollback plan |
| Stock manipulation | Stok dikurangi di luar service | Selisih stok | Medium | StockService FIFO dan audit movement |
| Audit log tampering | Log bisa dihapus bebas | Investigasi gagal | Low | Batasi akses admin dan backup log |
| Downtime | Queue/db/server mati | Layanan berhenti | Medium | Monitoring, manual fallback, rollback |
