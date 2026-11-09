# API Admin Toko

API untuk membuat dan mengelola produk toko secara otomatis, termasuk mengunggah file produk digital dan kunci lisensi.

- Base URL: `https://<domain>/api`
- Semua respons berformat JSON. Error validasi dikembalikan dengan status `422` dan objek `errors`.
- Hanya akun **super admin** yang bisa memakai API ini.

---

## 1. Autentikasi

Login untuk mendapatkan token. Token yang sama berlaku untuk API blog dan API toko.

```bash
curl -X POST https://<domain>/api/auth/login \
  -H "Accept: application/json" \
  -d "email=admin@contoh.com" -d "password=rahasia"
```

```json
{ "message": "Login berhasil.", "token": "12|AbCdEf...", "user": { "role": "super_admin" } }
```

Kirim token di setiap request berikutnya:

```
Authorization: Bearer 12|AbCdEf...
```

Hal penting soal token dan akses:

- **Satu token aktif per akun.** Login ulang mencabut semua token lama milik akun itu, termasuk token yang dipakai integrasi blog. Kalau beberapa sistem memakai API, pakai satu token bersama atau buat akun admin terpisah.
- **Batas percobaan login:** maksimal 10 kali per menit.
- **Token tetap mengikuti peran akun.** Kalau akun tidak lagi berperan super admin, tokennya otomatis ditolak (`403`).
- **Logout:** `POST /api/auth/logout` mencabut token yang sedang dipakai.

---

## 2. Kategori

| Method | Endpoint | Keterangan |
|---|---|---|
| GET | `/admin/shop/categories` | Daftar kategori |
| POST | `/admin/shop/categories` | Buat kategori |

Field yang bisa dikirim saat membuat kategori:

- `name_id`: wajib
- `name_en`
- `slug`: kalau kosong, dibuat otomatis dari nama
- `description_id`, `description_en`
- `sort_order`
- `is_active`: default `true`

---

## 3. Produk

| Method | Endpoint | Keterangan |
|---|---|---|
| GET | `/admin/shop/products` | Daftar produk (lihat filter di bawah) |
| GET | `/admin/shop/products/{id}` | Detail produk |
| POST | `/admin/shop/products` | Buat produk; boleh sekalian mengunggah gambar, file, dan kunci |
| PATCH | `/admin/shop/products/{id}` | Ubah sebagian field |
| DELETE | `/admin/shop/products/{id}` | Hapus dari toko (riwayat pesanan dan akses pembeli tetap ada) |

Filter untuk daftar produk:

- `search`: cari berdasarkan nama
- `type=digital|physical`
- `active=1|0`
- `per_page`: maksimal 100

### Field produk

| Field | Keterangan |
|---|---|
| `type` | **Wajib.** `digital` atau `physical` |
| `name_id` | **Wajib.** Nama produk (Indonesia) |
| `name_en` | Nama produk (Inggris) |
| `slug` | URL produk; kalau kosong, dibuat otomatis dari nama |
| `product_category_id` | ID kategori |
| `short_description_id`, `short_description_en` | Maks. 500 karakter |
| `description_id`, `description_en` | HTML |
| `license_terms_id`, `license_terms_en` | Ketentuan lisensi |
| `digital_delivery` | **Wajib untuk produk digital.** `file`, `license_key`, atau `link` |
| `access_url` | **Wajib jika `digital_delivery=link`.** Harus http/https. Disimpan terenkripsi dan tidak ditampilkan di respons |
| `max_downloads` | Batas unduhan; default 5 |
| `unlimited_downloads` | `true` = tanpa batas unduhan |
| `download_expiry_days` | Masa berlaku akses dalam hari; kosong = selamanya |
| `is_active` | Default `false` saat membuat produk; kirim `true` agar langsung tampil |
| `is_featured` | Tampilkan sebagai produk unggulan |
| `sort_order` | Urutan tampil |
| `meta_title`, `meta_description` | SEO |
| `has_variants` | `false` (default) atau `true` |

**Tanpa varian** (`has_variants=false`), harga diatur lewat field ini:

- `price`: **wajib**, dalam rupiah, sudah termasuk PPN
- `compare_at_price`: harga coret
- `sku`
- `stock`: kosong = tidak terbatas
- `weight_grams`: wajib untuk produk fisik

**Dengan varian** (`has_variants=true`):

- `options`: maksimal 3 opsi. Contoh: `[{"name_id":"Lisensi","name_en":"License","values":[{"value_id":"Personal","value_en":"Personal"}]}]`
- `variants`: satu baris per kombinasi.
  - Field per baris: `key`, `price`, `compare_at_price`, `sku`, `stock`, `weight_grams`, `is_active` (default `true`), `id`.
  - `key` adalah nilai `value_id` dari setiap opsi, digabung dengan `|` sesuai urutan opsi. Contoh: `Merah|L`.
  - Isi `id` saat mengubah varian yang sudah ada supaya riwayat pesanan tetap terhubung.

**Lampiran yang bisa dikirim bersama produk:**

- `images[]`: maks. 10 gambar per request, masing-masing hingga 4 MB. Request harus multipart.
- `thumbnail`: gambar yang dijadikan thumbnail (gambar utama dan gambar di kartu produk). Isi dengan ID gambar yang sudah ada, atau `new:<n>` untuk gambar ke-n (mulai dari 0) di `images[]` request yang sama. Kalau tidak diisi, gambar pertama di galeri yang dipakai. Respons menyertakan `thumbnail_id`.
- `digital_files[]`: maks. 10 file, masing-masing hingga 500 MB. Request harus multipart. File ini berlaku untuk semua varian.
- `license_keys`: teks, satu kunci per baris. Duplikat dilewati.

