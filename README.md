Aplikasi Demo Integrasi SSO Keycloak-Laravel
============================================

Repo ini berisi kode website yang dirancang sebagai demo integrasi aplikasi pemerintahan dengan sistem SSO berbasis Keycloak. Repo ini juga ditujukan sebagai referensi implementasi fitur2 SSO bagi para developer aplikasi/website di lingkungan pemerintahan jawa barat.

## Screenshots
Aplikasi/Website Pemprov
![Screenshot Service Provider](/screenshot.png?raw=true "Screenshot of example demo site")
Aplikasi/Website Simulasi SIAP
![Screenshot Simulasi SIAP](/screenshot_siap.png?raw=true "Screenshot of SIAP website simulation")

## Fitur
Repositori ini berisi beberapa bagian:
- contoh file  `docker-compose.yml` untuk deployment local integrasi SSO untuk keperluan development. file tersebut mendefinisikan 3 jenis services:
    - server keycloak sendiri. versi yang digunakan adalah versi 17 karena versi diatas itu memiliki issue terkait fitur logout dengan plugin Socialite/Keycloak yang digunakan di repo ini
    - website simulasi aplikasi OPD yang nantinya akan terkoneksi ke sistem keycloak. website ini berbasis PHP 8.1 dan Laravel 9. Adapun database yang digunakan adalah SQLite dengan asumsi kode ini hanya berfungsi sebagai dummy
    - website simulasi aplikasi SIAP, yang sebenarnya menggunakan codebase yang sama dengan website aplikasi OPD, namun dengan "flag" berupa environment variable yang nantinya akan membedakan fitur di 2 website ini
- adapun fitur yang ada di website simulasi aplikasi OPD antara lain:
    - Single Sign-In
    - Single Sign-Out dengan backchannel logout flow
    - Display data user
    - Custom styling untuk mempermudah proses demo
    - endpoint khusus `/mobile-api/me` yang mensimulasikan integrasi terhadap mobile app atau frontend. Endpoint ini akan menerima bearer token yang didapat dari keycloak, dan menggunakan NIK yang ada di dalamnya untuk mengambil data user yang bersesuaian dari SIAP serta dari database local aplikasi itu sendiri
- kemudian fitur yang ada di website simulasi SIAP antara lain:
    - login ke SIAP dengan SSO
    - endpoint publik:
        - autentikasi dengan JWT token dari keycloak
        - ada endpoint untuk get data pegawai by NIK yang ada di JWT token
        - fitur lupa password/reset password
    - halaman edit profil untuk user sendiri:
        - bisa ganti password + set otp
        - history login user, termasuk yang sedang aktif
        - fitur logout dari jauh
    - halaman untuk admin SIAP:
        - CRUD data pegawai dummy
        - setiap ada add & delete data pegawai, auto update juga ke keycloak

## install
1. clone
2. edit `.env_base` from `.env.example` . We use custom `.env` filename so it wouldn't be read by Laravel. instead it would be supplied from docker via docker compose settings. the reason is to allow base env vars to be used accross different containers, while in the same time allow customized values using docker compose configurations.
3. Setup proper URL routing. See section below about URL settings for integration
4. Run application. The easiest way is to use `docker compose up`
However you could also run laravel manually by using `php artisan serve` command. you could also running multiple website in the same computer by modifying env var. Example:

  ```
  # in one terminal
  APP_NAME="BKD" STYLE_COLOR=red KEYCLOAK_CLIENT=client_bkd php artisan serve --port 8001

  # in other terminal
  APP_NAME="Diknas" STYLE_COLOR=blue KEYCLOAK_CLIENT=client_diknas php artisan serve --port 8002
  ```

## Penjelasan terkait fungsionalitas single sign-in & single sign-out

Implementasi inti terkait Single Sign-On dan Single Sign-Off ada di controller `App\Http\Controllers\OAuthController.php`. Di sana ada 4 metode penting:

- `redirect()` : metode yang dipanggil ketika user melakukan login dengan Single Sign-On
- `callback()` : metode yang dipanggil ketika user kembali dari halaman SSO. Di fungsi ini juga terjadi pencatatan session id yang diperlukan untuk fungsi Single Sign-Out
- `logout()` : metode yang dipanggil ketika user ingin melakukan logout dari web yang saat ini digunakan. metode ini akan meredirect user ke web SSO, yang kemudian akan melogout semua sesi di browser tersebut dan mengirimkan 'Backchannel Logout Request'
- `logoutWebhook()` : metode yang dipanggil ketika web menerima 'Backchannel Logout Request'. Server akan memeriksa nilai session id yang dikirimkan oleh server SSO, dan menghapus semua session yang terkait dengan session tersebut sehingga user pun ter-logout.

