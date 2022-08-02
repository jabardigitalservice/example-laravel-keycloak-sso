# keycloak-autosetup-example.sh
# Adapted from : https://keycloak.ch/keycloak-tutorials/tutorial-1-installing-and-running-keycloak/

# basic variables. edit as needed
KCADM="/opt/keycloak/bin/kcadm.sh"
REALM_NAME=test_realm
CLIENT_ID=test_client
CLIENT_NAME="Test Client"
USER_NAME=testuser
USER_PASSWORD=testpassword
CLIENT_BASE_URL="http://localhost:8000"

# establish connection session to keycloak
$KCADM config credentials --server ${KEYCLOAK_BASE_URL} \
--user admin \
--password admin \
--realm master

# test connection
$KCADM get serverinfo

# setup new realm
$KCADM create realms -s realm="${REALM_NAME}" -s enabled=true

# setup client
KEYCLOAK_PAYLOAD=$(cat <<EOF
{
    "clientId": "${CLIENT_ID}",
    "name": "${CLIENT_NAME}",
    "access": {
        "view": true,
        "configure": true,
        "manage": true
    },
    "enabled": true,

    "baseUrl": "${CLIENT_BASE_URL}",
    "redirectUris": [
        "${CLIENT_BASE_URL}/*"
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
		"backchannel.logout.url": "${CLIENT_BASE_URL}/auth/logout_webhook"
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
EOF
)
echo "$KEYCLOAK_PAYLOAD"
echo "$KEYCLOAK_PAYLOAD" | $KCADM create clients -r ${REALM_NAME} -f -

# setup user
$KCADM create users -r $REALM_NAME \
-s username="${USER_NAME}" \
-s enabled=true \
-s firstName="My" \
-s lastName="User" \
-s email="my.user@example.com" \
-s "attributes.nik=123456789012"

## setup user password
$KCADM set-password -r $REALM_NAME \
--username "${USER_NAME}" \
--new-password "${USER_PASSWORD}"

