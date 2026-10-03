# Project API Fonnte

Aplikasi registrasi sederhana menggunakan **PHP Native, MySQL, dan Fonnte API**.

Project ini digunakan untuk melakukan registrasi user dan mengirimkan notifikasi WhatsApp secara otomatis setelah registrasi berhasil.

---

## Fitur

- Form registrasi user
- Input nama
- Input email
- Input nomor WhatsApp
- Input alamat
- Input password
- Validasi data
- Validasi email
- Validasi password minimal 6 karakter
- Cek email yang sudah terdaftar
- Cek nomor WhatsApp yang sudah terdaftar
- Password menggunakan hash
- Penyimpanan data ke MySQL
- Pengiriman notifikasi WhatsApp menggunakan Fonnte API
- API untuk mengirim WhatsApp
- Menampilkan status pengiriman WhatsApp

---

## Struktur Project

```text
project-api-fonnte/
│
├── config/
│   ├── database.php
│   └── fonnte.php
│
├── api/
│   ├── register.php
│   └── whatsapp.php
│
├── functions/
│   └── fonnte.php
│
├── index.html
│
└── README.md
