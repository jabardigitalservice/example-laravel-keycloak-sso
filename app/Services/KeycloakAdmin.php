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
            self::$_client = KeycloakClient::factory([
                'baseUri' => env('KEYCLOAK_BASE_URL'),
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
