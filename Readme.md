# Langkah Langkah Create Project Laravel

#### Note : Lakukan perintah tanpa tanda kutip "

1. "docker compose up -d --build" (pertama kali) atau "docker compose up -d" (jika udh pernah build)
2. "code ." (buka vscode)
3. "docker exec -it pemweb bash"
4. "composer create-project --prefer-dist raugadh/fila-starter ." (create project laravel filament)
5. "php artisan key:generate" (generate project key)
6. "php artisan storage:link" (ngebuat storage project)
7. "chown -R www-data:www-data storage/*" (memberikan akses ke storage)
8. "chown -R www-data:www-data bootstrap/*" (memberikan akses ke bootstrap)
9. kembali ke vscode lalu buka src/.env lalu lakukan perubahan seperti langkah selanjutnya
10. APP_DEBUG=false
11. APP_TIMEZONE='Asia/Jakarta'
12. APP_URL=http://localhost
13. ASSET_URL=http://localhost
14. DB_CONNECTION=sqlite
15. DB_CONNECTION=mysql
16. DB_HOST=db_pemweb
17. DB_PORT=3306
18. DB_DATABASE=db_pemweb
19. DB_USERNAME=root
20. DB_PASSWORD=p455w0rd
21. kembali ke terminal / ubuntu
22. "php artisan migrate" (perubahan migrations / tabel database)
23. "php artisan migrate:fresh" (membuat ulang migrations / tabel database)
24. "php artisan db:seed --force" (mengirim paksa seeder ke database)
25. "php artisan shield:generate --all" (membuat permissions)
26. "chmod 777 -R storage/* && chmod 777 bootstrap/*" (memberikan izin publik "tidak rekomendasi di project nyata")
27. "npm run build" (ngebuild assets frontend)
28. "php artisan project:init" (optimizer, hapus cache, cookie web, dan load filament)
29. buka localhost dengan browser Username : [admin@admin.com](mailto:admin@admin.com) Password : password


# Perintah Docker

#### Note : Lakukan perintah tanpa tanda kutip "
1. "docker compose down" (untuk menghapus kontainer yang telah dibuat)
2. "docker compose stop" (untuk mematikan semua imgaes dan kontainer yang berjalan sesuai docker-compose.yml tanpa menghapus kontainernya)
3. "docker compose start" (untuk menjalankan kontainer yang telah ada sesuai docker-compose.yml)
4. "docker ps" (untuk melihat kontainer yang berjalan)