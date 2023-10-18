<?php
$config_openidconnect = new stdClass();

/*
 * SSO Element of Config
 *
 */

$config_openidconnect->client_id      = $_ENV['worker_api_client_id'];
$config_openidconnect->client_secret  = $_ENV['worker_api_client_secret'];
$config_openidconnect->token_scope    = $_ENV['worker_api_token_scope'];

$config_openidconnect->authorize_url  = $_ENV['worker_api_authority'].'/authorize';
$config_openidconnect->token_url      = $_ENV['worker_api_authority'].'/token';
$config_openidconnect->userinfo_url   = $_ENV['worker_api_authority'].'/userinfo';
$config_openidconnect->introspect_url = $_ENV['worker_api_authority'].'/introspect';

error_log('Authorising to:' . $config_openidconnect->authorize_url . " as (" . $config_openidconnect->client_id . ") ");

?>