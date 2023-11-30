<?php
/*
 * SSO Element of Config
 *
 */

$config_openidconnect = new stdClass();

 // base API hostname
$_ENV['cfirst_api_base_host'] = 'https://cfusapi.cfirstcorp.com/v3/IBgvCandidate.svc/sjson';

// additional API hostname
$_ENV['cfirst_api_aux_host'] = 'https://capps.cfirst.io';

// $_ENV['cfirst_user_id'] = 'sandbox';
// $_ENV['cfirst_password'] = '$andb0x';
// $_ENV['cfirst_api_key'] = '17d756c2-d6fd-4233-9bbc-3cde20ad77bc';

$_ENV['cfirst_user_id'] = 'cf_kyndryl_lloyds';
$_ENV['cfirst_password'] = 'V0$t3x@K';
$_ENV['cfirst_api_key'] = 'H8FE-X1GV-R1YL-D6CT-X1AC';

$config_openidconnect->base_host = $_ENV['cfirst_api_base_host'];
$config_openidconnect->aux_host  = $_ENV['cfirst_api_aux_host'];

$config_openidconnect->user_id   = $_ENV['cfirst_user_id'];
$config_openidconnect->password  = $_ENV['cfirst_password'];
$config_openidconnect->api_key   = $_ENV['cfirst_api_key'];

$config_openidconnect->authorize_url  = $config_openidconnect->base_host.'/authentication';
$config_openidconnect->token_url      = $config_openidconnect->base_host.'/accesstoken';
$config_openidconnect->userinfo_url   = $config_openidconnect->base_host.'/userinfo';      // invalid
$config_openidconnect->introspect_url = $config_openidconnect->base_host.'/introspect';    // invalid

$config_openidconnect->order_url  = $config_openidconnect->aux_host.'/Order/Order';
$config_openidconnect->candidate_details_url = $config_openidconnect->aux_host.'/Report/CandidateDetails';

error_log('Authorising to:' . $config_openidconnect->authorize_url . " as (" . $config_openidconnect->user_id . ") ");

?>