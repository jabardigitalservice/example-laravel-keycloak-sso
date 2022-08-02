<?php

namespace App\Services;

use Keycloak\Admin\KeycloakClient;

class KeycloakAdmin
{
    static $_client = null;

    static function getClient()
    {
        if (empty(self::$_client)) {
            // referensi: https://github.com/MohammadWaleed/keycloak-admin-client#how-to-use
            $keycloakBaseUrl = env('KEYCLOAK_BASE_URL');
            // behaviour yang ditemukan di lapangan: kalau base URL Keycloak memiliki akhiran '/auth' tanpa '/' di akhir, maka di KeycloakClient akan menghilangkan '/auth' sehingga akhirnya URL not found. Workaround di bawah berfungsi untuk mengatasi hal tersebut.
            if ($keycloakBaseUrl[-1] != '/') $keycloakBaseUrl .= '/';

            self::$_client = KeycloakClient::factory([
                'baseUri' => $keycloakBaseUrl,
                'realm' => env('KEYCLOAK_REALM'),
                'grant_type' => 'client_credentials',
                'client_id' => env('KEYCLOAK_ADMIN_CLIENT_ID'),
                'client_secret' => env('KEYCLOAK_ADMIN_CLIENT_SECRET'),

                /* token caching mechanism as described in https://github.com/MohammadWaleed/keycloak-admin-client#changing-how-the-token-is-saved-and-stored */
                //'token_storage' => new CustomTokenStorage(),
            ]);
        }

        return self::$_client;
    }
}
