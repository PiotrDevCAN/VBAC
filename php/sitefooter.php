<?php
if ($GLOBALS['header_done']) {do_footer();}
// include ('itdq/java/scripts.html');
// include ('vbac/java/scripts.html');
include ('php/templates/interior.footer');

// if ($GLOBALS['conn']) {
//     $conn = $GLOBALS['conn'];
//     $rc = sqlsrv_close($conn);
//     if ($rc) {
//         echo "Connection was successfully closed.";
//     }
// }