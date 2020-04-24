<?php

use vbac\personRecord;

if( getenv( "VCAP_SERVICES" ) )
{
    # Get database details from the VCAP_SERVICES environment variable
    #
    # *This can only work if you have used the Bluemix dashboard to
    # create a connection from your dashDB service to your PHP App.
    #
    $details  = json_decode( getenv( "VCAP_SERVICES" ), true );
    $dsn      = $details [ "dashDB For Transactions" ][0][ "credentials" ][ "dsn" ];
    $ssl_dsn  = $details [ "dashDB For Transactions" ][0][ "credentials" ][ "ssldsn" ];

    # Build the connection string
    #
    $driver = "DRIVER={IBM DB2 ODBC DRIVER};";
    $conn_string = $driver . $dsn;     # Non-SSL
    $conn_string = $driver . $ssl_dsn; # SSL

    $conn_string = str_replace('poCXUiBIC0Pl','poCXUiBIC0Pl!ab',$conn_string);

    //echo $conn_string;


    die('here');

    $conn = db2_connect( $conn_string, "", "" );

//   $_SESSION['ssoEmail'] = 'dummyUser';
    if( $conn )
    {
        $_SESSION['conn'] = $conn;
        db2_autocommit($conn, TRUE); // This is how it was on the Wintel Box - so the code has no/few commit points.
    }
    else
    {
        echo "<p>Connection failed.</p>";
        echo db2_conn_error();
        echo db2_conn_errormsg();
    }
}
else
{
    echo "<p>No credentials.</p>";
}




?>
