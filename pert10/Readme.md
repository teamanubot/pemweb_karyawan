# Langkah Langkah Membuat Migrations, Seeders, Models

#### Note : Lakukan perintah tanpa tanda kutip "



## Apa itu Migrations?

Migrasi atau Migrations adalah mekanisme kontrol versi (version control) untuk skema basis data. Komponen ini memungkinan pengembangan perangkat lunak untuk merancang, memodifikasi, dan mendistribusikan struktur tabel basis data secara terprogram menggunakan bahasa pemrograman PHP.

* **Membuat Migrasi**

```Shell
php artisan make:migration Nama_Tabel
```

* **Mengeksekusi Migrasi**

```Shell
php artisan migrate
```

* **Membatalkan Migrasi (Rollback)**

```Shell
php artisan migrate:rollback
```

* **Mereset dan Mengeksekusi Ulang**

```Shell
php artisan migrate:fresh
```



## Apa itu Seeders?

Seeders atau Penyemai adalah sebuah kelas (class) yang berfungsi untuk mengotomatisasi proses populasi atau penyisipan data awal ke dalam tabel-tabel basis data, Komponen ini sangat esensial pada fase perancangan dan pengujian sistem (pembuatan data dummy).

* **Membuat Seeder**

```Shell
php artisan make:seeder Nama_Seeder
```

* **Mengeksekusi Seluruh Seeders**

```Shell
php artisan db:seed
```

- **Mengeksekusi Seeders secara Spesifik**

```Shell
php artisan db:seed --class=Nama_Seeder
```


## Apa itu Models?

Models atau model merupakan representasi logis dan struktural dari sebuah tabel di dalam basis data. Di dalam ekosistem Laravel, implementasi model difasilitasi oleh sebuah Object-Relational Mapping (ORM) yang disebut Eloquent (Abstraksi Data, Manipulasi Data  "CRUD", dan Relasi Entitas).

- **Membuat Model**

```
php artisan make:model Nama_Model
```


## Perintah Komprehensif (Integrasi)

Laravel menyediakan preintah terintegrasi yang mampu menciptakan Model, Migration, Seeder (bahkan Controller) secara bersamaan dalam satu baris instruksi tunggal.

- **Pembuatan Terpadu**

```Shell
php artisan make:model Nama_Model -m -s
```

 **Atau**

```
php artisan make:model Nama_Model --ms
```

**Deskripsi : Penambahan Argumen (flags) pada perintah pembuatan model memikil makna spesifik sebagai berikut**

* `-m` (atau `--migration`) : Menginstruksikan pembuatan Migration yang berasosiasi dengan model tersebut.
* `-s` (atau `--seed`) : Menginstruksikan pembuatan Seeder yang berasosiasi dengan model tersebut.

***Note : Jika menggunakan flag `-a` atau `--all`, sistem akan membuatkan Model, Migrasi, Seeder, Controller, dan Factory sekaligus.**