Selain itu di `App\Helpers.php` ada fungsi-fungsi penting:
- `parseJWTToken()` : untuk memparsing token JWT yang masuk menggunakan public key dari endpoint JWK bawaan Keycloak

## Penjelasan terkait fungsionalitas integrasi SSO-SIAP
- file `App\Helpers.php`, berisi beberapa fungsi penting:
    - `parseJWTToken($token)` :
    - `isAdmin($token)` :
    - `getCurrentUserProfileFromSIAP($token)` :
- file `App\Service\KeycloakAdmin.php` berisi class untuk interaksi dari SIAP ke Keycloak sebagai Admin Keycloak

## notes terkait setup keycloak
- untuk setiap aplikasi yang akan menggunakan fitur Single Sign-On & Single Sign-Out, perlu melakukan pembuatan client id & setting agar bisa terintegrasi dengan SSO Keycloak. Beberapa setting penting:
    - set redirect url yang diizinkan
    - set backchannel logout url
    - set attribute mapper
- untuk mempermudah setup keycloak di local, sudah disiapkan script untuk konfig keycloak dengan data dummy di `./keycloak-autosetup-script.sh`. Untuk menjalankannya di container yang ada di docker-compose repo ini, bisa dengan command:
        ```
        docker compose exec keycloak sh /app/keycloak-autosetup-script.sh
        ```
- script di sudah include juga pembuatan user dummy dengan login:
    ```
    username: testuser
    password: testpassword
    ```
- Khusus untuk integrasi SIAP ke Keycloak, dibutuhkan client Keycloak khusus dengan tipe **service account**. Petunjuk setting nya bisa dilihat di https://www.keycloak.org/docs/latest/server_admin/index.html#_service_accounts
    - Selain itu untuk manajemen user, client tersebut perlu disetting juga role nya. menu settingnya ada di web admin keycloak:
        - di sidebar masuk ke Clients
        - pilih client yang sesuai
        - masuk ke tab "Service Account Roles"
        - di dropdown "Client Roles" pilih  "realm management"
        - pastikan "manage-users" ada di daftar "Assigned Roles"

![Screenshot Setting Keycloak Client untuk SIAP](/screenshot_settings_client_siap.png?raw=true "Screenshot Setting Keycloak Client untuk SIAP")

## Notes terkait backchannel logout
- jangan lupa di update juga setting client untuk url backchannel logout agar fungsi Single Sign-Out bisa berjalan
- ~per 25 Juli 2022, untuk fitur backchannel logout baru bisa untuk fitur logout dari web console Keycloak, adapun untuk trigger backchannel logout dari fitur logout reguler dari sisi client belum berhasil. masih perlu di cek lebih dalam~
  Sudah solved. Kuncinya memang di routing URL antar container. Lihat penjelasan di bawah

## Notes terkait host & url untuk setting integrasi
- untuk integrasi OIDC diperlukan interaksi antara server keycloak maupun web client, dan interaksi ini terjadi tidak hanya di sisi client/via user agent, tapi juga di sisi host-to-host/via backchannel. Sementara berhubung umumnya settingannya di deploy via docker, ini akan **ada kendala ketika url integrasi yang digunakan adalah localhost** karena akan ketika dari dalam container memanggil ke localhost, by default dia tidak akan bisa menghubungi container di luar. karena itu ada beberapa solusi:
    - gunakan ip addres static dari host. beberapa ip address yang bisa dicoba:
        - 172.17.0.1 -> untuk di linux
        - ip komputer di jaringan yang didapat dari command `ipconfig`, `ifconfig` atau `ip addr`
    - edit file /etc/hosts dan menambahkan alias dari localhost ke nama container. contoh:

    ```
    127.0.0.1	keycloak
    127.0.0.1	web_bkd
    127.0.0.1	web_diknas
    ```
    entry di atas tujuannya agar setiap container bisa diakses menggunakan nama containernya, baik dari host maupun dari jaringan internal docker
    - container dihubungkan dulu ke luar via service seperti Ngrok atau localhost.run, nantinya untuk url hubungan keycloak-laravel menggunakan url public dari service-service di atas:


