<?php
// #
// # Bluepages and Bluegroups login code
// # Not for public use. For working with bluepages
// # and bluegroups see ldap.php instead.
// #

// require https for auth
// if (!$GLOBALS['https']) {
// $redirect = "https://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
// header("Location: $redirect");
// exit;
// }

// do IBM Intranet Password auth if not cached
if (! isset($GLOBALS['ltcuser'])) {
    // ldap authenticaiton
    $result = FALSE;
    if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {
        $time_start = _microtime_float();
        $result = user_auth($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']);
        $time_end = _microtime_float();
    }
    // bail on ldap server errors
    if (defined('IIP_AUTH_ERRNO') && IIP_AUTH_ERRNO == 3)
        do_ldap_error();

        // prompt for user id and password
    if (! $result) {
        header('WWW-Authenticate: Basic realm="w3"');
        header('HTTP/1.0 401 Unauthorized');
        // user hit cancel button
        if (! isset($_SERVER['PHP_AUTH_USER'])) {
            define('IIP_AUTH_ERRNO', 4);
            define('IIP_LDAP_ERRNO', 0);
        } elseif (! isset($_SERVER['PHP_AUTH_PW'])) {
            define('IIP_AUTH_ERRNO', 6);
            define('IIP_LDAP_ERRNO', 0);
        }
        do_auth_error();
    } else {
       // ob_flush();
       // die('here');
        // cache the results in session
        $GLOBALS['ltcuser'] = $result;
        $GLOBALS['ltcuser'] += array(
            'expire' => (time() + 3600),
            'userid' => trim($_SERVER['PHP_AUTH_USER']),
            'groups' => array()
        );
        $_SESSION['ltcuser'] = $GLOBALS['ltcuser'];

        // make a log entry
        $msg = "Authenticated " . $GLOBALS['ltcuser']['userid'] . " in %01.3f seconds";
        _log_auth(sprintf($msg, ($time_end - $time_start)));
    }
}

// do bluegroups check, if needed

if ($group) {
    define('IIP_AUTH_GROUP', trim($group));
    // check for group cached response
    if (! in_array($group, $GLOBALS['ltcuser']['groups'])) {
        // search bluegroups
        $time_start = _microtime_float();
        $result = group_auth($GLOBALS['ltcuser']['dn'], $group);
        $time_end = _microtime_float();
        if (defined('IIP_AUTH_ERRNO') && IIP_AUTH_ERRNO == 3) {
            do_ldap_error();
        }

        // check for an auth failure and bail
        if (! $result) {
            do_auth_error();
        }

        // cache the results in session
        $GLOBALS['ltcuser']['groups'][] = $group;
        $_SESSION['ltcuser'] = $GLOBALS['ltcuser'];

        // make a log entry
        $msg = "Authorized " . $GLOBALS['ltcuser']['userid'] . " for \"";
        $msg .= $group . "\" in %01.3f seconds";
        _log_auth(sprintf($msg, ($time_end - $time_start)));
    }
}

// success auth
if(!defined('IIP_AUTH_ERRNO')){
    define('IIP_AUTH_ERRNO', 0);
}
if(!defined('IIP_LDAP_ERRNO')){
    define('IIP_LDAP_ERRNO', 0);
}

// bool result = group_auth (string user_dn, mixed group, [string depth])
// given a DN, check all $groups and return TRUE or FALSE
// $group can be either an array of groups names or string of the group to check
// set $depth to 0 to check only the top level group
function group_auth($user_dn, $group, $depth = 2)
{
    // setup ldap connection resource
    if (! is_array($group)) {
        $group = array(
            $group
        );
    }
    $basedn = "ou=memberlist,ou=ibmgroups,o=ibm.com";
    if (! $ds = _ldaps_connect())
        return FALSE;

        // check this $group and all subgroups for $dn
    $result = FALSE;
    while ($depth >= 0) {
        // filter to look for $dn in $group list
        $filter = _make_filter($group, 'cn');
        $filter = "(&(objectclass=groupofuniquenames)(uniquemember=$user_dn)$filter)";

        // connect, bind and search for $dn in $group
        if (! $sr = @ldap_search($ds, $basedn, $filter, array(
            'cn'
        ))) {
            define("IIP_LDAP_ERRNO", ldap_errno($ds));
            define("IIP_AUTH_ERRNO", 3);
            break;
        }
        // bail out if $dn is found in this $group list
        if (@ldap_count_entries($ds, $sr) > 0) {
            $result = TRUE;
            break;
        }
        // bail out if there are no sub-groups
        if (! $group = _get_subgroups($group)) {
            break;
        }
        $depth --;
    }
    if ($result == FALSE && defined('IIP_LDAP_ERRNO') == FALSE) {
        define("IIP_LDAP_ERRNO", 0);
        define("IIP_AUTH_ERRNO", 8);
    }
    return $result;
}

// array result = user_auth(userid, password)
// Verify the userid and password given are valid in Bluepages
// returns an array of user information on success or FALSE on
// failure.
function user_auth($user, $pass)
{
    global $w3php;
    $user = trim($user);
    $pass = trim($pass);
    // handle php magic quotes (evil!)
    if (get_magic_quotes_gpc()) {
        $pass = stripslashes($pass);
        $user = stripslashes($user);
    }
    $filter = "(&(mail=" . $user . ")(objectclass=ibmPerson))";

    // empty user id
    if ($user == "") {
        define("IIP_LDAP_ERRNO", 0);
        define("IIP_AUTH_ERRNO", 4);
        return FALSE;
    }

    // empty password
    if ($pass == "") {
        define("IIP_LDAP_ERRNO", 0);
        define("IIP_AUTH_ERRNO", 6);
        return FALSE;
    }

    // setup ldaps resource
    if (! $ds = _ldaps_connect())
        return FALSE;

        // connect, bind, and search for $user
    if (! $sr = ldap_search($ds, $w3php['ldap_basedn'], $filter, $w3php['ldap_attr'])) {
        define("IIP_LDAP_ERRNO", ldap_errno($ds));
        define("IIP_AUTH_ERRNO", 3);
        return FALSE;
    }

    // retrive the first entry (if any)
    if (! $entry = ldap_first_entry($ds, $sr)) {
        define("IIP_LDAP_ERRNO", 0);
        define("IIP_AUTH_ERRNO", 4);
        return FALSE;
    }

    // authenticated bind using $user_dn and $pass
    $user_dn = ldap_get_dn($ds, $entry);
    if (! ldap_bind($ds, $user_dn, $pass)) {
        define("IIP_LDAP_ERRNO", ldap_errno($ds));
        define("IIP_AUTH_ERRNO", 6);
        return FALSE;
    }

    // while we have it, return an array of info
    $user = array(
        'dn' => $user_dn
    );
    foreach ($w3php['ldap_attr'] as $a) {
        $val = ldap_get_values($ds, $entry, $a);
        $user[$a] = ($val) ? $val[0] : null;
    }

    // close and return
    return $user;
}

// #
// # error handling functions for IIP and Bluegroups
// #

/*
 * Interesting LDAP errno:
 * 48 - Inappropriate authentication (password struck out)
 * 49 - Invalid credentials (bad password)
 * 81 - Can't contact LDAP server (bind / connection failure)
 */

// returns the error for an iip errno. note that iip
// errno is not standard across different platforms
function iip_auth_err2str()
{
    if (! defined('IIP_AUTH_ERRNO'))
        return FALSE;
    $error = array(
        0 => "Success",
        1 => "Connect to host failed",
        2 => "Not registered",
        3 => "LDAP error",
        4 => "No record found",
        5 => "Multiple records found",
        6 => "Invalid credentials",
        7 => "Group does not exist",
        8 => "User not in group"
    );
    return $error[IIP_AUTH_ERRNO];
}

// returns the error for an ldap errno
function iip_ldap_err2str()
{
    if (! defined('IIP_LDAP_ERRNO'))
        return FALSE;
    return ldap_err2str(IIP_LDAP_ERRNO);
}

// handles ldap error display page
function do_ldap_error()
{
    $error_doc = $GLOBALS['w3php']['error_doc'];
    if (defined('IIP_LDAP_ERRNO')) {
        $msg = "LDAP error \"" . iip_ldap_err2str() . "\"";
        if (isset($_SERVER['PHP_AUTH_USER']) && trim($_SERVER['PHP_AUTH_USER']) != "") {
            $msg .= " for user id \"" . trim($_SERVER['PHP_AUTH_USER']) . "\"";
        }
        _log_auth($msg);
    }
    include_once ($error_doc . 'ldap.html');
    //include ('templates/footer.html'); // Include the footer.
    exit();
}

// handles authentication error display page
function do_auth_error()
{
    $error_doc = $GLOBALS['w3php']['error_doc'];

    $user = isset($_SERVER['PHP_AUTH_USER']) ? trim($_SERVER['PHP_AUTH_USER']) : "";
    $pass = isset($_SERVER['PHP_AUTH_PW']) ? trim($_SERVER['PHP_AUTH_PW']) : "";
    $group = defined('IIP_AUTH_GROUP') ? IIP_AUTH_GROUP : "unknown";

    if (IIP_AUTH_ERRNO == 7 || IIP_AUTH_ERRNO == 8) {
        _log_auth("Failed authorization for \"$user\" for group \"$group\"");
        include_once ($error_doc . '403.html');
    } else {
        // log details of failed authentication
        if ($user != $pass && $user != "") {
            $msg = "Failed authentication for \"$user\"";
            if ($pass == "") {
                _log_auth("$msg (empty password)");
            } elseif (defined('IIP_AUTH_ERRNO')) {
                _log_auth("$msg (" . iip_auth_err2str() . ")");
            } else {
                _log_auth($msg);
            }
        }
        include_once ($error_doc . '401.html');
    }
    //include ('templates/footer.html'); // Include the footer.
    exit();
}

// make an OR filter from an array of values
function _make_filter($value, $attr)
{
    $filter = "";
    foreach ($value as $v) {
        $filter .= "($attr=$v)";
    }
    if (sizeof($value) > 1) {
        $filter = "(|$filter)";
    }
    return $filter;
}

// returns an array of all uniquegroup attriubtes for all
// groups in the $group array or returns false
function _get_subgroups($group)
{
    // make a filter
    if (! is_array($group))
        $group = array(
            $group
        );
    $filter = _make_filter($group, 'cn');
    $filter = "(&(objectclass=groupofuniquenames)(uniquegroup=*)$filter)";

    // setup ldap resource
    if (! $ds = _ldaps_connect()) {
        return FALSE;
    }
    $basedn = "ou=memberlist,ou=ibmgroups,o=ibm.com";

    // do the search
    if (! $sr = @ldap_search($ds, $basedn, $filter, array(
        'uniquegroup'
    ))) {
        return FALSE;
    }

    // check if sub group are present
    if (@ldap_count_entries($ds, $sr) == 0) {
        return FALSE;
    }

    // build a new filter from the sub-groups found
    $subgroup = array();
    for ($entry = ldap_first_entry($ds, $sr); $entry != FALSE; $entry = ldap_next_entry($ds, $entry)) {

        $val = ldap_get_values($ds, $entry, 'uniquegroup');
        for ($i = 0; $i < $val['count']; $i ++) {
            list ($cn, ) = ldap_explode_dn($val[$i], 1);
            $subgroup[] = stripslashes($cn);
        }
    }
    return array_unique($subgroup);
}

// setups ldap connection resource and keeps it active
// until the script ends. the same connection is used
// for both user_auth() and group_auth()
function _ldaps_connect()
{
    global $w3php;
    // use a previously opened connection
    if (isset($GLOBALS['ibm_ldaps_ds']) && is_resource($GLOBALS['ibm_ldaps_ds'])) {
        return $GLOBALS['ibm_ldaps_ds'];
    }

    // setup the ldap resource
    $ds = ldap_connect($w3php['ldaps_host']);
    if (! $ds) {
        define("IIP_LDAP_ERRNO", 0);
        define("IIP_AUTH_ERRNO", 1);

        var_dump($ds);

        return FALSE;
    }

    // use ldap protocol v3
    if (! ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3)) {
        define("IIP_LDAP_ERRNO", ldap_errno($ds));
        define("IIP_AUTH_ERRNO", 3);
        return FALSE;
    }

    $GLOBALS['ibm_ldaps_ds'] = $ds;
    return $ds;
}

// log to syslog
// change log to a file. Ensure Web-server userid has access to that file !!
// error_log(date('Ymd H:i:s').' '.$msg."\n",3,'<log file>.log');
function _log_auth($msg = "")
{
    if (! isset($GLOBALS['w3php']['log_auth']))
        return;
    if ($GLOBALS['w3php']['log_auth'] == FALSE)
        return;
    if ($msg == "")
        return;
    openlog('w3php', FALSE, LOG_USER);
    syslog(LOG_INFO, $msg);
    return;
}
?>