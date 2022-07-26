Aplikasi Demo Integrasi SSO Keycloak-Laravel
============================================

Contoh aplikasi Laravel sederhana untuk demo integrasi dengan SSO Keycloak

![Screenshot](/screenshot.png?raw=true "Screenshot of example demo site")

## Fitur
- Single Sign-In
- Single Sign-Out dengan backchannel logout flow
- Display data user
- Custom styling untuk mempermudah proses demo

## setting penting
- KEYCLOAK_BASE_URL
- KEYCLOAK_REALM
- KEYCLOAK_CLIENT_ID
- KEYCLOAK_REDIRECT_URL
- STYLE_COLOR
- APP_NAME

## install
1. clone
2. edit .env_base from .env.example . We use custom .env filename so it wouldn be read by laravel. instead it would be supplied from docker via docker compose settings
3. Setup proper URL routing. See section below about URL settings for integration
4. Run application. The easiest way is to use `docker compose up`
However you could also run laravel manually by using `php artisan serve` command. you could also running multiple website in the same computer by modifying env var. Example:

  ```
  # in one terminal
  APP_NAME="Aplikasi Kota Bandung" STYLE_COLOR=red KEYCLOAK_CLIENT=client_bandung php artisan serve --port 8001

  # in other terminal
  APP_NAME="Aplikasi Kota Bogor" STYLE_COLOR=blue KEYCLOAK_CLIENT=client_bogor php artisan serve --port 8002
  ```

## Notes terkait backchannel logout
- jangan lupa di update juga setting client untuk url backchannel logout agar fungsi Single Sign-Out bisa berjalan
- ~per 25 Juli 2022, untuk fitur backchannel logout baru bisa untuk fitur logout dari web console Keycloak, adapun untuk trigger backchannel logout dari fitur logout reguler dari sisi client belum berhasil. masih perlu di cek lebih dalam~
  Sudah solved. Kuncinya memang di routing URL antar container. Lihat penjelasan di bawah

## Notes terkait host & url untuk setting integrasi
- untuk integrasi OIDC diperlukan interaksi antara server keycloak maupun web client, dan interaksi ini terjadi tidak hanya di sisi client/via user agent, tapi juga di sisi host-to-host/via backchannel. Sementara berhubung umumnya settingannya di deploy via docker, ini akan **ada kendala ketika url integrasi yang digunakan adalah localhost** karena akan ketika dari dalam container memanggil ke localhost, by default dia tidak akan bisa menghubungi container di luar. karena itu ada beberapa solusi:
    - container sebaiknya dihubungkan dulu ke luar via service seperti Ngrok atau localhost.run, nantinya untuk url hubungan keycloak-laravel menggunakan url public dari service-service di atas:
    - edit file /etc/hosts dan menambahkan alias dari localhost ke nama container. contoh:

    ```
    127.0.0.1	keycloak
    127.0.0.1	web_bkd
    127.0.0.1	web_diknas
    ```

    entry di atas tujuannya agar setiap container bisa diakses menggunakan nama containernya, baik dari host maupun dari jaringan internal docker