Tipe file yang diizinkan: zip, rar, 7z, pdf, epub, mobi, psd, ai, fig, sketch, xd, png, jpg, jpeg, mp4, mp3, wav, txt, csv, xlsx, docx, pptx, json.

> Ukuran file juga dibatasi konfigurasi server (`upload_max_filesize`, `post_max_size`, dan batas body di Nginx/Apache). Untuk file besar, sesuaikan konfigurasi server.

### Contoh: produk digital + file dalam satu request

```bash
curl -X POST https://<domain>/api/admin/shop/products \
  -H "Authorization: Bearer $TOKEN" -H "Accept: application/json" \
  -F type=digital \
  -F name_id="Template Landing Page" \
  -F name_en="Landing Page Template" \
  -F short_description_id="Landing page siap pakai untuk UMKM." \
  -F digital_delivery=file \
  -F price=199000 \
  -F is_active=1 \
  -F "images[]=@foto-1.jpg" \
  -F "images[]=@foto-2.jpg" \
  -F thumbnail=new:1 \
  -F "digital_files[]=@template.zip" \
  -F "digital_files[]=@panduan.pdf"
```

Respons `201`:

```json
{
  "data": {
    "id": "9d1c…",
    "slug": "landing-page-template",
    "price": { "min": 199000, "max": 199000 },
    "files": [{ "id": "…", "name": "template.zip", "size": 307200 }]
  },
  "meta": {
    "message": "Produk dibuat.",
    "attached": { "files": 2, "keys_added": 0, "keys_skipped": 0 }
  }
}
```

### Contoh: produk berlisensi dengan varian (JSON)

```bash
curl -X POST https://<domain>/api/admin/shop/products \
  -H "Authorization: Bearer $TOKEN" -H "Accept: application/json" -H "Content-Type: application/json" \
  -d '{
    "type": "digital",
    "name_id": "Plugin Kasir",
    "digital_delivery": "license_key",
    "is_active": true,
    "has_variants": true,
    "options": [{ "name_id": "Lisensi", "values": [{ "value_id": "1 Domain" }, { "value_id": "5 Domain" }] }],
    "variants": [
      { "key": "1 Domain", "price": 250000, "sku": "KSR-1" },
      { "key": "5 Domain", "price": 900000, "sku": "KSR-5" }
    ],
    "license_keys": "KSR-AAAA-1111\nKSR-BBBB-2222"
  }'
```

### Mengubah produk

`PATCH` hanya mengubah field yang dikirim; field lain tetap. Kalau field `variants` dikirim, seluruh daftar varian diganti, jadi sertakan `id` untuk varian yang dipertahankan.

```bash
curl -X PATCH https://<domain>/api/admin/shop/products/$ID \
  -H "Authorization: Bearer $TOKEN" -H "Content-Type: application/json" \
  -d '{"is_featured": true, "price": 179000}'
```

Untuk ikut mengunggah file saat mengubah produk, kirim multipart dengan `POST` dan tambahkan `-F _method=PATCH`.

---

## 4. File produk digital

| Method | Endpoint | Keterangan |
|---|---|---|
| POST | `/admin/shop/products/{id}/files` | Unggah satu file (multipart) |
| DELETE | `/admin/shop/products/{id}/files/{fileId}` | Hapus file |

Field untuk unggah file:

- `file`: wajib
- `variant_id`: kosong = file untuk semua varian
- `version_label`: misalnya `v1.2`

```bash
curl -X POST https://<domain>/api/admin/shop/products/$ID/files \
  -H "Authorization: Bearer $TOKEN" -H "Accept: application/json" \
  -F "file=@bonus-pro.zip" -F variant_id=$VARIANT_ID -F version_label=v1.2
```

File disimpan di penyimpanan privat. Pembeli hanya bisa mengunduhnya lewat link sementara dari halaman "Produk Digital Saya".

---

## 5. Kunci lisensi

| Method | Endpoint | Keterangan |
|---|---|---|
| POST | `/admin/shop/products/{id}/keys` | Tambah kunci (`keys`: array string, maks. 5000; `variant_id` opsional) |
| DELETE | `/admin/shop/products/{id}/keys/{keyId}` | Hapus kunci yang belum terpakai |

```bash
curl -X POST https://<domain>/api/admin/shop/products/$ID/keys \
  -H "Authorization: Bearer $TOKEN" -H "Content-Type: application/json" \
  -d '{"keys": ["KSR-CCCC-3333", "KSR-DDDD-4444"]}'
```

Respons berisi jumlah kunci yang ditambahkan, yang dilewati karena sudah ada, dan yang tersedia. Isi kunci tidak pernah dikembalikan oleh API.

---

## 6. Kode error

| Status | Arti |
|---|---|
| 401 | Token tidak ada atau tidak valid |
| 403 | Token tanpa akses toko, atau akun bukan super admin |
| 404 | Produk, file, atau kunci tidak ditemukan |
| 422 | Validasi gagal (lihat `errors`) atau aturan toko dilanggar (lihat `message`), misalnya kombinasi varian tidak cocok dengan opsi |
| 429 | Terlalu banyak percobaan login |
