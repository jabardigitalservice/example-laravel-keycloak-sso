<?php

use Firebase\JWT\JWK;
use Firebase\JWT\JWT;

use App\Services\KeycloakAdmin;

/*
 * Fungsi untuk memparsing token JWT yang masuk menggunakan public key dari endpoint JWK bawaan Keycloak

 */
function parseJWTToken($token) {
    // ambil public key dari keycloak menggunakan endpoint JWK Keycloak. Idealnya hasil dari fungsi ini di cache agar tidak terlalu sering diakses
    $jwks_response = file_get_contents(env('KEYCLOAK_BASE_URL') . '/realms/' . env('KEYCLOAK_REALM') . '/protocol/openid-connect/certs');
    $jwks = json_decode($jwks_response, true);

    // parsing token JWT menggunakan public key JWK di atas
    $result = JWT::decode($token, JWK::parseKeySet($jwks));

    // Idealnya di sini ada validasi tambahan untuk access token yang masuk,
    // misalnya memeriksa nilai field `iss`, `aud`, dst. Namun karena web ini
    // hanya contoh, untuk mempesingkat kode di sini tidak akan dilakukan validasi lanjutan
    // referensi lebih dalam bisa cek: https://datatracker.ietf.org/doc/html/rfc8725#section-3

    return $result;
}

// Fungsi untuk memeriksa apakah NIK yang diinput  adalah admin di web
// ini. Ini adalah contoh sebagai gambaran bagaimana mekanisme otorisasi di
// lingkup aplikasi yang sudah terintegrasi dengan SSO
function isAdmin($user) {
    return in_array($user->role, [ 'admin', 'superadmin' ]);
}

function getCurrentKeycloakSessionId() {
    $KEYCLOAK_LOGIN_DETAILS = session('KEYCLOAK_LOGIN_DETAILS');

    if ($KEYCLOAK_LOGIN_DETAILS) return $KEYCLOAK_LOGIN_DETAILS['session_state'];

    return null;
}

// ini adalah fungsi yang bisa digunakan oleh aplikasi/website pemerintah untuk
// mendapatkan data profil user dari SIAP berdasarkan nik user yang login
// melalui sistem SSO.
function getCurrentUserProfileFromSIAP($token=null) {
    info('retrieving user profile from SIAP');
    $accessToken = !is_null($token) ?
                   $token :
                   session('KEYCLOAK_LOGIN_DETAILS')['access_token'] ;
    try {
        $siapUrl = env('SIAP_BASE_URL') . '/get_user_detail?token=' . $accessToken;
        $siapData = file_get_contents($siapUrl);
        return json_decode($siapData, true);
    } catch (Exception $e) {
        return $e->getMessage();
    }
}

function getKeycloakUserId($user) {
    // find matching Keycloak User by NIK
    $result = KeycloakAdmin::getClient()->getUsers([
        'q' => 'nik:' . $user->nik,
    ]);

    if (empty($result)) {
        info('No keycloak user found for NIK ' . $user->nik);
        abort(500, 'No keycloak user found for NIK ' . $user->nik);
    }

    if (isset($result['error'])) {
    info(json_encode($result));
        abort(500, json_encode($result));
    }

    $keycloakUserId = $result[0]['id'];

    info("Found matching user in Keycloak with id $keycloakUserId");

    return $keycloakUserId;
}
