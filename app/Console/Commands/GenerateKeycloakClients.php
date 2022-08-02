<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Keycloak\Admin\KeycloakClient;

class GenerateKeycloakClients extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate-keyloak-clients';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Helper script to generate multiple clients';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // edit list client sesuai kebutuhan. null untuk client bertipe service account (untuk admin access)
        $clients = [
            'test_client_admin' => null,
            'test_client_diknas' => 'http://10.53.1.174:8001',
            'test_client_siap' => 'http://10.53.1.174:8010',
        ];

        $keycloakBaseUrl = env('KEYCLOAK_BASE_URL');
        if ($keycloakBaseUrl[-1] != '/') $keycloakBaseUrl .= '/';

        $client = KeycloakClient::factory([
            'baseUri' => $keycloakBaseUrl,

            // sesuaikan dengan kondisi server masing2
            'realm' => 'master',
            'grant_type' => 'password',
            'username' => 'admin',
            'password' => 'admin',
        ]);

        foreach ($clients as $CLIENT_ID => $CLIENT_BASE_URL) {
            $KEYCLOAK_REALM = env('KEYCLOAK_REALM');

            if (empty($CLIENT_BASE_URL)) {
                // assume creating service account clients
                $payload = <<<EOF
                {
                    "realm": "$KEYCLOAK_REALM",
                    "clientId": "$CLIENT_ID",
                    "name": "$CLIENT_ID",
                    "access": {
                        "view": true,
                        "configure": true,
                        "manage": true
                    },
                    "enabled": true,

                    "protocol": "openid-connect",
                    "publicClient": false,
                    "directAccessGrantsEnabled": false,
                    "standardFlowEnabled": false,
                    "implicitFlowEnabled": false,
                    "serviceAccountsEnabled": true,
                    "fullScopeAllowed": true,
                    "defaultClientScopes": [
                        "web-origins",
                        "role_list",
                        "profile",
                        "roles",
                        "email"
                    ]
                }
                EOF;
            } else {
                $payload = <<<EOF
                {
                    "realm": "$KEYCLOAK_REALM",
                    "clientId": "$CLIENT_ID",
                    "name": "$CLIENT_ID",
                    "access": {
                        "view": true,
                        "configure": true,
                        "manage": true
                    },
                    "enabled": true,

                    "baseUrl": "$CLIENT_BASE_URL",
                    "redirectUris": [
                        "$CLIENT_BASE_URL/*"
                    ],

                    "protocol": "openid-connect",
                    "publicClient": true,
                    "directAccessGrantsEnabled": false,
                    "standardFlowEnabled": true,
                    "implicitFlowEnabled": false,
                    "fullScopeAllowed": true,
                    "defaultClientScopes": [
                        "web-origins",
                        "role_list",
                        "profile",
                        "roles",
                        "email"
                    ],

                    "attributes": {
                        "backchannel.logout.revoke.offline.tokens": "false",
                        "backchannel.logout.session.required": "true",
                        "backchannel.logout.url": "$CLIENT_BASE_URL/auth/logout_webhook"
                    },

                    "protocolMappers": [
                        {
                            "protocol":"openid-connect",
                            "config":{
                                "id.token.claim":"true",
                                "access.token.claim":"true",
                                "userinfo.token.claim":"true",
                                "multivalued":"",
                                "aggregate.attrs":"",
                                "user.attribute":"nik",
                                "claim.name":"nik",
                                "jsonType.label":"String"
                            },
                            "name":"nik",
                            "protocolMapper":"oidc-usermodel-attribute-mapper"
                        }
                    ]
                }
                EOF;
            }

            $result = $client->createClient(json_decode($payload, true));
            print_r($result);
        }
    }
}
