<?php

use Firebase\JWT\JWK;
use Firebase\JWT\JWT;

/*
 * Fungsi untuk memparsing token JWT yang masuk menggunakan public key dari endpoint JWK bawaan Keycloak

 */
function parseJWTToken($token) {
    // ambil public key dari keycloak menggunakan endpoint JWK Keycloak. Idealnya hasil dari fungsi ini di cache agar tidak terlalu sering diakses
    $jwks_response = file_get_contents(env('KEYCLOAK_BASE_URL') . '/realms/' . env('KEYCLOAK_REALM') . '/protocol/openid-connect/certs');
    $jwks = json_decode($jwks_response, true);

    // parsing token JWT menggunakan public key JWK di atas
    return JWT::decode($token, JWK::parseKeySet($jwks));
}

// Fungsi untuk memeriksa apakah NIK yang diinput  adalah admin di web
// ini. Ini adalah contoh sebagai gambaran bagaimana mekanisme otorisasi di
// lingkup aplikasi yang sudah terintegrasi dengan SSO
function isAdmin($nik) {
    $admin_niks = explode(',', env('ADMIN_NIK'));

    return in_array($nik, $admin_niks);
}
