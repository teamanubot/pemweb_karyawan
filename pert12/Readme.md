# Pertemuan 12 — Laravel Filament: Kerangka Kerja Administrasi Modern

> **Mata Kuliah**: Pemrograman Web  
> **Topik**: Pengenalan dan Implementasi Laravel Filament  
> **Tumpukan Teknologi**: Laravel 13, Filament 5, PHP 8.2, MariaDB, Docker, Nginx

---

## Daftar Isi

1. [Pengantar Filament](#1-pengantar-filament)
2. [Arsitektur dan Komponen Utama](#2-arsitektur-dan-komponen-utama)
3. [Instalasi dan Konfigurasi](#3-instalasi-dan-konfigurasi)
4. [Panel Administrasi](#4-panel-administrasi)
5. [Resource: Komponen Inti CRUD](#5-resource-komponen-inti-crud)
6. [Perintah `php artisan make:filament-resource`](#6-perintah-php-artisan-makefilament-resource)
7. [Form Schema](#7-form-schema)
8. [Table Schema](#8-table-schema)
9. [Infolist Schema](#9-infolist-schema)
10. [Pages (Halaman)](#10-pages-halaman)
11. [Widget](#11-widget)
12. [Plugin dan Ekosistem](#12-plugin-dan-ekosistem)
13. [Struktur Proyek](#13-struktur-proyek)
14. [Menjalankan Proyek dengan Docker](#14-menjalankan-proyek-dengan-docker)
15. [Referensi](#15-referensi)

---

## 1. Pengantar Filament

**Filament** adalah kerangka kerja (*framework*) administrasi berbasis komponen yang dibangun di atas ekosistem Laravel dan Livewire. Filament dirancang untuk mempercepat proses pengembangan antarmuka administrasi (*admin panel*) dengan menyediakan serangkaian komponen siap pakai yang mengikuti praktik terbaik (*best practices*) pengembangan perangkat lunak modern.

Filament pertama kali dirilis pada tahun 2021 dan sejak saat itu telah berkembang menjadi salah satu solusi administrasi paling populer dalam ekosistem PHP. Hingga saat ini, Filament telah mencapai versi 5 (digunakan dalam proyek ini) yang menghadirkan peningkatan signifikan dalam performa, fleksibilitas, dan kemudahan penggunaan.

### 1.1 Landasan Filosofis

Filament dibangun dengan landasan filosofis bahwa sebuah panel administrasi tidak seharusnya memerlukan waktu berhari-hari untuk dikembangkan. Dengan memanfaatkan paradigma *convention over configuration* (konvensi menggantikan konfigurasi) yang diwarisi dari Laravel, Filament memungkinkan pengembang untuk menghasilkan antarmuka CRUD (*Create, Read, Update, Delete*) yang fungsional hanya dalam hitungan menit.

### 1.2 Perbandingan dengan Alternatif

| Aspek | Filament | Nova | Voyager |
|---|---|---|---|
| Lisensi | MIT (Gratis) | Berbayar | MIT (Gratis) |
| Basis Teknologi | Livewire + Alpine.js | Vue.js | jQuery |
| Versi Laravel | 10+ | 9+ | 8+ |
| Kustomisasi | Sangat Tinggi | Tinggi | Sedang |
| Ekosistem Plugin | Sangat Luas | Terbatas | Sedang |

---

## 2. Arsitektur dan Komponen Utama

Filament menganut arsitektur berlapis (*layered architecture*) yang memisahkan tanggung jawab antara lapisan presentasi, logika bisnis, dan akses data.

```
┌─────────────────────────────────────────────┐
│              BROWSER / KLIEN                │
├─────────────────────────────────────────────┤
│         Livewire (Reaktivitas UI)           │
├──────────────┬──────────────────────────────┤
│   Pages      │   Resources                 │
│  (Halaman)   │  (Form / Table / Infolist)  │
├──────────────┴──────────────────────────────┤
│              Filament Core                  │
├─────────────────────────────────────────────┤
│         Laravel Framework (MVC)             │
├─────────────────────────────────────────────┤
│         Eloquent ORM / Database             │
└─────────────────────────────────────────────┘
```

Komponen utama yang menyusun arsitektur Filament adalah sebagai berikut:

- **Panel** — Kontainer utama yang mengelola seluruh konfigurasi administrasi, termasuk autentikasi, navigasi, dan tema.
- **Resource** — Kelas yang menghubungkan Model Eloquent dengan antarmuka CRUD secara otomatis.
- **Pages** — Halaman-halaman khusus yang dapat dikustomisasi di luar siklus CRUD standar.
- **Widgets** — Komponen statistik atau informatif yang dapat ditampilkan pada *dashboard*.
- **Schema** — Definisi struktur formulir (*form*), tabel, dan daftar informasi (*infolist*).

---

## 3. Instalasi dan Konfigurasi

### 3.1 Prasyarat Sistem

Sebelum melakukan instalasi Filament, pastikan sistem memenuhi persyaratan berikut:

| Komponen | Versi Minimum |
|---|---|
| PHP | 8.2 |
| Laravel | 13.x |
| Composer | 2.x |
| Node.js | 18.x |

### 3.2 Instalasi melalui Composer

Filament dipasang sebagai paket Composer. Perintah instalasi dilakukan melalui antarmuka baris perintah (*command-line interface*) sebagai berikut:

```bash
composer require filament/filament:"^5.0"
```

### 3.3 Instalasi Panel Administrasi

Setelah paket inti terpasang, panel administrasi perlu diinisialisasi menggunakan perintah *artisan* berikut:

```bash
php artisan filament:install --panels
```

Perintah tersebut akan melakukan serangkaian tindakan secara otomatis, antara lain:

1. Membuat berkas konfigurasi panel di `app/Providers/Filament/AdminPanelProvider.php`
2. Mendaftarkan *service provider* ke dalam konfigurasi aplikasi
3. Membuat direktori `app/Filament/` sebagai wadah seluruh komponen Filament

### 3.4 Konfigurasi Panel Provider

Berkas `AdminPanelProvider.php` merupakan pusat konfigurasi panel. Di dalamnya, pengembang dapat mendefinisikan berbagai aspek panel, seperti yang ditunjukkan pada contoh berikut:

```php
<?php

namespace App\Providers\Filament;

use Filament\Panel;
use Filament\PanelProvider;
use Filament\Http\Middleware\Authenticate;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')           // URL akses panel: /admin
            ->login()                 // Aktifkan halaman login
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(
                in: app_path('Filament/Admin/Resources'),
                for: 'App\\Filament\\Admin\\Resources'
            )
            ->discoverPages(
                in: app_path('Filament/Admin/Pages'),
                for: 'App\\Filament\\Admin\\Pages'
            )
            ->discoverWidgets(
                in: app_path('Filament/Admin/Widgets'),
                for: 'App\\Filament\\Admin\\Widgets'
            )
            ->middleware([
                EncryptCookies::class,
                Authenticate::class,
            ]);
    }
}
```

### 3.5 Migrasi dan Pembuatan Pengguna

Setelah konfigurasi panel selesai, jalankan migrasi basis data dan buat pengguna pertama:

```bash
# Menjalankan migrasi basis data
php artisan migrate

# Membuat pengguna administrasi pertama
php artisan make:filament-user
```

---

## 4. Panel Administrasi

Panel (*panel*) dalam terminologi Filament merujuk pada sebuah instansi antarmuka administrasi yang berdiri sendiri. Sebuah aplikasi Laravel dapat memiliki lebih dari satu panel secara bersamaan, misalnya panel untuk administrator dan panel terpisah untuk pelanggan.

### 4.1 Multi-Panel

Filament mendukung arsitektur multi-panel, yang berarti pengembang dapat mendefinisikan beberapa panel dengan jalur URL, konfigurasi autentikasi, dan komponen yang berbeda-beda. Setiap panel memiliki berkas *provider* tersendiri.

```bash
# Membuat panel baru
php artisan filament:install --panels
# Sistem akan meminta nama panel, misalnya: "customer"
# Berkas baru akan dibuat di: app/Providers/Filament/CustomerPanelProvider.php
```

### 4.2 Navigasi

Filament secara otomatis membangun navigasi lateral (*sidebar*) berdasarkan *resource* dan halaman yang terdaftar. Urutan dan pengelompokan navigasi dapat dikustomisasi melalui properti statik pada kelas *resource*:

```php
class ProductResource extends Resource
{
    protected static ?string $navigationGroup = 'Manajemen Produk';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationLabel = 'Produk';
}
```

---

## 5. Resource: Komponen Inti CRUD

*Resource* adalah abstraksi utama dalam Filament yang menghubungkan sebuah Model Eloquent dengan operasi-operasi CRUD melalui antarmuka grafis. Setiap *resource* merepresentasikan satu entitas data dalam sistem.

### 5.1 Anatomi Resource

Sebuah kelas *resource* standar memiliki struktur sebagai berikut:

```
app/Filament/Admin/Resources/Products/
├── ProductResource.php        # Kelas resource utama
├── Pages/
│   ├── CreateProduct.php      # Halaman pembuatan data
│   ├── EditProduct.php        # Halaman penyuntingan data
│   ├── ListProducts.php       # Halaman daftar data
│   └── ViewProduct.php        # Halaman detail data
├── Schemas/
│   ├── ProductForm.php        # Definisi formulir
│   └── ProductInfolist.php    # Definisi infolist
└── Tables/
    └── ProductsTable.php      # Definisi tabel
```

Contoh kelas *resource* yang digunakan dalam proyek ini (`ProductResource.php`):

```php
<?php

namespace App\Filament\Admin\Resources\Products;

use App\Models\Product;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Support\Icons\Heroicon;

class ProductResource extends Resource
{
    // Model yang dikelola oleh resource ini
    protected static ?string $model = Product::class;

    // Ikon navigasi pada sidebar
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    // Judul rekaman yang ditampilkan
    protected static ?string $recordTitleAttribute = 'Product';

    // Mendefinisikan skema formulir
    public static function form(Schema $schema): Schema
    {
        return ProductForm::configure($schema);
    }

    // Mendefinisikan tampilan informasi detail
    public static function infolist(Schema $schema): Schema
    {
        return ProductInfolist::configure($schema);
    }

    // Mendefinisikan struktur tabel
    public static function table(Table $table): Table
    {
        return ProductsTable::configure($table);
    }

    // Mendefinisikan halaman-halaman yang tersedia
    public static function getPages(): array
    {
        return [
            'index'  => ListProducts::route('/'),
            'create' => CreateProduct::route('/create'),
            'view'   => ViewProduct::route('/{record}'),
            'edit'   => EditProduct::route('/{record}/edit'),
        ];
    }
}
```

---

## 6. Perintah `php artisan make:filament-resource`

Perintah ini merupakan perintah generator (*generator command*) yang paling fundamental dalam alur kerja pengembangan dengan Filament. Perintah ini mengotomatiskan proses pembuatan seluruh berkas yang diperlukan untuk sebuah *resource* baru.

### 6.1 Sintaksis Dasar

```bash
php artisan make:filament-resource NamaModel
```

Contoh penggunaan untuk membuat *resource* bagi model `Product`:

```bash
php artisan make:filament-resource Product
```

Perintah di atas akan menghasilkan berkas-berkas berikut secara otomatis:

```
app/Filament/Admin/Resources/Products/
├── ProductResource.php
└── Pages/
    ├── CreateProduct.php
    ├── EditProduct.php
    └── ListProducts.php
```

### 6.2 Opsi Perintah `--generate`

Opsi `--generate` (disingkat `-G`) merupakan salah satu opsi paling berguna pada perintah `make:filament-resource`. Opsi ini menginstruksikan generator untuk membaca struktur tabel basis data dari model yang ditentukan, kemudian secara otomatis mengisi definisi kolom pada formulir (*form*) dan tabel yang dihasilkan.

#### Sintaksis

```bash
php artisan make:filament-resource NamaModel --generate
```

atau menggunakan singkatan:

```bash
php artisan make:filament-resource NamaModel -G
```

#### Contoh Penggunaan

Diberikan model `Product` dengan kolom-kolom berikut dalam tabel basis data:

| Kolom | Tipe Data | Keterangan |
|---|---|---|
| `id` | `bigint` | Kunci primer, otomatis |
| `name` | `varchar(255)` | Nama produk |
| `description` | `text` | Deskripsi produk |
| `price` | `decimal(10,2)` | Harga produk |
| `expired_at` | `timestamp` | Tanggal kedaluwarsa |
| `created_at` | `timestamp` | Waktu pembuatan |
| `updated_at` | `timestamp` | Waktu pembaruan |

Jalankan perintah:

```bash
php artisan make:filament-resource Product --generate
```

Filament akan membaca skema tabel `products` dan menghasilkan definisi formulir secara otomatis, kurang lebih seperti berikut:

```php
// Pada berkas ProductForm.php (atau di dalam form() method)
Forms\Components\TextInput::make('name')
    ->required()
    ->maxLength(255),

Forms\Components\Textarea::make('description')
    ->columnSpanFull(),

Forms\Components\TextInput::make('price')
    ->required()
    ->numeric()
    ->prefix('Rp'),

Forms\Components\DateTimePicker::make('expired_at'),
```

Dan pada definisi tabel:

```php
// Pada berkas ProductsTable.php (atau di dalam table() method)
Tables\Columns\TextColumn::make('name')
    ->searchable(),

Tables\Columns\TextColumn::make('description')
    ->limit(50),

Tables\Columns\TextColumn::make('price')
    ->money('IDR')
    ->sortable(),

Tables\Columns\TextColumn::make('expired_at')
    ->dateTime()
    ->sortable(),
```

### 6.3 Daftar Lengkap Opsi Perintah

Perintah `make:filament-resource` memiliki berbagai opsi yang dapat dikombinasikan sesuai kebutuhan:

| Opsi | Singkatan | Fungsi |
|---|---|---|
| `--generate` | `-G` | Mengisi formulir dan tabel berdasarkan skema basis data |
| `--simple` | `-S` | Membuat resource sederhana tanpa halaman View (hanya modal) |
| `--view` | | Menambahkan halaman View terpisah |
| `--soft-deletes` | | Menambahkan dukungan *soft delete* |
| `--panel` | | Menentukan panel target (untuk aplikasi multi-panel) |

#### Contoh Kombinasi Opsi

```bash
# Resource lengkap dengan data generate, soft delete, dan panel tertentu
php artisan make:filament-resource Product --generate --soft-deletes --panel=admin

# Resource sederhana (menggunakan modal, bukan halaman penuh)
php artisan make:filament-resource Product --simple --generate
```

### 6.4 Pemetaan Tipe Kolom ke Komponen Filament

Ketika opsi `--generate` digunakan, Filament melakukan pemetaan (*mapping*) otomatis antara tipe kolom basis data dengan komponen yang sesuai:

| Tipe Kolom BD | Komponen Form | Komponen Tabel |
|---|---|---|
| `string` / `varchar` | `TextInput` | `TextColumn` |
| `text` / `longtext` | `Textarea` | `TextColumn` |
| `integer` / `bigint` | `TextInput` (numeric) | `TextColumn` |
| `decimal` / `float` | `TextInput` (numeric) | `TextColumn` |
| `boolean` | `Toggle` | `IconColumn` |
| `date` | `DatePicker` | `TextColumn` (date) |
| `datetime` / `timestamp` | `DateTimePicker` | `TextColumn` (dateTime) |
| `json` | `KeyValue` | `TextColumn` |
| `enum` | `Select` | `TextColumn` |

### 6.5 Catatan Penting Penggunaan `--generate`

> **Perhatian**: Opsi `--generate` mensyaratkan bahwa tabel basis data yang berkaitan dengan model telah dibuat sebelumnya. Apabila tabel belum ada, perintah akan menghasilkan kesalahan. Pastikan migrasi telah dijalankan (`php artisan migrate`) sebelum menggunakan opsi ini.

```bash
# Urutan yang benar:
# 1. Buat migrasi
php artisan make:migration create_products_table

# 2. Jalankan migrasi
php artisan migrate

# 3. Buat resource dengan generate
php artisan make:filament-resource Product --generate
```

---

## 7. Form Schema

*Form Schema* mendefinisikan struktur formulir yang digunakan pada halaman *Create* dan *Edit*. Filament menyediakan berbagai komponen formulir (*form components*) yang dapat dikonfigurasi secara deklaratif.

### 7.1 Komponen Formulir yang Umum Digunakan

```php
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;

public static function form(Form $form): Form
{
    return $form->schema([

        // Input teks sederhana
        TextInput::make('name')
            ->label('Nama Produk')
            ->required()
            ->maxLength(255)
            ->placeholder('Masukkan nama produk'),

        // Area teks panjang
        Textarea::make('description')
            ->label('Deskripsi')
            ->rows(4)
            ->columnSpanFull(),

        // Input numerik dengan format mata uang
        TextInput::make('price')
            ->label('Harga')
            ->required()
            ->numeric()
            ->prefix('Rp')
            ->minValue(0),

        // Pemilih tanggal dan waktu
        DateTimePicker::make('expired_at')
            ->label('Tanggal Kedaluwarsa')
            ->nullable(),

        // Komponen pilihan (dropdown)
        Select::make('category')
            ->label('Kategori')
            ->options([
                'electronics' => 'Elektronik',
                'fashion'     => 'Fesyen',
                'food'        => 'Makanan',
            ]),

        // Saklar biner (aktif/nonaktif)
        Toggle::make('is_active')
            ->label('Status Aktif')
            ->default(true),
    ]);
}
```

### 7.2 Tata Letak Formulir

Filament mendukung penataan formulir (*form layout*) menggunakan komponen tata letak:

```php
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Tabs;

// Menggunakan Section untuk pengelompokan logis
Section::make('Informasi Dasar')
    ->description('Isi informasi utama produk')
    ->schema([
        TextInput::make('name'),
        Textarea::make('description'),
    ]),

// Menggunakan Grid untuk tata letak kolom
Grid::make(2)->schema([
    TextInput::make('price'),
    DateTimePicker::make('expired_at'),
]),
```

---

## 8. Table Schema

*Table Schema* mendefinisikan tampilan data dalam format tabel pada halaman *List*. Tabel Filament memiliki kemampuan pencarian, pengurutan, pemfilteran, dan aksi massal (*bulk actions*) secara bawaan.

### 8.1 Kolom Tabel

```php
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\BadgeColumn;

public static function table(Table $table): Table
{
    return $table
        ->columns([
            // Kolom teks dengan kemampuan pencarian dan pengurutan
            TextColumn::make('name')
                ->label('Nama Produk')
                ->searchable()
                ->sortable(),

            // Kolom teks dengan format mata uang
            TextColumn::make('price')
                ->label('Harga')
                ->money('IDR')
                ->sortable(),

            // Kolom teks dengan pembatasan karakter
            TextColumn::make('description')
                ->label('Deskripsi')
                ->limit(50)
                ->tooltip(fn ($record) => $record->description),

            // Kolom tanggal dengan format lokal
            TextColumn::make('expired_at')
                ->label('Kedaluwarsa')
                ->dateTime('d M Y H:i')
                ->sortable(),

            // Kolom ikon untuk nilai boolean
            IconColumn::make('is_active')
                ->label('Aktif')
                ->boolean(),
        ]);
}
```

### 8.2 Filter Tabel

```php
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

->filters([
    // Filter berdasarkan pilihan kategori
    SelectFilter::make('category')
        ->options([
            'electronics' => 'Elektronik',
            'fashion'     => 'Fesyen',
        ]),

    // Filter rentang tanggal kustom
    Filter::make('created_at')
        ->form([
            DatePicker::make('created_from')->label('Dari'),
            DatePicker::make('created_until')->label('Hingga'),
        ])
        ->query(function (Builder $query, array $data): Builder {
            return $query
                ->when($data['created_from'], fn ($q, $date) => $q->whereDate('created_at', '>=', $date))
                ->when($data['created_until'], fn ($q, $date) => $q->whereDate('created_at', '<=', $date));
        }),
])
```

### 8.3 Aksi Tabel

```php
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;

->actions([
    ViewAction::make(),
    EditAction::make(),
    DeleteAction::make(),
])
->bulkActions([
    BulkActionGroup::make([
        DeleteBulkAction::make(),
    ]),
])
```

---

## 9. Infolist Schema

*Infolist* adalah komponen tampilan baca saja (*read-only*) yang digunakan pada halaman *View* untuk menampilkan detail suatu rekaman secara terstruktur dan estetis.

```php
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Grid;

public static function infolist(Infolist $infolist): Infolist
{
    return $infolist->schema([
        Section::make('Detail Produk')->schema([
            Grid::make(2)->schema([
                TextEntry::make('name')
                    ->label('Nama Produk'),

                TextEntry::make('price')
                    ->label('Harga')
                    ->money('IDR'),
            ]),

            TextEntry::make('description')
                ->label('Deskripsi')
                ->columnSpanFull(),

            TextEntry::make('expired_at')
                ->label('Tanggal Kedaluwarsa')
                ->dateTime('d M Y H:i'),
        ]),
    ]);
}
```

---

## 10. Pages (Halaman)

*Pages* dalam Filament adalah kelas Livewire yang merepresentasikan halaman-halaman dalam panel. Setiap *resource* secara bawaan memiliki empat halaman: `ListRecords`, `CreateRecord`, `EditRecord`, dan `ViewRecord`.

### 10.1 Kustomisasi Halaman

```php
// app/Filament/Admin/Resources/Products/Pages/ListProducts.php
<?php

namespace App\Filament\Admin\Resources\Products\Pages;

use App\Filament\Admin\Resources\Products\ProductResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    // Menambahkan tombol aksi di pojok kanan atas
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
```

### 10.2 Halaman Kustom

Selain halaman bawaan, pengembang dapat membuat halaman kustom untuk keperluan tertentu:

```bash
php artisan make:filament-page Pengaturan
```

---

## 11. Widget

*Widget* adalah komponen yang dapat ditempatkan pada *dashboard* atau halaman tertentu untuk menampilkan informasi ringkas, statistik, atau visualisasi data.

### 11.1 Pembuatan Widget

```bash
# Membuat widget statistik
php artisan make:filament-widget StatistikProduk --stats-overview

# Membuat widget grafik
php artisan make:filament-widget GrafikPenjualan --chart

# Membuat widget tabel
php artisan make:filament-widget ProdukTerbaru --table
```

### 11.2 Contoh Widget Statistik

```php
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatistikProduk extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Produk', Product::count())
                ->description('Semua produk terdaftar')
                ->color('success'),

            Stat::make('Produk Aktif', Product::where('is_active', true)->count())
                ->description('Produk yang sedang aktif')
                ->color('primary'),

            Stat::make('Segera Kedaluwarsa', Product::expiringSoon()->count())
                ->description('Kedaluwarsa dalam 7 hari')
                ->color('warning'),
        ];
    }
}
```

---

## 12. Plugin dan Ekosistem

Proyek ini memanfaatkan sejumlah plugin Filament yang memperluas fungsionalitas bawaan:

| Plugin | Fungsi |
|---|---|
| `filament-shield` | Manajemen izin dan peran berbasis *role* (*RBAC*) |
| `filament-breezy` | Autentikasi lanjutan: verifikasi dua faktor, profil pengguna |
| `filament-logger` | Pencatatan aktivitas (*activity log*) pengguna |
| `overlook` | Widget ringkasan statistik resources |
| `global-search-modal` | Pencarian global lintas resource |
| `filament-shadcn-theme` | Tema visual bergaya *shadcn/ui* |
| `filament-expiration-notice` | Notifikasi sesi/data yang mendekati kedaluwarsa |
| `filament-developer-logins` | Akun login cepat untuk pengembangan |

---

## 13. Struktur Proyek

```
pert12/
├── docker-compose.yml          # Orkestrasi layanan Docker
├── .env                        # Variabel lingkungan
├── nginx/
│   └── nginx.conf              # Konfigurasi server web Nginx
├── php/
│   ├── Dockerfile              # Konfigurasi image PHP
│   └── www.conf                # Konfigurasi PHP-FPM
├── db/
│   └── data/                   # Volume persistensi data MariaDB
└── src/                        # Kode sumber aplikasi Laravel
    ├── app/
    │   ├── Filament/
    │   │   └── Admin/
    │   │       ├── Pages/      # Halaman kustom panel
    │   │       ├── Resources/  # Resource CRUD (Product, User, dll.)
    │   │       └── Widgets/    # Widget dashboard
    │   ├── Models/
    │   │   ├── Product.php     # Model Produk
    │   │   └── User.php        # Model Pengguna
    │   └── Providers/
    │       └── Filament/
    │           └── AdminPanelProvider.php
    ├── database/
    │   └── migrations/         # Berkas migrasi basis data
    └── resources/
        └── views/              # Tampilan Blade
```

---

## 14. Menjalankan Proyek dengan Docker

Proyek ini menggunakan Docker Compose untuk mengorkestrasi tiga layanan utama: aplikasi PHP, basis data MariaDB, dan server web Nginx.

### 14.1 Layanan Docker

| Layanan | Image | Port |
|---|---|---|
| `pemweb` | PHP 8.2 + Composer + Node | `5173` (Vite) |
| `db_pemweb` | MariaDB 10.2 | `13306` (host) |
| `nginx_pemweb` | Nginx (kustom) | `80` (HTTP) |

### 14.2 Perintah Menjalankan Proyek

```bash
# 1. Masuk ke direktori proyek
cd pert12

# 2. Membangun dan menjalankan seluruh layanan Docker
docker compose up -d --build

# 3. Masuk ke dalam kontainer PHP
docker exec -it pemweb bash

# 4. Instal dependensi PHP
composer install

# 5. Salin berkas konfigurasi lingkungan
cp .env.example .env

# 6. Generate kunci enkripsi aplikasi
php artisan key:generate

# 7. Jalankan migrasi basis data
php artisan migrate

# 8. Buat pengguna administrasi
php artisan make:filament-user

# 9. Instal dependensi JavaScript dan kompilasi aset
npm install && npm run dev
```

### 14.3 Akses Aplikasi

Setelah seluruh layanan berjalan, aplikasi dapat diakses melalui:

- **Panel Administrasi**: `http://localhost/admin`
- **Vite Dev Server**: `http://localhost:5173`
- **Basis Data (eksternal)**: `localhost:13306` (user: `djambred`)

---

## 15. Referensi

- Dokumentasi Resmi Filament: [https://filamentphp.com/docs](https://filamentphp.com/docs)
- Repositori Filament di GitHub: [https://github.com/filamentphp/filament](https://github.com/filamentphp/filament)
- Dokumentasi Laravel: [https://laravel.com/docs](https://laravel.com/docs)
- Dokumentasi Livewire: [https://livewire.laravel.com/docs](https://livewire.laravel.com/docs)
- Ekosistem Plugin Filament: [https://filamentphp.com/plugins](https://filamentphp.com/plugins)

---

*Dokumen ini disusun sebagai bahan ajar pertemuan ke-12 mata kuliah Pemrograman Web. Seluruh contoh kode bersifat ilustratif dan disesuaikan dengan konteks implementasi proyek.*
