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
2. edit .env from .env.example
3. run `php artisan serve`. you could also running multiple website in the same computer by modifying env var. Example:

  ```
  # in one terminal
  APP_NAME="Aplikasi Kota Bandung" STYLE_COLOR=red KEYCLOAK_CLIENT=client_bandung php artisan serve --port 8001

  # in other terminal
  APP_NAME="Aplikasi Kota Bogor" STYLE_COLOR=blue KEYCLOAK_CLIENT=client_bogor php artisan serve --port 8002
  ```
