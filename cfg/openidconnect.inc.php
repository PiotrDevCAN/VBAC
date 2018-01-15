<?php
$config_openidconnect = new stdClass();
$config_openidconnect->authorize_url = "https://w3id.alpha.sso.ibm.com/isam/oidc/endpoint/amapp-runtime-oidcidp/authorize";
$config_openidconnect->token_url = "https://w3id.alpha.sso.ibm.com/isam/oidc/endpoint/amapp-runtime-oidcidp/token";
$config_openidconnect->introspect_url = "https://w3id.alpha.sso.ibm.com/isam/oidc/endpoint/amapp-runtime-oidcidp/introspect";

$config_openidconnect->client_id['vbac'] = "NjVhM2FkZjQtNmU2Ny00";
$config_openidconnect->client_secret['vbac'] = "MzJkYjc5YjktNjBhYy00";
$config_openidconnect->redirect_url['vbac'] = "https://vbac.w3ibm.mybluemix.net/auth/index.php";


$config_openidconnect->client_id['vbac-ut'] = "ZTRmZmVlMWQtYWI0Mi00";
$config_openidconnect->client_secret['vbac-ut'] = "OGI5MzQwYTMtNzViMS00";
$config_openidconnect->redirect_url['vbac-ut'] = "https://vbac-ut.w3ibm.mybluemix.net/auth/index.php";

// $config_openidconnect->client_id['rob_dev'] = "ZGE0NDYzMTctYmZhNS00";
// $config_openidconnect->client_secret['rob_dev'] = "ZWFhM2U5MTktNDk4NS00";
// $config_openidconnect->redirect_url['rob_dev'] = "https://restdev.w3ibm.mybluemix.net/auth/index.php";
?>