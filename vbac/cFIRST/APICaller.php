<?php
namespace vbac\cFIRST;

class APICaller
{
    private static $userName = 'sandbox';
    private static $pw = '$andb0x';
    private static $apiKey = '17d756c2-d6fd-4233-9bbc-3cde20ad77bc';
    
    private static $baseAPIUrl = 'https://cfusapi.cfirstcorp.com/v3/IBgvCandidate.svc/sjson';

    private static $authenticationPage = '/authentication';
    private static $accessTokenPage = '/accesstoken';
    private static $addCandidatePage = '/AddCandidate';
    private static $getPackagesPage = '/GetPackages';
    private static $getCandidateStatusPage = '/GetCandidateStatus';

    private $accessToken = null;
    private $refreshToken = null;
    public $packages = null;

function __construct()
{
    // echo '<p>make authentication</p>';
    $this->makeAuthentication();
}

function prepareParams($params = array()) {
    if (count($params) > 0) {
        $str = '';
        foreach($params as $key => $value) {
            if ($key == 0) {
                $str .= '?';
            } else {
                $str .= '&';
            }
            $str .= $value;
        }
        return $str;
    }
    return '';
}

function prepareCandidateData($data) {
    $dataStr = json_encode($data);
    $dataStrURLEnc = urlencode('['.$dataStr.']');
    return $dataStrURLEnc;
}

function getAccessToken() {
    // echo '<p>get access token</p>';

    $this->refreshAccessToken();

    return $this->accessToken;
}

function getAccessTokenParam() {
    return 'AccessToken=' . $this->getAccessToken();
}

function getRefreshToken() {
    // echo '<p>get refresh token</p>';
    return $this->refreshToken;
}

function getRefreshTokenParam() {
    return 'RefreshToken=' . $this->getRefreshToken();
}

function getCandidateIdParam($id = '') {
    if (!empty($id)) {
        return 'candidateId=' . $id;
    }
    return '';
}

function getDateParam($date = '') {
    if (!empty($id)) {
        return 'date=' . $date;
    }
    return '';
}

function getCredentialsArr() {
    return array(
        'UserName=' . self::$userName,
        'Password=' . self::$pw,
        'Apikey=' . self::$apiKey
    );
}

function makeCURLQuery($url = '') {
        
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    curl_setopt($ch, CURLOPT_FAILONERROR, true);
    
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

    $headers = array();
    $headers[] = 'Content-Type: application/json';
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }
    curl_close($ch);

    return $result;
}

function makeCURLPostQuery($url = '', $data = '') {
        
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    curl_setopt($ch, CURLOPT_FAILONERROR, 1);
    
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

    $headers = array();
    $headers[] = 'Content-Type: application/json';
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

    $result = curl_exec($ch);

    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }
    curl_close($ch);

    return $result;
}

function getJsonFromResultSet($resultSet = '') {
    //Attempt to decode JSON.
    $dataObject = json_decode($resultSet);
    
    //Backwards compatability.
    if(!function_exists('json_last_error')){
        if($dataObject === false || $dataObject === null) {
            throw new \Exception('Could not decode JSON!');
        }
    } else {
    
        //Get the last JSON error.
        $jsonError = json_last_error();
        
        //In some cases, this will happen.
        if(is_null($dataObject) && $jsonError == JSON_ERROR_NONE) {
            throw new \Exception('Could not decode JSON!');
        }
        
        //If an error exists.
        if($jsonError != JSON_ERROR_NONE) {
            $error = 'Could not decode JSON! ';
            
            //Use a switch statement to figure out the exact error.
            switch($jsonError){
                case JSON_ERROR_DEPTH:
                    $error .= 'Maximum depth exceeded!';
                    break;
                case JSON_ERROR_STATE_MISMATCH:
                    $error .= 'Underflow or the modes mismatch!';
                    break;
                case JSON_ERROR_CTRL_CHAR:
                    $error .= 'Unexpected control character found';
                    break;
                case JSON_ERROR_SYNTAX:
                    $error .= 'Malformed JSON';
                    break;
                case JSON_ERROR_UTF8:
                    $error .= 'Malformed UTF-8 characters found!';
                    break;
                default:
                    $error .= 'Unknown error!';
                    break;
            }
            throw new \Exception($error);
        }
    }
    
    // if (! is_object($dataObject)) {
    //     $dataObject = json_decode($dataObject);
    // }

    // echo '<p>aaa NEW -> </p>';
    // var_dump($dataObject);
    // echo '<p> <- bbb NEW </p>';
    
    if (! is_array($dataObject)) {
        $dataObject = array(
            $dataObject
        );
    }
    
    return $dataObject;
}


// ------- 
// Authentication
// Host:
// http://developer.api.cfirstcorp.com/v1/IBgvCandidate.svc/json/authentication?
    // UserName={UserName}
    // &Password={Password}
    // &Apikey={Apikey}
function makeAuthentication() {

    $page = static::$authenticationPage;

    $paramsArr = array(
        'UserName=' . self::$userName,
        'Password=' . self::$pw,
        'Apikey=' . self::$apiKey
    );

    $params = $this->prepareParams($paramsArr);

    $url = self::$baseAPIUrl . $page . $params;
    $result = $this->makeCURLQuery($url);

    $json = $this->getJsonFromResultSet($result);
    $dataObject = json_decode($json[0]);

    $this->accessToken = $dataObject[0]->AcessToken;
    $this->refreshToken = $dataObject[0]->RefreshToken;
}


// ------- 
// Access Token & Refresh Token
// Once authentication process has been done using your access token and refresh token initiate background check
// for the candidate.
// Before doing any process you have to update Access Token using refresh token for retrieving any data to confirm
// that it has not expired.
// Using Refresh Token you got new Access Token.
// Host:
// http://developer.api.cfirstcorp.com/v1/IBgvCandidate.svc/json/accesstoken?
    // UserName={UserName}
    // &Password={Password}
    // &Apikey={Apikey}
    // &RefreshToken={RefreshToken}
// Content-Type: application/x-www-form-urlencoded
function refreshAccessToken() {

    $page = static::$accessTokenPage;

    $paramsArr = array(
        'UserName=' . self::$userName,
        'Password=' . self::$pw,
        'Apikey=' . self::$apiKey,
        $this->getRefreshTokenParam()
    );

    $params = $this->prepareParams($paramsArr);

    $url = self::$baseAPIUrl . $page . $params;
    $result = $this->makeCURLQuery($url);

    $json = $this->getJsonFromResultSet($result);
    $dataObject = json_decode($json[0]);

    $this->accessToken = $dataObject[0]->AcessToken;
    $this->refreshToken = $dataObject[0]->RefreshToken;
}


// ---------------
// Get Packages
// It will help you get all the packages available for the account. The package id available from response will be
// used while requesting for background check.
// Here we have to post authentication request
// Host: http://developer.api.cfirstcorp.com/v1/IBgvCandidate.svc/json/GetPackages
//  www.cfirst.io
// CF-PD-102 INTERNAL 16
// Method: Get
// Data:
// accesstoken – “Valid Access Token”
function getPackages() {

    $page = static::$getPackagesPage;
    
    $paramsArr = array(
        'UserName=' . self::$userName,
        'Password=' . self::$pw,
        'Apikey=' . self::$apiKey,
        $this->getAccessTokenParam()
    );

    $params = $this->prepareParams($paramsArr);

    $url = self::$baseAPIUrl . $page . $params;
    $result = $this->makeCURLQuery($url);
    
    $json = $this->getJsonFromResultSet($result);
    // $dataObject = json_decode($json[0]);

    $this->packages = $json[0]->Packages;
}


// ----------
// Request Background Check
// Initiate a background check on by sending a POST http call to the /candidate resource. Customer need
// below information for background verification.
// Note – Replace “Access Token” to the actual Access Token you received.
// POST: http://developer.api.cfirstcorp.com/v1/IBgvCandidate.svc/json/AddCandidate
// Post Data: Candidate & accesstoken
// Candidate: URL Encoded JSON String having Candidate Object Data
// accesstoken: Valid Access Token received from the refresh token request
function addCandidate($data = array()) {

    $page = static::$addCandidatePage;

    $candidateDataURLEncoded = $this->prepareCandidateData($data);

    $data = array(
        'accesstoken' => $this->getAccessToken(),
        'Candidate' => $candidateDataURLEncoded
    );
    $dataJson = json_encode($data);

    $url = self::$baseAPIUrl . $page;
    $result = $this->makeCURLPostQuery($url, $dataJson);
    
    $json = $this->getJsonFromResultSet($result);
    // $dataObject = json_decode($json[0]);

    // return $result;
    return $json;
}


// ------------
// Get Candidate Status
// It will help you get all the packages available for the account. The package id available from response will be
// used while requesting for background check.
// Here we have to post authentication request
// Host: http://developer.api.cfirstcorp.com/v1/IBgvCandidate.svc/json/GetCandidateStatus
// Method: Get
// Data:
// accesstoken – “Valid Access Token”
// candidateId – if passed, only that candidate’s status will be provided, else status of all candidates will be
// provided
// date – if passed, only status changed after that date will be provided, else all data will be provided.
function getCandidateStatus($id = '', $date = '') {

    $page = static::$getCandidateStatusPage;

    $paramsArr = array(
        $this->getAccessTokenParam(),
        $this->getCandidateIdParam($id),
        $this->getDateParam($date)
    );

    $params = $this->prepareParams($paramsArr);

    $url = self::$baseAPIUrl . $page . $params;
    $result = $this->makeCURLQuery($url);

    $json = $this->getJsonFromResultSet($result);
    // $dataObject = json_decode($json[0]);

    // return $result;
    return $json;
}

// ------------------------------------------------------------------------------------------------------------------------------

// -----------
// Order Profile Page
// In this two-step process, after you receive candidate id from Add Candidate API, you need to redirect your
// user to Order Profile page. To redirect user please use below details in the URL.
// URL: https://capps.cfirst.io/Order/Order?enc=<ENCODED_STRING>
// Parameters: enc=<ENCODED_STRING>
// ENCODED_STRING => CryptoEncoding("u=<username>&p=<password>&c=<candidateid>").
//  * Here c is candidate id retrieved in first API call
//  * User u & p are test credentials that were used in API calls also
//  * Encryption Key: $@b@^3@t!t@3^@b@

// ---------------
// Candidate Details Page
// In this two-step process, after you receive candidate id from Add Candidate API, you need to redirect your
// user to Order Profile page. To redirect user please use below details in the URL.
// URL: https://capps.cfirst.io/Report/CandidateDetails?enc=<ENCODED_STRING>
// Parameters: enc=<ENCODED_STRING>
// ENCODED_STRING => CryptoEncoding("u=<username>&p=<password>&c=<candidateid>").
//  * Here c is candidate id retrieved in first API call
//  * User u & p are test credentials that were used in API calls also
//  * Encryption Key: $@b@^3@t!t@3^@b@

// ------------------------------------------------------------------------------------------------------------------------------
}